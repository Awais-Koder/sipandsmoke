@php
    use App\Support\Media;
@endphp

<div>
  <div class="admin-page-head">
    <div>
      <p class="admin-eyebrow">Photography</p>
      <h1 class="admin-page-title">The <span class="admin-mark">gallery.</span></h1>
      <p class="admin-page-sub">Upload one or several photographs. Every file is converted to WebP, ordered and published to the gallery page.</p>
    </div>
    <button type="button" class="admin-btn admin-btn--primary" wire:click="openCreate">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
      Upload photographs
    </button>
  </div>

  <div class="admin-toolbar">
    <label class="admin-search">
      <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16 16 4 4"/></svg>
      <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search captions, tags or alt text…" aria-label="Search gallery">
    </label>
    <select class="admin-select" wire:model.live="categoryFilter" aria-label="Filter by collection">
      <option value="">All collections</option>
      @foreach ($categories as $category)
        <option value="{{ $category }}">{{ $category }}</option>
      @endforeach
    </select>
    <span class="admin-badge is-category">{{ $photos->total() }} photographs</span>
  </div>

  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Photograph</th>
          <th>Collection</th>
          <th>Order</th>
          <th>Status</th>
          <th aria-label="Actions"></th>
        </tr>
      </thead>
      <tbody>
        @forelse ($photos as $photo)
          @php([$photoWidth, $photoHeight] = Media::dimensions($photo->image_path, 667, 1000))
          <tr wire:key="gallery-photo-{{ $photo->id }}">
            <td>
              <div style="display:flex;align-items:center;gap:.65rem">
                <img class="admin-table__thumb" src="{{ Media::url($photo->image_path) }}" alt="" width="{{ $photoWidth }}" height="{{ $photoHeight }}" loading="lazy">
                <div class="admin-table__title">
                  <strong>{{ $photo->caption }}</strong>
                  <small>{{ $photo->alt_text }}</small>
                </div>
              </div>
            </td>
            <td><span class="admin-badge is-category">{{ $photo->category }}</span></td>
            <td><span class="admin-table__number">{{ str_pad($photo->sort_order, 2, '0', STR_PAD_LEFT) }}</span></td>
            <td>
              <button type="button" wire:click="toggle({{ $photo->id }})" class="admin-badge {{ $photo->is_active ? 'is-live' : 'is-hidden' }}" title="Toggle visibility">
                {{ $photo->is_active ? 'Live' : 'Hidden' }}
              </button>
            </td>
            <td>
              <div class="admin-row-actions">
                <button type="button" wire:click="toggle({{ $photo->id }})" class="admin-icon-btn" title="{{ $photo->is_active ? 'Hide photo' : 'Publish photo' }}" aria-label="{{ $photo->is_active ? 'Hide photo' : 'Publish photo' }}">
                  @if ($photo->is_active)
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                  @else
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m3 3 18 18M10.6 6.2A11.8 11.8 0 0 1 12 6c6.5 0 10 6 10 6a17 17 0 0 1-3 3.5M6.2 6.7A16.4 16.4 0 0 0 2 12s3.5 6 10 6a10 10 0 0 0 4-.8"/></svg>
                  @endif
                </button>
                <button type="button" wire:click="edit({{ $photo->id }})" class="admin-icon-btn" title="Edit photo" aria-label="Edit {{ $photo->caption }}">
                  <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m4 20 4.5-1 10-10a2.1 2.1 0 0 0-3-3l-10 10z"/><path d="m13.5 7.5 3 3"/></svg>
                </button>
                <button type="button" wire:click="delete({{ $photo->id }})" wire:confirm="Delete this photograph? The uploaded image will also be removed." class="admin-icon-btn is-danger" title="Delete photo" aria-label="Delete {{ $photo->caption }}">
                  <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3M7 7l1 13h8l1-13M10 11v5M14 11v5"/></svg>
                </button>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="admin-table__empty">No photographs found. Upload a batch to get the gallery started.</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <div class="admin-table-foot">
      <p>Showing <strong>{{ $photos->firstItem() ?? 0 }}–{{ $photos->lastItem() ?? 0 }}</strong> of <strong>{{ $photos->total() }}</strong></p>
      {{ $photos->links('admin.partials.pagination') }}
    </div>
  </div>

  @if ($showModal)
    <div class="admin-modal" role="dialog" aria-modal="true" aria-labelledby="gallery-modal-title">
      <div class="admin-modal__backdrop" wire:click="closeModal"></div>
      <div class="admin-modal__dialog admin-modal__dialog--wide">
        <form wire:submit="save">
          <div class="admin-modal__head">
            <div>
              <h2 id="gallery-modal-title">{{ $editingId ? 'Edit photograph' : 'Upload photographs' }}</h2>
              <p>{{ $editingId ? 'Update the caption, collection or replace the image.' : 'Select up to 20 images. Each one is converted to WebP.' }}</p>
            </div>
            <button type="button" class="admin-modal__close" wire:click="closeModal" aria-label="Close">
              <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>
            </button>
          </div>

          <div class="admin-modal__body">
            <div class="admin-form-grid">
              @if ($existingImage)
                <div class="admin-field admin-field--full">
                  <span class="admin-field__label">Current photograph</span>
                  <div class="admin-image-current">
                    <img src="{{ Media::url($existingImage) }}" alt="">
                    <span><strong>{{ $caption ?: 'Photograph' }}</strong>{{ basename($existingImage) }}</span>
                  </div>
                </div>

                <div class="admin-field admin-field--full">
                  <label class="admin-field__label" for="gallery-image">Replace image (optional)</label>
                  <div class="admin-file">
                    <input id="gallery-image" type="file" wire:model="image" accept="image/jpeg,image/png,image/webp,image/avif">
                  </div>
                  @error('image') <span class="admin-field__error">{{ $message }}</span> @enderror
                  @if ($image)
                    <div class="admin-image-preview">
                      <img src="{{ $image->temporaryUrl() }}" alt="Replacement preview">
                      <span>New photograph ready to save.</span>
                    </div>
                  @endif
                </div>
              @else
                <div class="admin-field admin-field--full">
                  <label class="admin-field__label" for="gallery-photos">Photographs</label>
                  <div class="admin-upload-drop">
                    <div>
                      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 16V4m-5 5 5-5 5 5"/><path d="M4 15v4a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-4"/></svg>
                      <strong>Choose one or several images</strong>
                      <span>JPG, PNG, WebP or AVIF · up to 20 files · 8 MB each</span>
                      <input id="gallery-photos" type="file" wire:model="newPhotos" multiple accept="image/jpeg,image/png,image/webp,image/avif">
                    </div>
                  </div>
                  <span class="admin-field__hint" wire:loading wire:target="newPhotos">Uploading {{ count($newPhotos) }} photograph{{ count($newPhotos) === 1 ? '' : 's' }}…</span>
                  @error('newPhotos') <span class="admin-field__error">{{ $message }}</span> @enderror
                  @error('newPhotos.*') <span class="admin-field__error">{{ $message }}</span> @enderror
                  @if (count($newPhotos))
                    <div class="admin-bulk-list">
                      @foreach ($newPhotos as $newPhoto)
                        <span>{{ $newPhoto->getClientOriginalName() }}</span>
                      @endforeach
                    </div>
                  @endif
                </div>
              @endif

              <div class="admin-field">
                <label class="admin-field__label" for="gallery-category">Collection</label>
                <input id="gallery-category" type="text" wire:model="category" list="gallery-categories" placeholder="Kitchen">
                <datalist id="gallery-categories">
                  @foreach ($categories as $categoryOption)
                    <option value="{{ $categoryOption }}"></option>
                  @endforeach
                  <option value="Kitchen"></option>
                  <option value="Lounge"></option>
                  <option value="Shakes"></option>
                  <option value="Desserts"></option>
                </datalist>
                @error('category') <span class="admin-field__error">{{ $message }}</span> @enderror
              </div>

              <div class="admin-field">
                <label class="admin-field__label" for="gallery-order">Sort order</label>
                <input id="gallery-order" type="number" min="0" max="9999" wire:model="sort_order">
                @error('sort_order') <span class="admin-field__error">{{ $message }}</span> @enderror
              </div>

              <div class="admin-field admin-field--full">
                <label class="admin-field__label" for="gallery-caption">Caption</label>
                <input id="gallery-caption" type="text" wire:model="caption" placeholder="{{ $editingId ? 'Stone-baked pizza · Kitchen' : 'Optional — filenames are used for batch uploads' }}">
                @error('caption') <span class="admin-field__error">{{ $message }}</span> @enderror
              </div>

              <div class="admin-field admin-field--full">
                <label class="admin-field__label" for="gallery-alt">Alt text</label>
                <input id="gallery-alt" type="text" wire:model="alt_text" placeholder="Describe the photograph for screen readers">
                @error('alt_text') <span class="admin-field__error">{{ $message }}</span> @enderror
              </div>

              <div class="admin-field admin-field--full">
                <label class="admin-check">
                  <input type="checkbox" wire:model="is_active">
                  <span>Show {{ $editingId ? 'this photograph' : 'these photographs' }} on the website</span>
                </label>
              </div>
            </div>
          </div>

          <div class="admin-modal__foot">
            <button type="button" class="admin-btn admin-btn--ghost" wire:click="closeModal">Cancel</button>
            <button type="submit" class="admin-btn admin-btn--primary" wire:loading.attr="disabled">
              <span wire:loading.remove wire:target="save">{{ $editingId ? 'Save photograph' : 'Upload and publish' }}</span>
              <span wire:loading wire:target="save">Uploading…</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  @endif
</div>
