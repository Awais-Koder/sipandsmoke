@php
    use App\Support\Media;
@endphp
<dialog class="mobile-nav-dialog" id="mobile-nav" aria-labelledby="mobile-nav-title" data-lenis-prevent>
  <div class="mobile-nav__inner">
    <div class="mobile-nav__head">
      <a class="brand" href="{{ route('home') }}" aria-label="{{ site('brand_name') }} home" data-nav-link>
        <img class="brand-logo" src="{{ Media::url($logo) }}" alt="{{ site('brand_logo_alt') }}" width="{{ $logoDims[0] }}" height="{{ $logoDims[1] }}">
      </a>
      <button class="dialog-close mobile-nav__close" type="button" data-nav-close aria-label="Close navigation">
        <svg class="icon" aria-hidden="true"><use href="#icon-close"></use></svg>
      </button>
    </div>

    <h2 class="sr-only" id="mobile-nav-title">Navigation</h2>

    <nav class="mobile-nav__links" aria-label="Mobile navigation">
      @foreach ($navItems as $item)
        <a class="mobile-nav__link" href="{{ $item['url'] }}" data-nav-link @if (($current ?? '') === $item['key']) aria-current="page" @endif>{{ $item['label'] }} <svg class="icon" aria-hidden="true"><use href="#icon-arrow-up-right"></use></svg></a>
      @endforeach
    </nav>

    <div class="mobile-nav__foot">
      <a class="btn btn--primary" href="{{ site('contact_phone_href') }}">
        <svg class="icon" aria-hidden="true"><use href="#icon-phone"></use></svg>
        <span>{{ site('contact_cta') }}</span>
      </a>
      <p class="mobile-nav__contact">{!! nl2br(e(site('contact_mobile_note'))) !!}</p>
    </div>
  </div>
</dialog>
