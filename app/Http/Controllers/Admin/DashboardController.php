<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryPhoto;
use App\Models\MenuPage;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'menuCount' => MenuPage::query()->count(),
            'galleryCount' => GalleryPhoto::query()->count(),
            'productCount' => Product::query()->count(),
            'activeMenuCount' => MenuPage::query()->where('is_active', true)->count(),
            'activeGalleryCount' => GalleryPhoto::query()->where('is_active', true)->count(),
            'activeProductCount' => Product::query()->where('is_active', true)->count(),
            'recentPhotos' => GalleryPhoto::query()->latest()->limit(6)->get(),
        ]);
    }
}
