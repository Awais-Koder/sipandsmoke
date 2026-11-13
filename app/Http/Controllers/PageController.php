<?php

namespace App\Http\Controllers;

use App\Models\GalleryPhoto;
use App\Models\MenuPage;
use App\Models\Product;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        $menuPages = MenuPage::query()->active()->ordered()->get();
        $products = Product::query()->active()->ordered()->get();

        return view('site.home', [
            'title' => site('seo_home_title'),
            'description' => site('seo_home_description'),
            'canonical' => route('home'),
            'current' => 'home',
            'ogImage' => site('seo_og_image') ?: site('home_hero_image'),
            'preloadImage' => site('home_hero_image'),
            'menuPages' => $menuPages,
            'products' => $products,
        ]);
    }

    public function menu(): View
    {
        $pages = MenuPage::query()->active()->ordered()->get();

        return view('site.menu', [
            'title' => site('seo_menu_title'),
            'description' => site('seo_menu_description'),
            'canonical' => route('menu'),
            'current' => 'menu',
            'ogImage' => $pages->first()?->image_path ?? site('seo_og_image'),
            'preloadImage' => $pages->first()?->image_path,
            'pages' => $pages,
        ]);
    }

    public function gallery(): View
    {
        $photos = GalleryPhoto::query()->active()->ordered()->get();

        return view('site.gallery', [
            'title' => site('seo_gallery_title'),
            'description' => site('seo_gallery_description'),
            'canonical' => route('gallery'),
            'current' => 'gallery',
            'ogImage' => $photos->first()?->image_path ?? site('seo_og_image'),
            'photos' => $photos,
        ]);
    }
}
