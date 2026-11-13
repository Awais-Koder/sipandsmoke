@extends('layouts.site')

@php
    use App\Support\Media;

    $photoCount = $photos->count();
    $firstPageCount = min(24, $photoCount);
@endphp

@section('content')
  <section class="gallery-page" id="gallery" aria-labelledby="gallery-heading">
    <div class="shell">
      <div class="gallery-page__head">
        <div>
          <p class="eyebrow">{{ site('gallery_eyebrow') }}</p>
          <h1 class="gallery-page__title" id="gallery-heading">{{ site('gallery_title_lead') }} <span class="mark">{{ site('gallery_title_mark') }}</span></h1>
        </div>
        <p class="gallery-page__note">{{ site('gallery_note') }}</p>
      </div>

      <div class="gallery-page__toolbar">
        <p class="gallery-page__status" aria-live="polite" data-gallery-status>
          Page 1 of {{ max(1, (int) ceil($photoCount / 24)) }} · showing {{ $photoCount ? 1 : 0 }}–{{ $firstPageCount }} of {{ $photoCount }} photographs
        </p>
        <p class="gallery-page__count">{{ site('gallery_count_label') }}</p>
      </div>

      @if ($photos->isNotEmpty())
        <div class="gallery-grid gallery-page__grid" data-gallery-grid>
          @foreach ($photos as $photo)
            @php([$photoWidth, $photoHeight] = Media::dimensions($photo->image_path, 667, 1000))
            <figure class="gallery-item">
              <button class="gallery-photo" type="button" data-gallery-item data-full="{{ Media::url($photo->image_path) }}" data-alt="{{ $photo->alt_text }}" data-caption="{{ $photo->caption }}" aria-label="Open photo {{ $loop->iteration }}: {{ $photo->alt_text }}">
                <img src="{{ Media::url($photo->image_path) }}" alt="{{ $photo->alt_text }}" width="{{ $photoWidth }}" height="{{ $photoHeight }}" loading="lazy" decoding="async">
              </button>
              <span class="gallery-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
              <span class="gallery-tag">{{ $photo->category }}</span>
            </figure>
          @endforeach
        </div>

        <nav class="pagination" data-pagination aria-label="Gallery pages"></nav>

        <noscript>
          <p class="gallery-page__noscript">{{ site('gallery_noscript') }}</p>
        </noscript>
      @else
        <div class="gallery-page__empty">
          <p class="menu-kicker">{{ site('gallery_eyebrow') }}</p>
          <h2 class="menu-title">The gallery is being filled.</h2>
          <p class="menu-copy">New photographs are on the way. In the meantime, come see the room in person.</p>
        </div>
      @endif
    </div>
  </section>
@endsection

@push('dialogs')
  @if ($photos->isNotEmpty())
    <dialog class="gallery-dialog" id="gallery-dialog" aria-labelledby="gallery-dialog-title">
      <div class="gallery-dialog__panel">
        <header class="gallery-dialog__head">
          <h2 class="gallery-dialog__title" id="gallery-dialog-title">Gallery</h2>
          <span class="gallery-dialog__count" data-gallery-count>01 / {{ str_pad($photoCount, 2, '0', STR_PAD_LEFT) }}</span>
          <button class="gallery-dialog__close" type="button" data-gallery-close aria-label="Close gallery">
            <svg class="icon" aria-hidden="true"><use href="#icon-close"></use></svg>
          </button>
        </header>
        <div class="gallery-dialog__stage" data-gallery-stage>
          <button class="gallery-dialog__nav gallery-dialog__nav--prev" type="button" data-gallery-prev aria-label="Previous photo">
            <svg class="icon" aria-hidden="true"><use href="#icon-arrow-right"></use></svg>
          </button>
          <img data-gallery-img src="{{ Media::url($photos->first()->image_path) }}" alt="" decoding="async">
          <button class="gallery-dialog__nav gallery-dialog__nav--next" type="button" data-gallery-next aria-label="Next photo">
            <svg class="icon" aria-hidden="true"><use href="#icon-arrow-right"></use></svg>
          </button>
        </div>
        <footer class="gallery-dialog__foot">
          <p data-gallery-caption></p>
          <p class="gallery-dialog__hint">Use &larr; &rarr; to browse</p>
        </footer>
      </div>
    </dialog>
  @endif
@endpush

@push('scripts')
  <script defer src="{{ asset('assets/js/gallery.js') }}?v={{ filemtime(public_path('assets/js/gallery.js')) }}"></script>
@endpush
