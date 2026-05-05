<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\PictureController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Api\CartApiController;
use App\Http\Controllers\Api\FavoriteApiController;
use App\Http\Controllers\Api\PictureApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\ProfileApiController;
use App\Http\Controllers\Api\AuctionApiController;

/* |-------------------------------------------------------------------------- | Публичные маршруты |-------------------------------------------------------------------------- */

Route::get('/', [MainController::class , 'index'])->name('main');
Route::get('/main', [MainController::class , 'index']);
Route::get('/gallery', [GalleryController::class , 'index'])->name('gallery');
Route::get('/auction', [AuctionController::class , 'index'])->name('auction');
Route::get('/picture/{id}', [PictureController::class , 'show'])->name('picture.show');

// Авторизация
Route::get('/auth', [AuthController::class , 'showLogin'])->name('auth.login');
Route::post('/auth', [AuthController::class , 'login']);
Route::get('/reg', [AuthController::class , 'showRegister'])->name('auth.register');
Route::post('/reg', [AuthController::class , 'register']);
Route::get('/logout', [AuthController::class , 'logout'])->name('auth.logout');

/* |-------------------------------------------------------------------------- | Защищённые маршруты (авторизованные пользователи) |-------------------------------------------------------------------------- */

Route::middleware('auth.custom')->group(function () {
    Route::get('/cart', [CartController::class , 'index'])->name('cart');
    Route::get('/fav', [FavoriteController::class , 'index'])->name('favorites');
    Route::get('/orders', [OrderController::class , 'index'])->name('orders');
    Route::get('/account', [AccountController::class , 'index'])->name('account');
    Route::get('/add', [PictureController::class , 'create'])->name('picture.create');
    Route::get('/edit/{id}', [PictureController::class , 'edit'])->name('picture.edit');
    Route::get('/checkout', [CheckoutController::class , 'index'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class , 'process']);

    // API маршруты
    Route::post('/api/cart', [CartApiController::class , 'handle']);
    Route::post('/api/favorites', [FavoriteApiController::class , 'handle']);
    Route::post('/api/picture/add', [PictureApiController::class , 'store']);
    Route::post('/api/picture/edit', [PictureApiController::class , 'update']);
    Route::post('/api/picture/delete', [PictureApiController::class , 'destroy']);
    Route::post('/api/auction/bid', [AuctionApiController::class , 'bid']);
    Route::post('/api/auction/buyout', [AuctionApiController::class , 'buyout']);
    Route::post('/api/profile/update', [ProfileApiController::class , 'update']);
});

/* |-------------------------------------------------------------------------- | Админские маршруты |-------------------------------------------------------------------------- */

Route::middleware(['auth.custom', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class , 'index'])->name('admin');
    Route::post('/api/picture/moderate', [PictureApiController::class , 'moderate']);
    Route::post('/api/categories', [CategoryApiController::class , 'handle']);
});
