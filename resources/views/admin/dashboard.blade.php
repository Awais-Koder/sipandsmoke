@extends('layouts.admin', ['title' => 'Overview'])

@php
    use App\Support\Media;
@endphp

@section('content')
  <div class="admin-page-head">
    <div>
      <p class="admin-eyebrow">Good to see you</p>
      <h1 class="admin-page-title">The <span class="admin-mark">control room.</span></h1>
      <p class="admin-page-sub">Everything on the public site can be changed from here. Saves go live immediately.</p>
    </div>
    <a class="admin-btn admin-btn--primary" href="{{ route('home') }}" target="_blank" rel="noopener">
      Open frontend
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7M8 7h9v9"/></svg>
    </a>
  </div>

  <div class="admin-stats">
    <a class="admin-stat" href="{{ route('admin.menu') }}">
      <span class="admin-stat__icon is-orange">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v17H6.5A2.5 2.5 0 0 1 4 17.5z"/><path d="M4 17.5A2.5 2.5 0 0 1 6.5 15H20"/></svg>
      </span>
      <span class="admin-stat__value">{{ $menuCount }}</span>
      <span class="admin-stat__label">Menu pages</span>
      <span class="admin-stat__meta">{{ $activeMenuCount }} live</span>
    </a>
    <a class="admin-stat" href="{{ route('admin.gallery') }}">
      <span class="admin-stat__icon is-blue">
        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="8.5" cy="9" r="1.5"/><path d="m4 17 5-5 4 4 3-3 4 4"/></svg>
      </span>
      <span class="admin-stat__value">{{ $galleryCount }}</span>
      <span class="admin-stat__label">Gallery photos</span>
      <span class="admin-stat__meta">{{ $activeGalleryCount }} shown</span>
    </a>
    <a class="admin-stat" href="{{ route('admin.products') }}">
      <span class="admin-stat__icon is-warm">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 8h12v8a5 5 0 0 1-5 5h-2a5 5 0 0 1-5-5z"/><path d="M17 10h2a3 3 0 0 1 0 6h-2M4 3v2M8 3v2M12 3v2"/></svg>
      </span>
      <span class="admin-stat__value">{{ $productCount }}</span>
      <span class="admin-stat__label">Juice bar items</span>
      <span class="admin-stat__meta">{{ $activeProductCount }} shown</span>
    </a>
    <a class="admin-stat admin-stat--accent" href="{{ route('admin.content') }}">
      <span class="admin-stat__icon is-ink">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m4 20 4.5-1 10-10a2.1 2.1 0 0 0-3-3l-10 10z"/><path d="m13.5 7.5 3 3"/></svg>
      </span>
      <span class="admin-stat__value">All</span>
      <span class="admin-stat__label">Site content</span>
      <span class="admin-stat__meta">Copy · images · SEO</span>
    </a>
  </div>

  <div class="admin-dashboard-grid">
    <section class="admin-panel">
      <div class="admin-panel__head">
        <div>
          <p class="admin-eyebrow">At a glance</p>
          <h2>Recent photographs</h2>
        </div>
        <a class="admin-link" href="{{ route('admin.gallery') }}">Manage gallery</a>
      </div>

      @if ($recentPhotos->isNotEmpty())
        <div class="admin-recent-grid">
          @foreach ($recentPhotos as $photo)
            @php([$recentWidth, $recentHeight] = Media::dimensions($photo->image_path, 667, 1000))
            <div class="admin-recent">
              <img src="{{ Media::url($photo->image_path) }}" alt="{{ $photo->alt_text }}" width="{{ $recentWidth }}" height="{{ $recentHeight }}" loading="lazy">
              <div>
                <span>{{ $photo->caption }}</span>
                <small>{{ $photo->category }} · #{{ $photo->id }}</small>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <p class="admin-empty">No photographs yet. Upload the first one in the gallery.</p>
      @endif
    </section>

    <aside class="admin-panel admin-panel--side">
      <div class="admin-panel__head">
        <div>
          <p class="admin-eyebrow">Shortcuts</p>
          <h2>Keep it current</h2>
        </div>
      </div>
      <div class="admin-shortcuts">
        <a href="{{ route('admin.menu') }}">
          <span><strong>Add a menu page</strong><small>Upload a kitchen sheet as WebP</small></span>
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7M8 7h9v9"/></svg>
        </a>
        <a href="{{ route('admin.gallery') }}">
          <span><strong>Upload photographs</strong><small>Add one or several at a time</small></span>
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7M8 7h9v9"/></svg>
        </a>
        <a href="{{ route('admin.content') }}">
          <span><strong>Swap the hero</strong><small>Change imagery, headline or logo</small></span>
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7M8 7h9v9"/></svg>
        </a>
      </div>
      <div class="admin-tip">
        <span>Tip</span>
        <p>Uploaded JPG, PNG and WebP files are converted to compressed WebP automatically. Keep phone photos under 8 MB for the quickest upload.</p>
      </div>
    </aside>
  </div>
@endsection
