<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\PageController;
use App\Livewire\Admin\ContentManager;
use App\Livewire\Admin\GalleryManager;
use App\Livewire\Admin\MenuManager;
use App\Livewire\Admin\ProductManager;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/menu', [PageController::class, 'menu'])->name('menu');
Route::get('/gallery', [PageController::class, 'gallery'])->name('gallery');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('two-factor', [AuthController::class, 'showTwoFactor'])->name('two-factor.show');
    Route::post('two-factor', [AuthController::class, 'verifyTwoFactor'])->name('two-factor.verify');
    Route::post('two-factor/cancel', [AuthController::class, 'cancelTwoFactor'])->name('two-factor.cancel');
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('content', ContentManager::class)->name('content');
        Route::get('menu', MenuManager::class)->name('menu');
        Route::get('gallery', GalleryManager::class)->name('gallery');
        Route::get('products', ProductManager::class)->name('products');
        Route::get('account', [AccountController::class, 'edit'])->name('account');
        Route::put('account', [AccountController::class, 'update'])->name('account.update');
    });
});
