@extends('layouts.site')

@php
    use App\Support\Media;

    $heroImage = site('home_hero_image');
    [$heroWidth, $heroHeight] = Media::dimensions($heroImage, 1672, 941);
    $ribbon = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) site('home_ribbon')))));
    $menuIndex = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) site('home_menu_index')))));
@endphp

@section('content')
  <!-- Hero -->
  <section class="hero" id="home" aria-labelledby="hero-title">
    <img class="hero-bg" src="{{ Media::url($heroImage) }}" alt="{{ site('home_hero_alt') }}" width="{{ $heroWidth }}" height="{{ $heroHeight }}" fetchpriority="high" decoding="async">
    <div class="shell hero-inner">
      <div class="hero-copy">
        <p class="hero-eyebrow" data-hero-item>{{ site('home_hero_eyebrow') }}</p>

        <h1 class="hero-title" id="hero-title">
          <span class="hero-title__line"><span>{{ site('home_hero_title_1') }}</span></span>
          <span class="hero-title__line"><span class="hero-title__accent">{{ site('home_hero_title_2') }}</span></span>
        </h1>

        <p class="hero-lede" data-hero-item>{{ site('home_hero_lede') }}</p>

        <div class="hero-actions" data-hero-item>
          <a class="btn btn--primary" href="{{ site('contact_phone_href') }}">
            <svg class="icon" aria-hidden="true"><use href="#icon-phone"></use></svg>
            <span>{{ site('home_hero_cta') }}</span>
          </a>
          <a class="btn btn--ghost" href="{{ route('menu') }}">
            <span>{{ site('home_hero_secondary') }}</span>
            <svg class="icon" aria-hidden="true"><use href="#icon-arrow-right"></use></svg>
          </a>
        </div>

        <p class="hero-meta" data-hero-item>
          <span><i class="dot" aria-hidden="true"></i>{{ site('home_hero_meta_open') }}</span>
          <span>{{ site('home_hero_meta_address') }}</span>
        </p>
      </div>
    </div>
  </section>

  <!-- Brand ribbon -->
  <div class="brand-ribbon" aria-hidden="true">
    <div class="brand-ribbon__track">
      @for ($group = 0; $group < 2; $group++)
        <div class="brand-ribbon__group">
          @foreach ($ribbon as $item)
            <span class="brand-ribbon__item">{{ $item }} <i>✦</i></span>
          @endforeach
        </div>
      @endfor
    </div>
  </div>

  <!-- Menu -->
  <section class="section menu-section" id="the-menu" aria-labelledby="menu-heading">
    <div class="shell">
      <div class="section-head section-head--split">
        <div>
          <p class="eyebrow" data-reveal>{{ site('home_menu_eyebrow') }}</p>
          <h2 class="section-title" id="menu-heading" data-reveal>{{ site('home_menu_title_lead') }} <span class="mark">{{ site('home_menu_title_mark') }}</span></h2>
        </div>
        <p class="section-note" data-reveal>{{ site('home_menu_note') }}</p>
      </div>

      <div class="menu-feature" data-reveal>
        <div class="menu-preview-panel">
          <a class="card-arrow menu-preview-arrow" href="{{ route('menu') }}" aria-label="View the full menu">
            <svg class="icon" aria-hidden="true"><use href="#icon-arrow-up-right"></use></svg>
          </a>

          @if ($menuPages->isNotEmpty())
            <div class="menu-deck-wrap">
              <div class="menu-deck" data-menu-deck role="group" tabindex="0" aria-label="Menu pages" aria-describedby="menu-deck-help">
                @foreach ($menuPages as $menuPage)
                  @php([$sheetWidth, $sheetHeight] = Media::dimensions($menuPage->image_path, 1055, 1491))
                  <figure class="menu-card" data-page="{{ $loop->iteration }}">
                    <div class="menu-card__sheet">
                      <img src="{{ Media::url($menuPage->image_path) }}" alt="{{ $menuPage->alt_text }}" width="{{ $sheetWidth }}" height="{{ $sheetHeight }}" @if ($loop->first) fetchpriority="high" @else loading="lazy" @endif decoding="async">
                    </div>
                  </figure>
                @endforeach
              </div>
            </div>

            <p class="menu-deck-hint" id="menu-deck-help">{{ site('home_menu_hint') }}</p>

            <div class="menu-deck-meta">
              <span class="menu-deck-count" aria-live="polite">Page <strong data-deck-current>01</strong> / {{ str_pad($menuPages->count(), 2, '0', STR_PAD_LEFT) }}</span>
              <div class="menu-deck-nav">
                <button class="menu-deck-btn menu-deck-btn--back" type="button" data-deck-dir="-1" aria-label="Slide the top page left">
                  <svg class="icon" aria-hidden="true"><use href="#icon-arrow-right"></use></svg>
                </button>
                <button class="menu-deck-btn" type="button" data-deck-dir="1" aria-label="Slide the top page right">
                  <svg class="icon" aria-hidden="true"><use href="#icon-arrow-right"></use></svg>
                </button>
              </div>
            </div>
          @else
            <div class="menu-deck-empty">
              <p class="menu-kicker">{{ site('home_menu_kicker') }}</p>
              <p class="menu-copy">The menu pages are being prepared. Please check back soon.</p>
            </div>
          @endif
        </div>

        <div class="menu-content">
          <p class="menu-kicker">{{ site('home_menu_kicker') }}</p>
          <h3 class="menu-title">{{ site('home_menu_title') }}</h3>
          <p class="menu-copy">{{ site('home_menu_copy') }}</p>

          <ul class="menu-index">
            @foreach ($menuIndex as $line)
              <li><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>{{ $line }}</li>
            @endforeach
          </ul>

          <div class="menu-actions">
            <a class="btn btn--primary" href="{{ route('menu') }}">
              <span>{{ site('home_menu_cta') }}</span>
              <svg class="icon" aria-hidden="true"><use href="#icon-arrow-up-right"></use></svg>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Juice bar -->
  <section class="section juice-section" id="juice-bar" aria-labelledby="juice-heading">
    <div class="shell">
      <div class="section-head section-head--split">
        <div>
          <p class="eyebrow" data-reveal>{{ site('home_juice_eyebrow') }}</p>
          <h2 class="section-title" id="juice-heading" data-reveal>{{ site('home_juice_title_lead') }} <span class="mark">{{ site('home_juice_title_mark') }}</span></h2>
        </div>
        <p class="section-note" data-reveal>{{ site('home_juice_note') }}</p>
      </div>

      <div class="filter-row" data-reveal>
        <div class="filter-group" role="group" aria-label="Filter the juice bar">
          <button class="filter-btn is-active" type="button" data-filter="all" aria-pressed="true">{{ site('home_filter_all') }}</button>
          <button class="filter-btn" type="button" data-filter="juice" aria-pressed="false">{{ site('home_filter_juice') }}</button>
          <button class="filter-btn" type="button" data-filter="shakes" aria-pressed="false">{{ site('home_filter_shakes') }}</button>
          <button class="filter-btn" type="button" data-filter="desserts" aria-pressed="false">{{ site('home_filter_desserts') }}</button>
        </div>
        <p class="filter-count" aria-live="polite"><span data-result-count>{{ $products->count() }}</span> {{ site('home_count_label') }}</p>
      </div>

      <div class="product-grid" data-reveal>
        @forelse ($products as $product)
          @php([$productWidth, $productHeight] = Media::dimensions($product->image_path, 1000, 1250))
          <article class="product-card" data-category="{{ $product->category }}">
            <div class="product-media">
              <img src="{{ Media::url($product->image_path) }}" alt="{{ $product->name }} at {{ site('brand_name') }}" width="{{ $productWidth }}" height="{{ $productHeight }}" loading="lazy" decoding="async">
              @if ($product->kind)
                <span class="product-kind">{{ $product->kind }}</span>
              @endif
            </div>
            <div class="product-body">
              <div class="product-heading">
                <h3>{{ $product->name }}</h3>
              </div>
              <p class="product-desc">{{ $product->description }}</p>
              <div class="product-footer">
                <span class="product-sizes">{{ $product->sizes }}</span>
                <a class="card-arrow" href="{{ route('menu') }}" aria-label="Open the menu for {{ $product->name }}">
                  <svg class="icon" aria-hidden="true"><use href="#icon-arrow-up-right"></use></svg>
                </a>
              </div>
            </div>
          </article>
        @empty
          <p class="section-note">The juice bar list is being updated. Please check back soon.</p>
        @endforelse
      </div>
    </div>
  </section>

  <!-- About -->
  <section class="section about-section" id="about-us" aria-labelledby="about-heading">
    <div class="shell about-grid">
      <div class="about-media" data-reveal>
        @php([$aboutWidth, $aboutHeight] = Media::dimensions(site('home_about_image'), 1200, 1000))
        <img class="about-photo" src="{{ Media::url(site('home_about_image')) }}" alt="{{ site('home_about_image_alt') }}" width="{{ $aboutWidth }}" height="{{ $aboutHeight }}" loading="lazy" decoding="async">
        <div class="about-note">
          <span class="about-note__mark">{{ site('home_about_note_mark') }}</span>
          <small>{{ site('home_about_note_small') }}</small>
        </div>
      </div>

      <div class="about-copy">
        <p class="eyebrow" data-reveal>{{ site('home_about_eyebrow') }}</p>
        <h2 class="section-title" id="about-heading" data-reveal>{{ site('home_about_title_lead') }} <span class="mark">{{ site('home_about_title_mark') }}</span></h2>
        <p class="about-lead" data-reveal>{{ site('home_about_lead_1') }}</p>
        <p class="about-lead" data-reveal>{{ site('home_about_lead_2') }}</p>

        <dl class="about-facts" data-reveal>
          <div>
            <dt>{{ site('home_fact_1_label') }}</dt>
            <dd><a href="{{ site('contact_map_url') }}" target="_blank" rel="noopener">{{ site('home_fact_1_value') }}</a></dd>
          </div>
          <div>
            <dt>{{ site('home_fact_2_label') }}</dt>
            <dd>{{ site('home_fact_2_value') }}</dd>
          </div>
          <div>
            <dt>{{ site('home_fact_3_label') }}</dt>
            <dd>{{ site('home_fact_3_value') }}</dd>
          </div>
          <div>
            <dt>{{ site('home_fact_4_label') }}</dt>
            <dd><a href="{{ site('contact_phone_href') }}">{{ site('home_fact_4_value') }}</a></dd>
          </div>
        </dl>
      </div>
    </div>
  </section>

  <!-- Contact -->
  <section class="section contact-section" id="contact-us" aria-labelledby="contact-heading">
    <div class="shell">
      <div class="contact-panel" data-reveal>
        <div class="contact-copy">
          <p class="eyebrow">{{ site('home_contact_eyebrow') }}</p>
          <h2 class="contact-title" id="contact-heading">{{ site('home_contact_title_1') }}<br>{{ site('home_contact_title_2') }}</h2>
          <p class="contact-lede">{{ site('home_contact_lede') }}</p>
          <div class="contact-cta">
            <a class="btn btn--primary" href="{{ site('contact_phone_href') }}">
              <svg class="icon" aria-hidden="true"><use href="#icon-phone"></use></svg>
              <span>{{ site('contact_cta') }}</span>
            </a>
          </div>
          <p class="contact-call-note">{{ site('home_contact_note') }}</p>
        </div>

        <div class="contact-details">
          <dl class="contact-list">
            <div>
              <dt>{{ site('home_contact_call_label') }}</dt>
              <dd><a href="{{ site('contact_phone_href') }}">{{ site('contact_phone') }}</a></dd>
            </div>
            <div>
              <dt>{{ site('home_contact_email_label') }}</dt>
              <dd><a href="mailto:{{ site('contact_email') }}">{{ site('contact_email') }}</a></dd>
            </div>
            <div>
              <dt>{{ site('home_contact_visit_label') }}</dt>
              <dd><a href="{{ site('contact_map_url') }}" target="_blank" rel="noopener">{{ site('contact_address_1') }}<br>{{ site('contact_address_2') }}</a></dd>
            </div>
            <div>
              <dt>{{ site('home_contact_open_label') }}</dt>
              <dd>{{ site('contact_hours_days') }}<br>{{ site('contact_hours_time') }}</dd>
            </div>
          </dl>
          <a class="contact-map" href="{{ site('contact_map_url') }}" target="_blank" rel="noopener">
            {{ site('home_contact_map_label') }}
            <svg class="icon" aria-hidden="true"><use href="#icon-arrow-up-right"></use></svg>
          </a>
        </div>
      </div>
    </div>
  </section>
@endsection

@push('scripts')
  <script>window.SIP_ROUTES = { menu: @json(route('menu')) };</script>
  <script defer src="{{ asset('assets/vendor/gsap.min.js') }}"></script>
  <script defer src="{{ asset('assets/vendor/ScrollTrigger.min.js') }}"></script>
  <script defer src="{{ asset('assets/vendor/lenis.min.js') }}"></script>
  <script defer src="{{ asset('assets/js/app.js') }}?v={{ filemtime(public_path('assets/js/app.js')) }}"></script>
@endpush
