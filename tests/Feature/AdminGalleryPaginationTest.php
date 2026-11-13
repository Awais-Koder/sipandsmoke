<?php

namespace Tests\Feature;

use App\Livewire\Admin\GalleryManager;
use App\Models\GalleryPhoto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminGalleryPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_gallery_pagination_moves_to_the_second_page(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        foreach (range(1, 13) as $index) {
            GalleryPhoto::query()->create([
                'caption' => "Photograph {$index}",
                'alt_text' => "Photograph {$index} alt",
                'category' => 'Kitchen',
                'image_path' => "gallery/photo-{$index}.webp",
                'sort_order' => $index,
                'is_active' => true,
            ]);
        }

        $this->actingAs($admin);

        Livewire::test(GalleryManager::class)
            ->assertSee('wire:click.prevent="gotoPage(2, \'page\')"', false)
            ->assertSee('wire:key="gallery-photo-1"', false)
            ->assertDontSee('wire:key="gallery-photo-13"', false)
            ->call('gotoPage', 2, 'page')
            ->assertSet('paginators.page', 2)
            ->assertSee('wire:key="gallery-photo-13"', false)
            ->assertDontSee('wire:key="gallery-photo-1"', false);
    }
}
