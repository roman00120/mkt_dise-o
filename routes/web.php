<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/design-system', function () {
    abort_unless(config('app.env') === 'local', 404);

    return view('design-system.index');
})->name('design-system.index');

Route::middleware(['auth', 'active'])->group(function (): void {
    Route::view('/app', 'placeholders.app')->middleware('role:marketing')->name('app.dashboard');
    Route::view('/creative', 'placeholders.creative')->middleware('role:supervisor')->name('creative.dashboard');
    Route::view('/creative/design', 'placeholders.creative')->middleware('role:design')->name('creative.design.dashboard');
    Route::view('/creative/video', 'placeholders.creative')->middleware('role:video')->name('creative.video.dashboard');
    Route::view('/creative/render', 'placeholders.creative')->middleware('role:render')->name('creative.render.dashboard');
    Route::view('/admin', 'placeholders.admin')->middleware('role:admin')->name('admin.dashboard');
});

require __DIR__.'/auth.php';
