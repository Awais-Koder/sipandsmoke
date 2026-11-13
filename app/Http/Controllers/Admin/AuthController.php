<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class AuthController extends Controller
{
    private const PENDING_USER = 'admin_2fa.user_id';

    private const PENDING_REMEMBER = 'admin_2fa.remember';

    private const PENDING_EXPIRES = 'admin_2fa.expires_at';

    private const PENDING_SECRET = 'admin_2fa.setup_secret';

    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $key = Str::transliterate($credentials['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Too many attempts. Try again in '.RateLimiter::availableIn($key).' seconds.',
            ]);
        }

        if (! Auth::validate($credentials)) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'email' => 'Those details do not match an admin account.',
            ]);
        }

        $user = User::query()->where('email', $credentials['email'])->first();

        if (! $user || ! $user->is_admin) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'email' => 'This account does not have admin access.',
            ]);
        }

        RateLimiter::clear($key);

        // Password accepted, but do not authenticate yet. The user still has to
        // pass the authenticator challenge (or enrol one on first sign-in).
        Auth::logout();
        $request->session()->regenerate();
        $request->session()->put([
            self::PENDING_USER => $user->getKey(),
            self::PENDING_REMEMBER => $request->boolean('remember'),
            self::PENDING_EXPIRES => now()->addMinutes(10)->getTimestamp(),
        ]);

        return redirect()->route('admin.two-factor.show');
    }

    public function showTwoFactor(Request $request, Google2FA $google2fa): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        $user = $this->pendingUser($request);

        if (! $user) {
            return $this->expiredSignIn($request);
        }

        $setup = ! $user->hasTwoFactorAuthentication();
        $secret = null;
        $qrSvg = null;

        if ($setup) {
            $secret = $this->pendingSecret($request) ?: $google2fa->generateSecretKey();

            if ($request->session()->get(self::PENDING_SECRET) === null) {
                $request->session()->put(self::PENDING_SECRET, Crypt::encryptString($secret));
            }

            $qrSvg = $this->qrCodeSvg(
                $google2fa->getQRCodeUrl(config('app.name'), $user->email, $secret)
            );
        }

        return view('admin.two-factor', [
            'email' => $user->email,
            'setup' => $setup,
            'manualKey' => $secret ? trim(chunk_split($secret, 4, ' ')) : null,
            'qrSvg' => $qrSvg,
        ]);
    }

    public function verifyTwoFactor(Request $request, Google2FA $google2fa): RedirectResponse
    {
        $user = $this->pendingUser($request);

        if (! $user) {
            return $this->expiredSignIn($request);
        }

        $request->validate([
            'code' => ['required', 'string', 'regex:/^\d{6}$/'],
        ], [
            'code.regex' => 'Enter the 6-digit code from your authenticator app.',
        ]);

        $key = 'admin-2fa|'.$user->getKey().'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 6)) {
            throw ValidationException::withMessages([
                'code' => 'Too many attempts. Try again in '.RateLimiter::availableIn($key).' seconds.',
            ]);
        }

        $setup = ! $user->hasTwoFactorAuthentication();
        $secret = $user->two_factor_secret;

        if ($setup) {
            $secret = $this->pendingSecret($request);
        }

        if (! $secret || ! $google2fa->verifyKey($secret, (string) $request->input('code'), 1)) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'code' => 'That code did not match. Check your authenticator app and try again.',
            ]);
        }

        if ($setup) {
            $user->forceFill([
                'two_factor_secret' => $secret,
                'two_factor_confirmed_at' => now(),
            ])->save();
        }

        RateLimiter::clear($key);

        $remember = (bool) $request->session()->get(self::PENDING_REMEMBER, false);

        Auth::login($user, $remember);
        $request->session()->forget([
            self::PENDING_USER,
            self::PENDING_REMEMBER,
            self::PENDING_EXPIRES,
            self::PENDING_SECRET,
        ]);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function cancelTwoFactor(Request $request): RedirectResponse
    {
        if (! Auth::check()) {
            $request->session()->forget([
                self::PENDING_USER,
                self::PENDING_REMEMBER,
                self::PENDING_EXPIRES,
                self::PENDING_SECRET,
            ]);
            $request->session()->regenerateToken();
        }

        return redirect()->route('admin.login');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('status', 'You have been signed out.');
    }

    private function pendingUser(Request $request): ?User
    {
        $userId = $request->session()->get(self::PENDING_USER);
        $expires = (int) $request->session()->get(self::PENDING_EXPIRES, 0);

        if (! $userId || $expires < now()->getTimestamp()) {
            $this->clearPending($request);

            return null;
        }

        $user = User::query()->find($userId);

        if (! $user || ! $user->is_admin) {
            $this->clearPending($request);

            return null;
        }

        return $user;
    }

    private function pendingSecret(Request $request): ?string
    {
        $encrypted = $request->session()->get(self::PENDING_SECRET);

        if (! is_string($encrypted) || $encrypted === '') {
            return null;
        }

        try {
            return Crypt::decryptString($encrypted);
        } catch (DecryptException) {
            $request->session()->forget(self::PENDING_SECRET);

            return null;
        }
    }

    private function clearPending(Request $request): void
    {
        $request->session()->forget([
            self::PENDING_USER,
            self::PENDING_REMEMBER,
            self::PENDING_EXPIRES,
            self::PENDING_SECRET,
        ]);
    }

    private function expiredSignIn(Request $request): RedirectResponse
    {
        $this->clearPending($request);

        return redirect()
            ->route('admin.login')
            ->with('status', 'Your sign-in session expired. Please enter your details again.');
    }

    private function qrCodeSvg(string $provisioningUri): string
    {
        $writer = new Writer(
            new ImageRenderer(
                new RendererStyle(320, 2),
                new SvgImageBackEnd
            )
        );

        return $writer->writeString($provisioningUri);
    }
}
