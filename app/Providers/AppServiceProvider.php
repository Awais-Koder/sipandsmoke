<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $root = rtrim(config('app.url'), '/');
        $path = trim((string) parse_url($root, PHP_URL_PATH), '/');

        URL::forceRootUrl($root);

        if ($path !== '') {
            Livewire::setUpdateRoute(fn ($handle) => Route::post("/{$path}/livewire/update", $handle)->middleware('web'));
            Livewire::setScriptRoute(fn ($handle) => Route::get("/{$path}/livewire/livewire.min.js", $handle));
        }

        require_once app_path('Support/helpers.php');
    }
}
