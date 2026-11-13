@php
    use App\Support\Media;

    $loginLogo = site('brand_logo');
    $loginLogoDims = Media::dimensions($loginLogo, 700, 829);
@endphp
<!DOCTYPE html>
<html lang="en-AU">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex,nofollow">
  <title>{{ $setup ? 'Set up two-step verification' : 'Two-step verification' }} · {{ site('brand_name') }}</title>
  <link rel="icon" href="{{ Media::url(site('brand_favicon')) }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400..600&amp;family=Plus+Jakarta+Sans:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}?v={{ filemtime(public_path('assets/css/theme.css')) }}">
  <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}?v={{ filemtime(public_path('assets/css/admin.css')) }}">
  <style>
    :root {
      --brand-orange: {{ hex_rgb(site('brand_orange'), '255 130 0') }};
      --brand-blue: {{ hex_rgb(site('brand_blue'), '44 86 151') }};
      --brand-ice: {{ hex_rgb(site('brand_ice'), '234 238 245') }};
    }
  </style>
</head>
<body class="admin-login-body admin-2fa-body">
  <div class="admin-login">
    <section class="admin-login__visual">
      <div class="admin-login__visual-inner">
        <a class="admin-login__brand" href="{{ route('home') }}">
          <img src="{{ Media::url($loginLogo) }}" alt="{{ site('brand_logo_alt') }}" width="{{ $loginLogoDims[0] }}" height="{{ $loginLogoDims[1] }}">
        </a>
        <p class="admin-login__eyebrow">{{ $setup ? 'First sign-in · Extra security' : 'Two-step verification' }}</p>
        <h1>One more step,<br><span>then you're in.</span></h1>
        <p class="admin-login__lede">
          @if ($setup)
            Link an authenticator app to this admin account. From the next sign-in, the app will generate a fresh 6-digit code every 30 seconds.
          @else
            Enter the current 6-digit code from your authenticator app. It refreshes every 30 seconds, so use the newest one.
          @endif
        </p>
        <div class="admin-login__note">
          <i></i>
          <span>{{ site('contact_hours') }} · {{ site('contact_address_full') }}</span>
        </div>
      </div>
    </section>

    <section class="admin-login__panel">
      <div class="admin-login__form-wrap admin-2fa__form-wrap">
        <form method="POST" action="{{ route('admin.two-factor.cancel') }}" class="admin-2fa__back-form">
          @csrf
          <button class="admin-login__back" type="submit">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 12H5m6-6-6 6 6 6"/></svg>
            Sign in with a different account
          </button>
        </form>

        <p class="admin-login__kicker">{{ $setup ? 'Secure this account' : 'Admin access' }}</p>
        <h2>{{ $setup ? 'Scan to secure.' : 'Enter your code.' }}</h2>
        <p class="admin-login__sub">
          @if ($setup)
            We'll confirm the link with one code before letting you in.
          @else
            Signing in as <strong>{{ $email }}</strong>.
          @endif
        </p>

        @if (session('status'))
          <div class="admin-alert is-success">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
          <div class="admin-alert is-error" role="alert">{{ $errors->first() }}</div>
        @endif

        @if ($setup)
          <div class="admin-2fa__setup">
            <div class="admin-2fa__qr" aria-label="Authenticator QR code">
              {!! $qrSvg !!}
            </div>

            <ol class="admin-2fa__steps">
              <li>Open your authenticator app.</li>
              <li>Scan the QR code, or enter the setup key below.</li>
              <li>Enter the 6-digit code it shows to finish.</li>
            </ol>

            <div class="admin-2fa__manual">
              <span>Setup key</span>
              <code data-2fa-key>{{ $manualKey }}</code>
              <button type="button" class="admin-2fa__copy" data-2fa-copy>Copy</button>
            </div>
          </div>
        @else
          <div class="admin-2fa__badge" aria-hidden="true">
            <svg viewBox="0 0 24 24"><rect x="6" y="2.5" width="12" height="19" rx="2.5"/><path d="M10 18.5h4"/></svg>
            <span>Authenticator code</span>
          </div>
        @endif

        <form method="POST" action="{{ route('admin.two-factor.verify') }}" class="admin-login__form admin-2fa__form" id="two-factor-form" novalidate>
          @csrf

          <div class="admin-otp" data-otp>
            <input
              class="admin-otp__input"
              id="code"
              name="code"
              type="text"
              inputmode="numeric"
              pattern="[0-9]*"
              maxlength="6"
              autocomplete="one-time-code"
              value="{{ old('code') }}"
              aria-label="6-digit authentication code"
              required
              autofocus
            >
            <div class="admin-otp__cells" aria-hidden="true">
              @for ($i = 0; $i < 6; $i++)
                <span class="admin-otp__cell" data-otp-cell>
                  <span class="admin-otp__digit" data-otp-digit></span>
                </span>
              @endfor
            </div>
          </div>

          <p class="admin-otp__hint">
            <span class="admin-otp__pulse" aria-hidden="true"></span>
            Auto-submits when all six digits are entered.
          </p>

          <button type="submit" class="admin-btn admin-btn--primary admin-btn--wide" data-2fa-submit>
            {{ $setup ? 'Confirm and sign in' : 'Verify and sign in' }}
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
          </button>
        </form>

        <p class="admin-login__fine">
          @if ($setup)
            Keep this device and your authenticator app safe. If you lose access, an account owner will need to reset two-step verification on the server.
          @else
            Codes are time-based and never stored on this site. If you lose your authenticator, contact the site owner.
          @endif
        </p>
      </div>
    </section>
  </div>

  <script>
    (() => {
      const otp = document.querySelector('[data-otp]');
      if (!otp) return;

      const input = otp.querySelector('.admin-otp__input');
      const cells = Array.from(otp.querySelectorAll('[data-otp-cell]'));
      const form = document.getElementById('two-factor-form');
      const submit = document.querySelector('[data-2fa-submit]');
      let autoSubmitted = false;
      let submitting = false;

      const render = () => {
        const digits = input.value.replace(/\D/g, '').slice(0, 6);

        if (input.value !== digits) {
          input.value = digits;
        }

        cells.forEach((cell, index) => {
          const digit = cell.querySelector('[data-otp-digit]');
          const value = digits[index] || '';

          if (digit.textContent !== value) {
            digit.textContent = value;

            if (value) {
              digit.classList.remove('is-pop');
              void digit.offsetWidth;
              digit.classList.add('is-pop');
            }
          }

          cell.classList.toggle('is-filled', Boolean(value));
          cell.classList.toggle('is-active', index === digits.length && digits.length < 6 && document.activeElement === input);
        });
      };

      input.addEventListener('input', () => {
        render();

        if (input.value.length === 6 && !autoSubmitted) {
          autoSubmitted = true;
          window.setTimeout(() => {
            if (!submitting) form.requestSubmit(submit);
          }, 320);
        } else if (input.value.length < 6) {
          autoSubmitted = false;
        }
      });

      ['focus', 'blur', 'click'].forEach((eventName) => {
        input.addEventListener(eventName, render);
      });

      input.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && input.value.length === 6) {
          event.preventDefault();
          form.requestSubmit(submit);
        }
      });

      form.addEventListener('submit', () => {
        submitting = true;
        submit.disabled = true;
        submit.classList.add('is-loading');
      });

      const copyButton = document.querySelector('[data-2fa-copy]');
      const key = document.querySelector('[data-2fa-key]');

      if (copyButton && key) {
        copyButton.addEventListener('click', async () => {
          try {
            await navigator.clipboard.writeText(key.textContent.trim());
            copyButton.textContent = 'Copied';
            window.setTimeout(() => { copyButton.textContent = 'Copy'; }, 1600);
          } catch (error) {
            copyButton.textContent = 'Select key';
          }
        });
      }

      render();
      input.focus();
    })();
  </script>
</body>
</html>
