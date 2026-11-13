@php
    use App\Models\Product;
    use App\Support\Media;
@endphp

<div>
  <div class="admin-page-head">
    <div>
      <p class="admin-eyebrow">Juice bar</p>
      <h1 class="admin-page-title">Juice bar <span class="admin-mark">items.</span></h1>
      <p class="admin-page-sub">These cards appear in the juice bar section on the homepage. Add, edit, reorder or hide any item.</p>
    </div>
    <button type="button" class="admin-btn admin-btn--primary" wire:click="openCreate">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
      Add item
    </button>
  </div>

  <div class="admin-toolbar">
    <label class="admin-search">
      <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16 16 4 4"/></svg>
      <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search items…" aria-label="Search juice bar items">
    </label>
    <select class="admin-select" wire:model.live="categoryFilter" aria-label="Filter by category">
      <option value="">All categories</option>
      @foreach (Product::CATEGORIES as $value => $label)
        <option value="{{ $value }}">{{ $label }}</option>
      @endforeach
    </select>
    <span class="admin-badge is-category">{{ $products->total() }} items</span>
  </div>

  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Item</th>
          <th>Category</th>
          <th>Sizes</th>
          <th>Order</th>
          <th>Status</th>
          <th aria-label="Actions"></th>
        </tr>
      </thead>
      <tbody>
        @forelse ($products as $product)
          @php([$productWidth, $productHeight] = Media::dimensions($product->image_path, 1000, 1250))
          <tr wire:key="product-{{ $product->id }}">
            <td>
              <div style="display:flex;align-items:center;gap:.65rem">
                <img class="admin-table__thumb" src="{{ Media::url($product->image_path) }}" alt="" width="{{ $productWidth }}" height="{{ $productHeight }}" loading="lazy">
                <div class="admin-table__title">
                  <strong>{{ $product->name }}</strong>
                  <small>{{ $product->description }}</small>
                </div>
              </div>
            </td>
            <td><span class="admin-badge is-category">{{ $product->categoryLabel() }}</span></td>
            <td><span class="admin-table__number">{{ $product->sizes ?: '—' }}</span></td>
            <td><span class="admin-table__number">{{ str_pad($product->sort_order, 2, '0', STR_PAD_LEFT) }}</span></td>
            <td>
              <button type="button" wire:click="toggle({{ $product->id }})" class="admin-badge {{ $product->is_active ? 'is-live' : 'is-hidden' }}" title="Toggle visibility">
                {{ $product->is_active ? 'Live' : 'Hidden' }}
              </button>
            </td>
            <td>
              <div class="admin-row-actions">
                <button type="button" wire:click="edit({{ $product->id }})" class="admin-icon-btn" title="Edit item" aria-label="Edit {{ $product->name }}">
                  <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m4 20 4.5-1 10-10a2.1 2.1 0 0 0-3-3l-10 10z"/><path d="m13.5 7.5 3 3"/></svg>
                </button>
                <button type="button" wire:click="delete({{ $product->id }})" wire:confirm="Delete this juice bar item? Its uploaded image will also be removed." class="admin-icon-btn is-danger" title="Delete item" aria-label="Delete {{ $product->name }}">
                  <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3M7 7l1 13h8l1-13M10 11v5M14 11v5"/></svg>
                </button>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="admin-table__empty">No juice bar items yet. Add the first one to get started.</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <div class="admin-table-foot">
      <p>Showing <strong>{{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}</strong> of <strong>{{ $products->total() }}</strong></p>
      {{ $products->links('admin.partials.pagination') }}
    </div>
  </div>

  @if ($showModal)
    <div class="admin-modal" role="dialog" aria-modal="true" aria-labelledby="product-modal-title">
      <div class="admin-modal__backdrop" wire:click="closeModal"></div>
      <div class="admin-modal__dialog">
        <form wire:submit="save">
          <div class="admin-modal__head">
            <div>
              <h2 id="product-modal-title">{{ $editingId ? 'Edit juice bar item' : 'Add juice bar item' }}</h2>
              <p>{{ $editingId ? 'Update the card details or replace its image.' : 'Upload an image; it will be converted to compressed WebP.' }}</p>
            </div>
            <button type="button" class="admin-modal__close" wire:click="closeModal" aria-label="Close">
              <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>
            </button>
          </div>

          <div class="admin-modal__body">
            <div class="admin-form-grid">
              @if ($existingImage)
                <div class="admin-field admin-field--full">
                  <span class="admin-field__label">Current image</span>
                  <div class="admin-image-current">
                    <img src="{{ Media::url($existingImage) }}" alt="">
                    <span><strong>{{ $name ?: 'Juice bar item' }}</strong>{{ basename($existingImage) }}</span>
                  </div>
                </div>
              @endif

              <div class="admin-field admin-field--full">
                <label class="admin-field__label" for="product-image">{{ $existingImage ? 'Replacement image (optional)' : 'Item image' }}</label>
                <div class="admin-file">
                  <input id="product-image" type="file" wire:model="image" accept="image/jpeg,image/png,image/webp,image/avif">
                </div>
                <span class="admin-field__hint" wire:loading wire:target="image">Uploading image…</span>
                @error('image') <span class="admin-field__error">{{ $message }}</span> @enderror
                @if ($image)
                  <div class="admin-image-preview">
                    <img src="{{ $image->temporaryUrl() }}" alt="New item image preview">
                    <span>New image ready to save.</span>
                  </div>
                @endif
              </div>

              <div class="admin-field">
                <label class="admin-field__label" for="product-category">Category</label>
                <select id="product-category" wire:model="category">
                  @foreach (Product::CATEGORIES as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                  @endforeach
                </select>
                @error('category') <span class="admin-field__error">{{ $message }}</span> @enderror
              </div>

              <div class="admin-field">
                <label class="admin-field__label" for="product-kind">Badge</label>
                <input id="product-kind" type="text" wire:model="kind" placeholder="Arabian cocktail">
                @error('kind') <span class="admin-field__error">{{ $message }}</span> @enderror
              </div>

              <div class="admin-field">
                <label class="admin-field__label" for="product-name">Item name</label>
                <input id="product-name" type="text" wire:model="name" placeholder="Avocado Dream">
                @error('name') <span class="admin-field__error">{{ $message }}</span> @enderror
              </div>

              <div class="admin-field">
                <label class="admin-field__label" for="product-sizes">Sizes / serve</label>
                <input id="product-sizes" type="text" wire:model="sizes" placeholder="S · M · L · XL">
                @error('sizes') <span class="admin-field__error">{{ $message }}</span> @enderror
              </div>

              <div class="admin-field admin-field--full">
                <label class="admin-field__label" for="product-description">Description</label>
                <textarea id="product-description" wire:model="description" rows="3" placeholder="Creamy avocado, milk and a little sweetness."></textarea>
                @error('description') <span class="admin-field__error">{{ $message }}</span> @enderror
              </div>

              <div class="admin-field">
                <label class="admin-field__label" for="product-order">Sort order</label>
                <input id="product-order" type="number" min="0" max="9999" wire:model="sort_order">
                @error('sort_order') <span class="admin-field__error">{{ $message }}</span> @enderror
              </div>

              <div class="admin-field">
                <label class="admin-check">
                  <input type="checkbox" wire:model="is_active">
                  <span>Show this item on the website</span>
                </label>
              </div>
            </div>
          </div>

          <div class="admin-modal__foot">
            <button type="button" class="admin-btn admin-btn--ghost" wire:click="closeModal">Cancel</button>
            <button type="submit" class="admin-btn admin-btn--primary" wire:loading.attr="disabled">
              <span wire:loading.remove wire:target="save">{{ $editingId ? 'Save item' : 'Add item' }}</span>
              <span wire:loading wire:target="save">Saving…</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  @endif
</div>
