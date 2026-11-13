@extends('layouts.admin', ['title' => 'Account'])

@section('content')
  <div class="admin-page-head">
    <div>
      <p class="admin-eyebrow">Your access</p>
      <h1 class="admin-page-title">Admin <span class="admin-mark">account.</span></h1>
      <p class="admin-page-sub">Change the email address and password used to sign in to this admin panel. Your current password is required to save any change.</p>
    </div>
  </div>

  @if (session('status'))
    <div class="admin-alert is-success" role="status">{{ session('status') }}</div>
  @endif

  @if ($errors->any())
    <div class="admin-alert is-error" role="alert">Please check the fields below and try again.</div>
  @endif

  <section class="admin-panel admin-account-panel">
    <div class="admin-panel__head">
      <div>
        <p class="admin-eyebrow">Sign-in details</p>
        <h2>Email and password</h2>
      </div>
      <span class="admin-badge {{ $user->hasTwoFactorAuthentication() ? 'is-live' : 'is-category' }}">{{ $user->hasTwoFactorAuthentication() ? '2FA protected' : '2FA not set up' }}</span>
    </div>

    <form method="POST" action="{{ route('admin.account.update') }}" class="admin-account-form">
      @csrf
      @method('PUT')

      <div class="admin-form-grid">
        <div class="admin-field admin-field--full">
          <label class="admin-field__label" for="account-email">Email address</label>
          <input
            id="account-email"
            name="email"
            type="email"
            value="{{ old('email', $user->email) }}"
            autocomplete="email"
            required
          >
          <span class="admin-field__hint">This is the address you use on the admin sign-in screen.</span>
          @error('email') <span class="admin-field__error">{{ $message }}</span> @enderror
        </div>

        <div class="admin-field admin-field--full">
          <label class="admin-field__label" for="account-current-password">Current password</label>
          <input
            id="account-current-password"
            name="current_password"
            type="password"
            autocomplete="current-password"
            required
          >
          <span class="admin-field__hint">Required to confirm it is really you.</span>
          @error('current_password') <span class="admin-field__error">{{ $message }}</span> @enderror
        </div>

        <div class="admin-field">
          <label class="admin-field__label" for="account-password">New password</label>
          <input
            id="account-password"
            name="password"
            type="password"
            autocomplete="new-password"
            minlength="8"
          >
          <span class="admin-field__hint">Leave blank to keep the current password. Use at least 8 characters.</span>
          @error('password') <span class="admin-field__error">{{ $message }}</span> @enderror
        </div>

        <div class="admin-field">
          <label class="admin-field__label" for="account-password-confirmation">Confirm new password</label>
          <input
            id="account-password-confirmation"
            name="password_confirmation"
            type="password"
            autocomplete="new-password"
            minlength="8"
          >
        </div>
      </div>

      <div class="admin-account-actions">
        <p>Two-step verification is not changed here. It stays linked to this account.</p>
        <button type="submit" class="admin-btn admin-btn--primary">
          Save account changes
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg>
        </button>
      </div>
    </form>
  </section>
@endsection
