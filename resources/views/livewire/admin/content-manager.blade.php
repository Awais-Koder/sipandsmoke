@php
    use App\Support\Media;
@endphp

<div>
  <div class="admin-page-head">
    <div>
      <p class="admin-eyebrow">Public site</p>
      <h1 class="admin-page-title">Site <span class="admin-mark">content.</span></h1>
      <p class="admin-page-sub">Edit the words, images, brand details and SEO used across the homepage, menu and gallery. Changes publish immediately.</p>
    </div>
    <button type="submit" form="content-form" class="admin-btn admin-btn--primary">
      Save changes
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg>
    </button>
  </div>

  <div class="admin-content-layout">
    <aside class="admin-content-nav" aria-label="Content sections">
      <p>On this page</p>
      @foreach ($groups as $groupKey => $group)
        <a href="#content-{{ $groupKey }}">{{ $group['label'] }}</a>
      @endforeach
    </aside>

    <form id="content-form" wire:submit="save" class="admin-content-form">
      @foreach ($groups as $groupKey => $group)
        <section class="admin-content-group" id="content-{{ $groupKey }}">
          <div class="admin-content-group__head">
            <div>
              <h2>{{ $group['label'] }}</h2>
              <p>{{ $group['note'] }}</p>
            </div>
            <span class="admin-content-group__count">{{ count($group['fields']) }} fields</span>
          </div>

          <div class="admin-content-fields">
            @foreach ($group['fields'] as $key => $field)
              @php
                $isImage = $field['type'] === 'image';
                $isTextarea = $field['type'] === 'textarea';
                $upload = $uploads[$key] ?? null;
                $currentImage = $data[$key] ?? '';
              @endphp
              <div class="admin-field {{ $isTextarea || $isImage ? 'admin-field--full' : '' }}">
                <label class="admin-field__label" for="field-{{ $key }}">{{ $field['label'] }}</label>

                @if ($field['type'] === 'textarea')
                  <textarea id="field-{{ $key }}" wire:model="data.{{ $key }}" rows="4" placeholder="{{ $field['label'] }}"></textarea>
                @elseif ($field['type'] === 'color')
                  <div class="admin-colour-row">
                    <input id="field-{{ $key }}" type="color" wire:model="data.{{ $key }}">
                    <input type="text" wire:model="data.{{ $key }}" aria-label="{{ $field['label'] }} hex value" placeholder="#FF8200">
                  </div>
                @elseif ($field['type'] === 'image')
                  @if ($currentImage && Media::url($currentImage))
                    <div class="admin-image-current {{ in_array($key, ['brand_logo', 'brand_favicon']) ? 'is-logo' : '' }}">
                      <img src="{{ Media::url($currentImage) }}" alt="">
                      <span>
                        <strong>Current image</strong>
                        {{ basename($currentImage) }}
                      </span>
                    </div>
                  @endif

                  <div class="admin-file">
                    <input id="field-{{ $key }}" type="file" wire:model="uploads.{{ $key }}" accept="image/jpeg,image/png,image/webp,image/avif">
                  </div>

                  @if ($upload)
                    <div class="admin-image-preview" wire:loading.remove wire:target="uploads.{{ $key }}">
                      <img src="{{ $upload->temporaryUrl() }}" alt="New upload preview">
                      <span>New image selected. It will be converted to WebP when saved.</span>
                    </div>
                  @endif

                  <span class="admin-field__hint" wire:loading wire:target="uploads.{{ $key }}">Uploading and preparing preview…</span>
                @else
                  <input
                    id="field-{{ $key }}"
                    type="{{ $field['type'] === 'url' ? 'url' : ($field['type'] === 'email' ? 'email' : 'text') }}"
                    wire:model="data.{{ $key }}"
                    placeholder="{{ $field['label'] }}"
                  >
                @endif

                @if (! empty($field['help']))
                  <span class="admin-field__hint">{{ $field['help'] }}</span>
                @endif
                @error('data.'.$key)
                  <span class="admin-field__error">{{ $message }}</span>
                @enderror
                @error('uploads.'.$key)
                  <span class="admin-field__error">{{ $message }}</span>
                @enderror
              </div>
            @endforeach
          </div>
        </section>
      @endforeach

      <div class="admin-savebar">
        <p><strong>Ready when you are.</strong> Text saves instantly; uploads are converted to WebP.</p>
        <button type="submit" class="admin-btn admin-btn--orange" wire:loading.attr="disabled">
          <span wire:loading.remove wire:target="save">Save all changes</span>
          <span wire:loading wire:target="save">Saving…</span>
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg>
        </button>
      </div>
    </form>
  </div>
</div>
