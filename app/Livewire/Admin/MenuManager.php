<?php

namespace App\Livewire\Admin;

use App\Models\MenuPage;
use App\Services\ImageService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class MenuManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $search = '';

    public int $perPage = 12;

    public bool $showModal = false;

    public ?int $editingId = null;

    public string $title = '';

    public string $alt_text = '';

    public int $sort_order = 1;

    public bool $is_active = true;

    public $image = null;

    public ?string $existingImage = null;

    protected $queryString = ['search' => ['except' => '']];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->editingId = null;
        $this->title = '';
        $this->alt_text = '';
        $this->sort_order = ((int) MenuPage::query()->max('sort_order')) + 1;
        $this->is_active = true;
        $this->image = null;
        $this->existingImage = null;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $page = MenuPage::query()->findOrFail($id);
        $this->resetValidation();
        $this->editingId = $page->id;
        $this->title = $page->title;
        $this->alt_text = $page->alt_text;
        $this->sort_order = $page->sort_order;
        $this->is_active = $page->is_active;
        $this->existingImage = $page->image_path;
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
            'title' => ['required', 'string', 'max:120'],
            'alt_text' => ['required', 'string', 'max:180'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['boolean'],
            'image' => ['nullable', 'image', 'max:8192'],
        ];

        if (! $this->editingId) {
            $rules['image'][] = 'required';
        }

        $validated = $this->validate($rules, [], [
            'title' => 'page title',
            'alt_text' => 'image alt text',
            'sort_order' => 'sort order',
            'image' => 'menu image',
        ]);

        $newPath = null;

        if ($this->image) {
            try {
                $newPath = $images->toWebp($this->image, 'menu', 1600, 86);
            } catch (\RuntimeException $exception) {
                $this->addError('image', $exception->getMessage());

                return;
            }
        }

        if ($this->editingId) {
            $page = MenuPage::query()->findOrFail($this->editingId);

            if ($newPath) {
                $images->delete($page->image_path);
            }

            $page->update([
                'title' => $validated['title'],
                'alt_text' => $validated['alt_text'],
                'sort_order' => $validated['sort_order'],
                'is_active' => $validated['is_active'],
                'image_path' => $newPath ?? $page->image_path,
            ]);

            $message = 'Menu page updated.';
        } else {
            MenuPage::query()->create([
                'title' => $validated['title'],
                'alt_text' => $validated['alt_text'],
                'sort_order' => $validated['sort_order'],
                'is_active' => $validated['is_active'],
                'image_path' => $newPath,
            ]);

            $message = 'Menu page added.';
        }

        $this->closeModal();
        $this->dispatch('toast', message: $message, type: 'success');
    }

    public function toggle(int $id): void
    {
        $page = MenuPage::query()->findOrFail($id);
        $page->update(['is_active' => ! $page->is_active]);

        $this->dispatch('toast', message: $page->is_active ? 'Menu page is live.' : 'Menu page hidden.', type: 'success');
    }

    public function delete(int $id, ImageService $images): void
    {
        $page = MenuPage::query()->findOrFail($id);
        $images->delete($page->image_path);
        $page->delete();

        $this->dispatch('toast', message: 'Menu page deleted.', type: 'success');
    }

    public function render()
    {
        $pages = MenuPage::query()
            ->when($this->search !== '', function ($query) {
                $term = '%'.$this->search.'%';
                $query->where('title', 'like', $term)
                    ->orWhere('alt_text', 'like', $term);
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate($this->perPage);

        return view('livewire.admin.menu-manager', [
            'pages' => $pages,
        ]);
    }
}
