<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use App\Services\ImageService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class ProductManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $search = '';

    public string $categoryFilter = '';

    public int $perPage = 12;

    public bool $showModal = false;

    public ?int $editingId = null;

    public string $category = 'juice';

    public string $kind = '';

    public string $name = '';

    public string $description = '';

    public string $sizes = 'S · M · L · XL';

    public int $sort_order = 1;

    public bool $is_active = true;

    public $image = null;

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
        $this->category = 'juice';
        $this->kind = '';
        $this->name = '';
        $this->description = '';
        $this->sizes = 'S · M · L · XL';
        $this->sort_order = ((int) Product::query()->max('sort_order')) + 1;
        $this->is_active = true;
        $this->image = null;
        $this->existingImage = null;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $product = Product::query()->findOrFail($id);
        $this->resetValidation();
        $this->editingId = $product->id;
        $this->category = $product->category;
        $this->kind = (string) $product->kind;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->sizes = (string) $product->sizes;
        $this->sort_order = $product->sort_order;
        $this->is_active = $product->is_active;
        $this->existingImage = $product->image_path;
        $this->image = null;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingId = null;
        $this->image = null;
        $this->existingImage = null;
        $this->resetValidation();
    }

    public function save(ImageService $images): void
    {
        $rules = [
            'category' => ['required', 'in:juice,shakes,desserts'],
            'kind' => ['nullable', 'string', 'max:80'],
            'name' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:500'],
            'sizes' => ['nullable', 'string', 'max:80'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['boolean'],
            'image' => ['nullable', 'image', 'max:8192'],
        ];

        if (! $this->editingId) {
            $rules['image'][] = 'required';
        }

        $validated = $this->validate($rules, [], [
            'category' => 'category',
            'name' => 'item name',
            'description' => 'description',
            'sizes' => 'sizes',
            'sort_order' => 'sort order',
            'image' => 'product image',
        ]);

        $newPath = null;

        if ($this->image) {
            try {
                $newPath = $images->toWebp($this->image, 'products', 1400, 84);
            } catch (\RuntimeException $exception) {
                $this->addError('image', $exception->getMessage());

                return;
            }
        }

        if ($this->editingId) {
            $product = Product::query()->findOrFail($this->editingId);

            if ($newPath) {
                $images->delete($product->image_path);
            }

            $product->update([
                'category' => $validated['category'],
                'kind' => $validated['kind'] ?? null,
                'name' => $validated['name'],
                'description' => $validated['description'],
                'sizes' => $validated['sizes'] ?? null,
                'sort_order' => $validated['sort_order'],
                'is_active' => $validated['is_active'],
                'image_path' => $newPath ?? $product->image_path,
            ]);

            $message = 'Juice bar item updated.';
        } else {
            Product::query()->create([
                'category' => $validated['category'],
                'kind' => $validated['kind'] ?? null,
                'name' => $validated['name'],
                'description' => $validated['description'],
                'sizes' => $validated['sizes'] ?? null,
                'sort_order' => $validated['sort_order'],
                'is_active' => $validated['is_active'],
                'image_path' => $newPath,
            ]);

            $message = 'Juice bar item added.';
        }

        $this->closeModal();
        $this->dispatch('toast', message: $message, type: 'success');
    }

    public function toggle(int $id): void
    {
        $product = Product::query()->findOrFail($id);
        $product->update(['is_active' => ! $product->is_active]);

        $this->dispatch('toast', message: $product->is_active ? 'Item is live.' : 'Item hidden.', type: 'success');
    }

    public function delete(int $id, ImageService $images): void
    {
        $product = Product::query()->findOrFail($id);
        $images->delete($product->image_path);
        $product->delete();

        $this->dispatch('toast', message: 'Juice bar item deleted.', type: 'success');
    }

    public function render()
    {
        $products = Product::query()
            ->when($this->search !== '', function ($query) {
                $term = '%'.$this->search.'%';
                $query->where(function ($query) use ($term) {
                    $query->where('name', 'like', $term)
                        ->orWhere('description', 'like', $term)
                        ->orWhere('kind', 'like', $term);
                });
            })
            ->when($this->categoryFilter !== '', fn ($query) => $query->where('category', $this->categoryFilter))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate($this->perPage);

        return view('livewire.admin.product-manager', [
            'products' => $products,
        ]);
    }
}
