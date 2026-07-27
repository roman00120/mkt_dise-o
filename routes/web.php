<?php

use App\Http\Controllers\App\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/design-system', function () {
    abort_unless(config('app.env') === 'local', 404);

    return view('design-system.index');
})->name('design-system.index');

Route::middleware(['auth', 'active'])->group(function (): void {
    Route::get('/app', DashboardController::class)->middleware('role:marketing,supervisor,admin')->name('app.dashboard');
    Route::middleware('role:marketing,supervisor,admin')->group(function (): void {
        Route::view('/app/requests/create', 'placeholders.app-module')->name('app.requests.create');
        Route::view('/app/requests', 'placeholders.app-module')->name('app.requests.index');
        Route::view('/app/requests/{request}', 'placeholders.app-module')->name('app.requests.show');
        Route::view('/app/profile', 'placeholders.app-module')->name('app.profile');
        Route::view('/app/notifications', 'placeholders.app-module')->name('app.notifications');
    });
    Route::view('/creative', 'placeholders.creative')->middleware('role:supervisor')->name('creative.dashboard');
    Route::view('/creative/design', 'placeholders.creative')->middleware('role:design')->name('creative.design.dashboard');
    Route::view('/creative/video', 'placeholders.creative')->middleware('role:video')->name('creative.video.dashboard');
    Route::view('/creative/render', 'placeholders.creative')->middleware('role:render')->name('creative.render.dashboard');
    Route::view('/admin', 'placeholders.admin')->middleware('role:admin')->name('admin.dashboard');
});

require __DIR__.'/auth.php';
