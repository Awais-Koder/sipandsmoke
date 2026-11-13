@php
    use App\Support\Media;

    $pageTitle = $title ?? site('seo_home_title');
    $pageDescription = $description ?? site('seo_home_description');
    $pageCanonical = $canonical ?? url()->current();
    $socialImage = Media::url($ogImage ?? site('seo_og_image'));
    $logo = site('brand_logo');
    $logoDims = Media::dimensions($logo, 700, 829);
    $navItems = [
        ['label' => site('nav_home'), 'url' => route('home'), 'section' => 'home', 'key' => 'home'],
        ['label' => site('nav_menu'), 'url' => route('menu'), 'section' => null, 'key' => 'menu'],
        ['label' => site('nav_juice'), 'url' => route('home').'#juice-bar', 'section' => 'juice-bar', 'key' => 'juice'],
        ['label' => site('nav_about'), 'url' => route('home').'#about-us', 'section' => 'about-us', 'key' => 'about'],
        ['label' => site('nav_contact'), 'url' => route('home').'#contact-us', 'section' => 'contact-us', 'key' => 'contact'],
        ['label' => site('nav_gallery'), 'url' => route('gallery'), 'section' => null, 'key' => 'gallery'],
    ];
@endphp
<!DOCTYPE html>
<html lang="en-AU">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $pageTitle }}</title>
  <meta name="description" content="{{ $pageDescription }}">
  <meta name="theme-color" content="{{ site('brand_ice') }}">
  <link rel="canonical" href="{{ $pageCanonical }}">
  <link rel="icon" href="{{ Media::url(site('brand_favicon')) }}">
  @if (! empty($preloadImage))
    <link rel="preload" href="{{ Media::url($preloadImage) }}" as="image" fetchpriority="high">
  @endif

  <meta property="og:type" content="website">
  <meta property="og:title" content="{{ $pageTitle }}">
  <meta property="og:description" content="{{ $pageDescription }}">
  <meta property="og:url" content="{{ $pageCanonical }}">
  @if ($socialImage)
    <meta property="og:image" content="{{ $socialImage }}">
  @endif

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400..600&amp;family=Plus+Jakarta+Sans:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet">

  <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}?v={{ filemtime(public_path('assets/css/theme.css')) }}">
  <link rel="stylesheet" href="{{ asset('assets/css/site.css') }}?v={{ filemtime(public_path('assets/css/site.css')) }}">
  <style>
    :root {
      --brand-orange: {{ hex_rgb(site('brand_orange'), '255 130 0') }};
      --brand-blue: {{ hex_rgb(site('brand_blue'), '44 86 151') }};
      --brand-ice: {{ hex_rgb(site('brand_ice'), '234 238 245') }};
    }
  </style>
  @stack('head')

  @php
    $restaurantSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Restaurant',
        'name' => site('brand_name'),
        'servesCuisine' => ['Juice bar', 'Desserts', 'Shakes'],
        'telephone' => site('contact_phone_href'),
        'email' => site('contact_email'),
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => site('contact_address_1'),
            'addressLocality' => 'Auburn',
            'addressRegion' => 'NSW',
            'postalCode' => '2144',
            'addressCountry' => 'AU',
        ],
        'hasMenu' => route('menu'),
    ];
  @endphp
  <script type="application/ld+json">{!! json_encode($restaurantSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
</head>
<body class="@yield('body_class')">
  <a class="skip-link" href="#main">Skip to content</a>

  <svg aria-hidden="true" focusable="false" style="position:absolute;width:0;height:0;overflow:hidden">
    <symbol id="icon-arrow-up-right" viewBox="0 0 24 24">
      <path d="M7 17 17 7M8 7h9v9"/>
    </symbol>
    <symbol id="icon-arrow-right" viewBox="0 0 24 24">
      <path d="M4 12h15m-6-6 6 6-6 6"/>
    </symbol>
    <symbol id="icon-phone" viewBox="0 0 24 24">
      <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.08 4.18 2 2 0 0 1 4.06 2h3a2 2 0 0 1 2 1.72c.12.9.34 1.78.66 2.62a2 2 0 0 1-.45 2.11L8 9.72a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.84.32 1.72.54 2.62.66A2 2 0 0 1 22 16.92z"/>
    </symbol>
    <symbol id="icon-close" viewBox="0 0 24 24">
      <path d="m6 6 12 12M18 6 6 18"/>
    </symbol>
  </svg>

  <header class="site-header">
    <div class="shell header-inner">
      <a class="brand" href="{{ route('home') }}" aria-label="{{ site('brand_name') }} home">
        <img class="brand-logo" src="{{ Media::url($logo) }}" alt="{{ site('brand_logo_alt') }}" width="{{ $logoDims[0] }}" height="{{ $logoDims[1] }}">
      </a>

      <nav class="primary-nav" aria-label="Primary navigation">
        @foreach ($navItems as $item)
          <a class="nav-link" href="{{ $item['url'] }}"
             @if ($item['section'] && ($current ?? '') === 'home') data-nav-section="{{ $item['section'] }}" @endif
             @if (($current ?? '') === $item['key']) aria-current="page" @endif>{{ $item['label'] }}</a>
        @endforeach
      </nav>

      <div class="header-actions">
        <a class="btn btn--primary header-cta" href="{{ site('contact_phone_href') }}">
          <svg class="icon" aria-hidden="true"><use href="#icon-phone"></use></svg>
          <span>{{ site('contact_cta') }}</span>
        </a>
        <button class="nav-toggle" type="button" aria-label="Open navigation" aria-controls="mobile-nav" aria-expanded="false" data-nav-toggle>
          <span class="nav-toggle__bars" aria-hidden="true"><span></span><span></span></span>
        </button>
      </div>
    </div>
  </header>

  <main id="main">
    @yield('content')
  </main>

  @include('site.partials.footer', ['logo' => $logo, 'logoDims' => $logoDims])
  @include('site.partials.mobile-nav', ['logo' => $logo, 'logoDims' => $logoDims, 'navItems' => $navItems])

  @stack('dialogs')
  @stack('scripts')
</body>
</html>
