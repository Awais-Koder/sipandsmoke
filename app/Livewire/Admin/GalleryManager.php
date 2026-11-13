<?php

namespace App\Livewire\Admin;

use App\Models\GalleryPhoto;
use App\Services\ImageService;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class GalleryManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $search = '';

    public string $categoryFilter = '';

    public int $perPage = 12;

    public bool $showModal = false;

    public ?int $editingId = null;

    public string $caption = '';

    public string $alt_text = '';

    public string $category = 'Kitchen';

    public int $sort_order = 1;

    public bool $is_active = true;

    public $image = null;

    /** @var array<int, mixed> */
    public array $newPhotos = [];

    public ?string $existingImage = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->editingId = null;
        $this->caption = '';
        $this->alt_text = '';
        $this->category = 'Kitchen';
        $this->sort_order = ((int) GalleryPhoto::query()->max('sort_order')) + 1;
        $this->is_active = true;
        $this->image = null;
        $this->newPhotos = [];
        $this->existingImage = null;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $photo = GalleryPhoto::query()->findOrFail($id);
        $this->resetValidation();
        $this->editingId = $photo->id;
        $this->caption = $photo->caption;
        $this->alt_text = $photo->alt_text;
        $this->category = $photo->category;
        $this->sort_order = $photo->sort_order;
        $this->is_active = $photo->is_active;
        $this->existingImage = $photo->image_path;
        $this->image = null;
        $this->newPhotos = [];
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingId = null;
        $this->image = null;
        $this->newPhotos = [];
        $this->existingImage = null;
        $this->resetValidation();
    }

    public function save(ImageService $images): void
    {
        $rules = [
            'caption' => ['nullable', 'string', 'max:180'],
            'alt_text' => ['nullable', 'string', 'max:220'],
            'category' => ['required', 'string', 'max:40'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['boolean'],
            'image' => ['nullable', 'image', 'max:8192'],
            'newPhotos' => ['array', 'max:20'],
            'newPhotos.*' => ['image', 'max:8192'],
        ];

        if (! $this->editingId) {
            $rules['newPhotos'][] = 'required';
            $rules['newPhotos'][] = 'min:1';
        }

        $validated = $this->validate($rules, [], [
            'caption' => 'caption',
            'alt_text' => 'alt text',
            'category' => 'category',
            'sort_order' => 'sort order',
            'image' => 'replacement image',
            'newPhotos' => 'photographs',
            'newPhotos.*' => 'photograph',
        ]);

        if ($this->editingId) {
            $this->updatePhoto($images, $validated);

            return;
        }

        $this->createPhotos($images);
    }

    public function toggle(int $id): void
    {
        $photo = GalleryPhoto::query()->findOrFail($id);
        $photo->update(['is_active' => ! $photo->is_active]);

        $this->dispatch('toast', message: $photo->is_active ? 'Photograph is visible.' : 'Photograph hidden.', type: 'success');
    }

    public function delete(int $id, ImageService $images): void
    {
        $photo = GalleryPhoto::query()->findOrFail($id);
        $images->delete($photo->image_path);
        $photo->delete();

        $this->dispatch('toast', message: 'Photograph deleted.', type: 'success');
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function updatePhoto(ImageService $images, array $validated): void
    {
        $photo = GalleryPhoto::query()->findOrFail($this->editingId);
        $newPath = null;

        if ($this->image) {
            try {
                $newPath = $images->toWebp($this->image, 'gallery', 1800, 84);
            } catch (\RuntimeException $exception) {
                $this->addError('image', $exception->getMessage());

                return;
            }
        }

        if ($newPath) {
            $images->delete($photo->image_path);
        }

        $caption = trim((string) ($validated['caption'] ?? '')) ?: $photo->caption;

        $photo->update([
            'caption' => $caption,
            'alt_text' => trim((string) ($validated['alt_text'] ?? '')) ?: $caption,
            'category' => $validated['category'],
            'sort_order' => $validated['sort_order'],
            'is_active' => $validated['is_active'],
            'image_path' => $newPath ?? $photo->image_path,
        ]);

        $this->closeModal();
        $this->dispatch('toast', message: 'Photograph updated.', type: 'success');
    }

    private function createPhotos(ImageService $images): void
    {
        $files = is_array($this->newPhotos) ? $this->newPhotos : [];
        $converted = [];

        try {
            foreach ($files as $file) {
                $converted[] = $images->toWebp($file, 'gallery', 1800, 84);
            }
        } catch (\RuntimeException $exception) {
            foreach ($converted as $path) {
                $images->delete($path);
            }

            $this->addError('newPhotos', $exception->getMessage());

            return;
        }

        $sort = $this->sort_order;

        foreach ($converted as $index => $path) {
            $original = $files[$index] ?? null;
            $base = $original
                ? Str::headline(pathinfo($original->getClientOriginalName(), PATHINFO_FILENAME))
                : 'Gallery photograph';
            $caption = trim($this->caption) ?: $base.' · '.$this->category;

            GalleryPhoto::query()->create([
                'caption' => $caption,
                'alt_text' => trim($this->alt_text) ?: $caption.' at '.site('brand_name'),
                'category' => $this->category,
                'sort_order' => $sort++,
                'is_active' => $this->is_active,
                'image_path' => $path,
            ]);
        }

        $this->closeModal();
        $this->dispatch('toast', message: count($converted).' photograph'.(count($converted) === 1 ? '' : 's').' uploaded and converted to WebP.', type: 'success');
    }

    public function render()
    {
        $photos = GalleryPhoto::query()
            ->when($this->search !== '', function ($query) {
                $term = '%'.$this->search.'%';
                $query->where(function ($query) use ($term) {
                    $query->where('caption', 'like', $term)
                        ->orWhere('alt_text', 'like', $term)
                        ->orWhere('category', 'like', $term);
                });
            })
            ->when($this->categoryFilter !== '', fn ($query) => $query->where('category', $this->categoryFilter))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate($this->perPage);

        $categories = GalleryPhoto::query()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('livewire.admin.gallery-manager', [
            'photos' => $photos,
            'categories' => $categories,
        ]);
    }
}
