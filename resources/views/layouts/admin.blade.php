@php
    use App\Support\Media;

    $adminLogo = site('brand_logo');
    $adminLogoDims = Media::dimensions($adminLogo, 700, 829);
    $nav = [
        ['route' => 'admin.dashboard', 'label' => 'Overview', 'icon' => 'grid', 'pattern' => 'admin.dashboard'],
        ['route' => 'admin.content', 'label' => 'Site content', 'icon' => 'edit', 'pattern' => 'admin.content'],
        ['route' => 'admin.menu', 'label' => 'Menu pages', 'icon' => 'book', 'pattern' => 'admin.menu'],
        ['route' => 'admin.gallery', 'label' => 'Gallery', 'icon' => 'image', 'pattern' => 'admin.gallery'],
        ['route' => 'admin.products', 'label' => 'Juice bar', 'icon' => 'cup', 'pattern' => 'admin.products'],
        ['route' => 'admin.account', 'label' => 'Account', 'icon' => 'user', 'pattern' => 'admin.account'],
    ];
@endphp
<!DOCTYPE html>
<html lang="en-AU">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex,nofollow">
  <title>{{ $title ?? 'Admin' }} · {{ site('brand_name') }}</title>
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
  @livewireStyles
</head>
<body class="admin-body">
  <div class="admin-shell" x-data="{ navOpen: false }">
    <aside class="admin-sidebar" :class="{ 'is-open': navOpen }">
      <div class="admin-sidebar__brand">
        <a href="{{ route('admin.dashboard') }}" class="admin-brand" aria-label="{{ site('brand_name') }} admin">
          <span class="admin-brand__mark"><img src="{{ Media::url($adminLogo) }}" alt="" width="{{ $adminLogoDims[0] }}" height="{{ $adminLogoDims[1] }}"></span>
          <span class="admin-brand__copy">
            <strong>{{ site('brand_name') }}</strong>
            <small>Control room</small>
          </span>
        </a>
        <button class="admin-sidebar__close" type="button" @click="navOpen = false" aria-label="Close navigation">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>
        </button>
      </div>

      <nav class="admin-nav" aria-label="Admin navigation">
        @foreach ($nav as $item)
          <a href="{{ route($item['route']) }}" class="admin-nav__link {{ request()->routeIs($item['pattern']) ? 'is-active' : '' }}">
            <span class="admin-nav__icon">
              @switch($item['icon'])
                @case('grid')
                  <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                  @break
                @case('book')
                  <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v17H6.5A2.5 2.5 0 0 1 4 17.5z"/><path d="M4 17.5A2.5 2.5 0 0 1 6.5 15H20"/></svg>
                  @break
                @case('image')
                  <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="8.5" cy="9" r="1.5"/><path d="m4 17 5-5 4 4 3-3 4 4"/></svg>
                  @break
                @case('cup')
                  <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 8h12v8a5 5 0 0 1-5 5h-2a5 5 0 0 1-5-5z"/><path d="M17 10h2a3 3 0 0 1 0 6h-2M4 3v2M8 3v2M12 3v2"/></svg>
                  @break
                @case('user')
                  <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="3.5"/><path d="M5 20a7 7 0 0 1 14 0"/></svg>
                  @break
                @default
                  <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m4 20 4.5-1 10-10a2.1 2.1 0 0 0-3-3l-10 10z"/><path d="m13.5 7.5 3 3"/></svg>
              @endswitch
            </span>
            <span>{{ $item['label'] }}</span>
          </a>
        @endforeach
      </nav>

      <div class="admin-sidebar__foot">
        <a class="admin-view-site" href="{{ route('home') }}" target="_blank" rel="noopener">
          View site
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7M8 7h9v9"/></svg>
        </a>
        <div class="admin-user">
          <span class="admin-user__avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
          <span class="admin-user__copy">
            <strong>{{ auth()->user()->name }}</strong>
            <small>{{ auth()->user()->email }}</small>
          </span>
          <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="admin-logout" title="Sign out" aria-label="Sign out">
              <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 17l5-5-5-5M15 12H3M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/></svg>
            </button>
          </form>
        </div>
      </div>
    </aside>

    <div class="admin-backdrop" x-cloak x-show="navOpen" x-transition.opacity @click="navOpen = false"></div>

    <div class="admin-main">
      <header class="admin-topbar">
        <button class="admin-menu-toggle" type="button" @click="navOpen = true" aria-label="Open navigation">
          <span></span><span></span>
        </button>
        <div class="admin-topbar__crumb">
          <span>Admin</span>
          <i>/</i>
          <strong>{{ $title ?? 'Control room' }}</strong>
        </div>
        <div class="admin-topbar__actions">
          <span class="admin-status"><i></i> Live site</span>
          <a class="admin-topbar__link" href="{{ route('home') }}" target="_blank" rel="noopener">
            Open frontend
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7M8 7h9v9"/></svg>
          </a>
        </div>
      </header>

      <main class="admin-content">
        @hasSection('content')
          @yield('content')
        @else
          {{ $slot ?? '' }}
        @endif
      </main>

      <footer class="admin-footer">
        <span>{{ site('brand_name') }} · admin</span>
        <span>Changes publish instantly</span>
      </footer>
    </div>
  </div>

  <div class="toast-stack"
       x-data="{
         toasts: [],
         push(message, type = 'success') {
           const id = Date.now() + Math.random();
           this.toasts.push({ id, message, type });
           setTimeout(() => this.toasts = this.toasts.filter(t => t.id !== id), 4200);
         }
       }"
       @toast.window="push($event.detail.message, $event.detail.type || 'success')">
    <template x-for="toast in toasts" :key="toast.id">
      <div class="admin-toast" :class="'is-' + toast.type" x-transition>
        <span class="admin-toast__icon">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg>
        </span>
        <span x-text="toast.message"></span>
      </div>
    </template>
  </div>

  @if (session('status'))
    <div class="toast-stack" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4200)">
      <div class="admin-toast is-success">
        <span class="admin-toast__icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg></span>
        <span>{{ session('status') }}</span>
      </div>
    </div>
  @endif

  @livewireScripts
</body>
</html>
