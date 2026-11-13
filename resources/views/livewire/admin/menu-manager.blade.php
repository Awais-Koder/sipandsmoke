@php
    use App\Support\Media;
@endphp

<div>
  <div class="admin-page-head">
    <div>
      <p class="admin-eyebrow">Kitchen</p>
      <h1 class="admin-page-title">Menu <span class="admin-mark">pages.</span></h1>
      <p class="admin-page-sub">Each sheet appears in the homepage stack and on the menu page. Upload a new image to replace an existing page.</p>
    </div>
    <button type="button" class="admin-btn admin-btn--primary" wire:click="openCreate">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
      Add menu page
    </button>
  </div>

  <div class="admin-toolbar">
    <label class="admin-search">
      <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16 16 4 4"/></svg>
      <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search menu pages…" aria-label="Search menu pages">
    </label>
    <span class="admin-badge is-category">{{ $pages->total() }} pages</span>
  </div>

  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Page</th>
          <th>Sheet</th>
          <th>Order</th>
          <th>Status</th>
          <th aria-label="Actions"></th>
        </tr>
      </thead>
      <tbody>
        @forelse ($pages as $page)
          @php([$menuWidth, $menuHeight] = Media::dimensions($page->image_path, 1055, 1491))
          <tr wire:key="menu-page-{{ $page->id }}">
            <td>
              <div class="admin-table__title">
                <strong>{{ $page->title }}</strong>
                <small>{{ $page->alt_text }}</small>
              </div>
            </td>
            <td>
              <img class="admin-table__thumb" src="{{ Media::url($page->image_path) }}" alt="" width="{{ $menuWidth }}" height="{{ $menuHeight }}" loading="lazy">
            </td>
            <td><span class="admin-table__number">{{ str_pad($page->sort_order, 2, '0', STR_PAD_LEFT) }}</span></td>
            <td>
              <button type="button" wire:click="toggle({{ $page->id }})" class="admin-badge {{ $page->is_active ? 'is-live' : 'is-hidden' }}" title="Toggle visibility">
                {{ $page->is_active ? 'Live' : 'Hidden' }}
              </button>
            </td>
            <td>
              <div class="admin-row-actions">
                <button type="button" wire:click="toggle({{ $page->id }})" class="admin-icon-btn" title="{{ $page->is_active ? 'Hide page' : 'Publish page' }}" aria-label="{{ $page->is_active ? 'Hide page' : 'Publish page' }}">
                  @if ($page->is_active)
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                  @else
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m3 3 18 18M10.6 6.2A11.8 11.8 0 0 1 12 6c6.5 0 10 6 10 6a17 17 0 0 1-3 3.5M6.2 6.7A16.4 16.4 0 0 0 2 12s3.5 6 10 6a10 10 0 0 0 4-.8"/></svg>
                  @endif
                </button>
                <button type="button" wire:click="edit({{ $page->id }})" class="admin-icon-btn" title="Edit page" aria-label="Edit {{ $page->title }}">
                  <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m4 20 4.5-1 10-10a2.1 2.1 0 0 0-3-3l-10 10z"/><path d="m13.5 7.5 3 3"/></svg>
                </button>
                <button type="button" wire:click="delete({{ $page->id }})" wire:confirm="Delete this menu page? The image will also be removed." class="admin-icon-btn is-danger" title="Delete page" aria-label="Delete {{ $page->title }}">
                  <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3M7 7l1 13h8l1-13M10 11v5M14 11v5"/></svg>
                </button>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="admin-table__empty">No menu pages yet. Upload the first sheet to start the stack.</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <div class="admin-table-foot">
      <p>Showing <strong>{{ $pages->firstItem() ?? 0 }}–{{ $pages->lastItem() ?? 0 }}</strong> of <strong>{{ $pages->total() }}</strong></p>
      {{ $pages->links('admin.partials.pagination') }}
    </div>
  </div>

  @if ($showModal)
    <div class="admin-modal" role="dialog" aria-modal="true" aria-labelledby="menu-modal-title">
      <div class="admin-modal__backdrop" wire:click="closeModal"></div>
      <div class="admin-modal__dialog">
        <form wire:submit="save">
          <div class="admin-modal__head">
            <div>
              <h2 id="menu-modal-title">{{ $editingId ? 'Edit menu page' : 'Add menu page' }}</h2>
              <p>{{ $editingId ? 'Replace the sheet or update its details.' : 'Upload a menu sheet; it will be converted to compressed WebP.' }}</p>
            </div>
            <button type="button" class="admin-modal__close" wire:click="closeModal" aria-label="Close">
              <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>
            </button>
          </div>

          <div class="admin-modal__body">
            <div class="admin-form-grid">
              @if ($existingImage)
                <div class="admin-field admin-field--full">
                  <span class="admin-field__label">Current sheet</span>
                  <div class="admin-image-current">
                    <img src="{{ Media::url($existingImage) }}" alt="">
                    <span><strong>{{ $title ?: 'Menu page' }}</strong>{{ basename($existingImage) }}</span>
                  </div>
                </div>
              @endif

              <div class="admin-field admin-field--full">
                <label class="admin-field__label" for="menu-image">{{ $existingImage ? 'Replacement image (optional)' : 'Menu sheet image' }}</label>
                <div class="admin-upload-drop">
                  <div>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 16V4m-5 5 5-5 5 5"/><path d="M4 15v4a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-4"/></svg>
                    <strong>Choose a JPG, PNG or WebP</strong>
                    <span>Converted to WebP automatically · up to 8 MB</span>
                    <input id="menu-image" type="file" wire:model="image" accept="image/jpeg,image/png,image/webp,image/avif">
                  </div>
                </div>
                <span class="admin-field__hint" wire:loading wire:target="image">Uploading image…</span>
                @error('image') <span class="admin-field__error">{{ $message }}</span> @enderror
                @if ($image)
                  <div class="admin-image-preview">
                    <img src="{{ $image->temporaryUrl() }}" alt="New menu sheet preview">
                    <span>New sheet ready to save.</span>
                  </div>
                @endif
              </div>

              <div class="admin-field">
                <label class="admin-field__label" for="menu-title">Page title</label>
                <input id="menu-title" type="text" wire:model="title" placeholder="e.g. Burgers">
                @error('title') <span class="admin-field__error">{{ $message }}</span> @enderror
              </div>

              <div class="admin-field">
                <label class="admin-field__label" for="menu-order">Sort order</label>
                <input id="menu-order" type="number" min="0" max="9999" wire:model="sort_order">
                @error('sort_order') <span class="admin-field__error">{{ $message }}</span> @enderror
              </div>

              <div class="admin-field admin-field--full">
                <label class="admin-field__label" for="menu-alt">Image alt text</label>
                <input id="menu-alt" type="text" wire:model="alt_text" placeholder="Sip & Smoke burgers menu">
                @error('alt_text') <span class="admin-field__error">{{ $message }}</span> @enderror
              </div>

              <div class="admin-field admin-field--full">
                <label class="admin-check">
                  <input type="checkbox" wire:model="is_active">
                  <span>Show this page on the website</span>
                </label>
              </div>
            </div>
          </div>

          <div class="admin-modal__foot">
            <button type="button" class="admin-btn admin-btn--ghost" wire:click="closeModal">Cancel</button>
            <button type="submit" class="admin-btn admin-btn--primary" wire:loading.attr="disabled">
              <span wire:loading.remove wire:target="save">{{ $editingId ? 'Save page' : 'Add page' }}</span>
              <span wire:loading wire:target="save">Saving…</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  @endif
</div>
