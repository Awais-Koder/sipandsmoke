@php
    use App\Support\Media;
@endphp
<footer class="site-footer">
  <div class="shell">
    <div class="footer-grid">
      <div class="footer-brand">
        <a href="{{ route('home') }}" aria-label="{{ site('brand_name') }} home">
          <img class="footer-logo" src="{{ Media::url($logo) }}" alt="{{ site('brand_logo_alt') }}" width="{{ $logoDims[0] }}" height="{{ $logoDims[1] }}" loading="lazy" decoding="async">
        </a>
        <p class="footer-blurb">{{ site('footer_blurb') }}</p>
      </div>

      <div>
        <p class="footer-heading">{{ site('footer_explore') }}</p>
        <nav class="footer-nav" aria-label="Footer navigation">
          <a href="{{ route('home') }}">Home</a>
          <a href="{{ route('menu') }}">The menu</a>
          <a href="{{ route('home') }}#juice-bar">Juice bar</a>
          <a href="{{ route('home') }}#about-us">About us</a>
          <a href="{{ route('home') }}#contact-us">Contact us</a>
          <a href="{{ route('gallery') }}">Gallery</a>
        </nav>
      </div>

      <div>
        <p class="footer-heading">{{ site('footer_visit') }}</p>
        <address class="footer-contact">
          <a href="{{ site('contact_map_url') }}" target="_blank" rel="noopener">{{ site('contact_address_1') }}<br>{{ site('contact_address_2') }}</a>
          <a href="{{ site('contact_phone_href') }}">{{ site('contact_phone') }}</a>
          <a href="mailto:{{ site('contact_email') }}">{{ site('contact_email') }}</a>
          <span>{{ site('contact_hours') }}</span>
        </address>
      </div>
    </div>

    <div class="footer-bottom">
      <p>{{ site('footer_copyright') }}</p>
      <p>{{ site('footer_tagline') }}</p>
    </div>
  </div>
</footer>
