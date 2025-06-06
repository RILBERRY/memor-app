<?php

use App\Http\Controllers\PostGenerateDataController;
use App\Http\Middleware\IsUsersPost;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;


    Route::get('/', [PostGenerateDataController::class, 'home'])->name('home');

    Route::post('/create-post-new', [PostGenerateDataController::class, 'storeAndCreateNew'])->name('create-post-new');



Route::middleware(['auth', IsUsersPost::class])->group(function () {
    Route::get('/generate-post/{id}', [PostGenerateDataController::class, 'generateFromSearchImg'])->name('create-post');
    Route::get('/dashboard', [PostGenerateDataController::class, 'dashboard'])->name('dashboard');
});

Route::middleware(['auth'])->group(function () {

    Route::post('/create-post', [PostGenerateDataController::class, 'storeAndCreate'])->name('create-post');

    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
