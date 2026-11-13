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
  <title>Admin sign in · {{ site('brand_name') }}</title>
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
<body class="admin-login-body">
  <div class="admin-login">
    <section class="admin-login__visual">
      <div class="admin-login__visual-inner">
        <a class="admin-login__brand" href="{{ route('home') }}">
          <img src="{{ Media::url($loginLogo) }}" alt="{{ site('brand_logo_alt') }}" width="{{ $loginLogoDims[0] }}" height="{{ $loginLogoDims[1] }}">
        </a>
        <p class="admin-login__eyebrow">Behind the counter</p>
        <h1>Run the room,<br><span>not the website.</span></h1>
        <p class="admin-login__lede">Update the menu, upload photographs, swap the hero and keep every detail of {{ site('brand_name') }} current — all from one place.</p>
        <div class="admin-login__note">
          <i></i>
          <span>{{ site('contact_hours') }} · {{ site('contact_address_full') }}</span>
        </div>
      </div>
    </section>

    <section class="admin-login__panel">
      <div class="admin-login__form-wrap">
        <a class="admin-login__back" href="{{ route('home') }}">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 12H5m6-6-6 6 6 6"/></svg>
          Back to site
        </a>

        <p class="admin-login__kicker">Admin access</p>
        <h2>Welcome back.</h2>
        <p class="admin-login__sub">Sign in to manage the public site.</p>

        @if (session('status'))
          <div class="admin-alert is-success">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.attempt') }}" class="admin-login__form">
          @csrf
          <label class="admin-field">
            <span class="admin-field__label">Email address</span>
            <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
            @error('email')
              <span class="admin-field__error">{{ $message }}</span>
            @enderror
          </label>

          <label class="admin-field">
            <span class="admin-field__label">Password</span>
            <input type="password" name="password" autocomplete="current-password" required>
            @error('password')
              <span class="admin-field__error">{{ $message }}</span>
            @enderror
          </label>

          <label class="admin-check">
            <input type="checkbox" name="remember" value="1">
            <span>Keep me signed in</span>
          </label>

          <button type="submit" class="admin-btn admin-btn--primary admin-btn--wide">
            Sign in
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
          </button>
        </form>

        <p class="admin-login__fine">Admin access only. Public accounts are not available.</p>
      </div>
    </section>
  </div>
</body>
</html>
