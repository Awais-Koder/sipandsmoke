<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class TwoFactorAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_without_two_factor_is_prompted_to_enrol_and_can_confirm(): void
    {
        $user = User::factory()->create([
            'is_admin' => true,
            'password' => Hash::make('secret-password'),
        ]);

        $this->post(route('admin.login.attempt'), [
            'email' => $user->email,
            'password' => 'secret-password',
        ])->assertRedirect(route('admin.two-factor.show'));

        $this->assertGuest();

        $this->get(route('admin.two-factor.show'))
            ->assertOk()
            ->assertSee('Scan to secure.')
            ->assertSee('data-2fa-key', false);

        $secret = Crypt::decryptString(session('admin_2fa.setup_secret'));
        $code = (new Google2FA)->getCurrentOtp($secret);

        $this->post(route('admin.two-factor.verify'), ['code' => $code])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);

        $user->refresh();

        $this->assertSame($secret, $user->two_factor_secret);
        $this->assertNotNull($user->two_factor_confirmed_at);
    }

    public function test_admin_with_two_factor_must_enter_a_valid_code(): void
    {
        $google2fa = new Google2FA;
        $secret = $google2fa->generateSecretKey();

        $user = User::factory()->create([
            'is_admin' => true,
            'password' => Hash::make('secret-password'),
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ]);

        $this->post(route('admin.login.attempt'), [
            'email' => $user->email,
            'password' => 'secret-password',
        ])->assertRedirect(route('admin.two-factor.show'));

        $this->get(route('admin.two-factor.show'))
            ->assertOk()
            ->assertSee('Enter your code.');

        $this->post(route('admin.two-factor.verify'), ['code' => '000000'])
            ->assertSessionHasErrors('code');

        $this->assertGuest();

        $this->post(route('admin.two-factor.verify'), [
            'code' => $google2fa->getCurrentOtp($secret),
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_an_expired_pending_sign_in_returns_to_the_login_screen(): void
    {
        $user = User::factory()->create([
            'is_admin' => true,
            'password' => Hash::make('secret-password'),
        ]);

        $this->post(route('admin.login.attempt'), [
            'email' => $user->email,
            'password' => 'secret-password',
        ]);

        $this->withSession([
            'admin_2fa.expires_at' => now()->subMinute()->getTimestamp(),
        ]);

        $this->get(route('admin.two-factor.show'))
            ->assertRedirect(route('admin.login'))
            ->assertSessionHas('status');
    }
}
