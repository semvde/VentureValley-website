<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OpeningHourExceptionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\RideController;
use App\Http\Controllers\ShowController;
use App\Http\Controllers\SouvenirController;
use Illuminate\Support\Facades\Route;

Route::permanentRedirect('/dashboard', '/')
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/', [HomeController::class, 'show'])
    ->name('home');

Route::resource('attracties', RideController::class)
    ->only(['index', 'show'])
    ->parameters(['attracties' => 'ride']);

Route::resource('parkshows', ShowController::class)
    ->only(['index', 'show'])
    ->parameters(['parkshows' => 'show']);

Route::resource('restaurants', RestaurantController::class)
    ->only(['index', 'show'])
    ->parameters(['restaurants' => 'restaurant']);

Route::resource('souvenirs', SouvenirController::class)
    ->only(['index', 'show'])
    ->parameters(['souvenirs' => 'souvenir']);

Route::get('/bezoeken', [HomeController::class, 'bezoeken'])
    ->name('bezoeken');

Route::get('/openingstijden', [HomeController::class, 'openingstijden'])
    ->name('openingstijden');

Route::get('/privacybeleid', [HomeController::class, 'privacy'])
    ->name('privacy');

Route::get('/parkreglement', [HomeController::class, 'parkreglement'])
    ->name('parkreglement');

Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])
        ->name('index');

    Route::get('attracties', [AdminController::class, 'rides'])
        ->name('attracties.index');

    Route::resource('attracties', RideController::class)
        ->except(['index'])
        ->parameters(['attracties' => 'ride']);

    Route::get('parkshows', [AdminController::class, 'shows'])
        ->name('parkshows.index');

    Route::resource('parkshows', ShowController::class)
        ->except(['index'])
        ->parameters(['parkshows' => 'show']);

    Route::get('restaurants', [AdminController::class, 'restaurants'])
        ->name('restaurants.index');

    Route::resource('restaurants', RestaurantController::class)
        ->except(['index'])
        ->parameters(['restaurants' => 'restaurant']);

    Route::get('souvenirs', [AdminController::class, 'souvenirs'])
        ->name('souvenirs.index');

    Route::resource('souvenirs', SouvenirController::class)
        ->except(['index'])
        ->parameters(['souvenirs' => 'souvenir']);

    Route::resource('openingstijden', OpeningHourExceptionController::class)
        ->except(['show'])
        ->parameters(['openingstijden' => 'exception']);
});

require __DIR__ . '/auth.php';
