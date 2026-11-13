@extends('layouts.site')

@php
    use App\Support\Media;
@endphp

@section('content')
  <section class="menu-page" id="menu" aria-labelledby="menu-page-title">
    <div class="shell">
      <div class="menu-page__head">
        <div>
          <p class="eyebrow">{{ site('menu_page_eyebrow') }}</p>
          <h1 class="menu-page__title" id="menu-page-title">{{ site('menu_page_title_lead') }} <span class="mark">{{ site('menu_page_title_mark') }}</span></h1>
        </div>

        <div class="menu-page__aside">
          <p class="menu-page__note">{{ site('menu_page_note') }}</p>
        </div>
      </div>

      <div class="menu-page__toolbar">
        <p>{{ site('menu_page_toolbar_left') }}</p>
        <p>{{ site('menu_page_toolbar_right') }}</p>
      </div>

      @if ($pages->isNotEmpty())
        <div class="menu-sheet-grid">
          @foreach ($pages as $page)
            @php([$sheetWidth, $sheetHeight] = Media::dimensions($page->image_path, 1055, 1491))
            <figure class="menu-sheet" id="page-{{ $loop->iteration }}">
              <figcaption class="menu-sheet__head"><span>{{ $page->title }}</span></figcaption>
              <img src="{{ Media::url($page->image_path) }}" alt="{{ $page->alt_text }}" width="{{ $sheetWidth }}" height="{{ $sheetHeight }}" @if ($loop->first) fetchpriority="high" @else loading="lazy" @endif decoding="async">
            </figure>
          @endforeach
        </div>
      @else
        <div class="menu-page__empty">
          <p class="menu-kicker">{{ site('menu_page_eyebrow') }}</p>
          <h2 class="menu-title">The menu is being updated.</h2>
          <p class="menu-copy">Please check back soon, or call us and we will help with today’s menu.</p>
          <a class="btn btn--primary" href="{{ site('contact_phone_href') }}">
            <svg class="icon" aria-hidden="true"><use href="#icon-phone"></use></svg>
            <span>{{ site('contact_cta') }}</span>
          </a>
        </div>
      @endif
    </div>
  </section>
@endsection

@push('scripts')
  <script defer src="{{ asset('assets/js/gallery.js') }}?v={{ filemtime(public_path('assets/js/gallery.js')) }}"></script>
@endpush
