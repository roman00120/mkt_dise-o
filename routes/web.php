<?php

use App\Http\Controllers\App\DashboardController;
use App\Http\Controllers\App\DraftController;
use App\Http\Controllers\App\RequestFileController;
use App\Http\Controllers\App\RequestSubmissionController;
use App\Http\Controllers\App\RequestWizardController;
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
        Route::get('/app/requests/create', [RequestWizardController::class, 'create'])->middleware('role:marketing')->name('app.requests.create');
        Route::post('/app/requests', [RequestWizardController::class, 'store'])->middleware('role:marketing')->name('app.requests.store');
        Route::get('/app/requests/drafts', [DraftController::class, 'index'])->middleware('role:marketing')->name('app.requests.drafts.index');
        Route::get('/app/requests/drafts/{creativeRequest}/edit', [RequestWizardController::class, 'edit'])->middleware('role:marketing')->name('app.requests.drafts.edit');
        Route::patch('/app/requests/drafts/{creativeRequest}', [RequestWizardController::class, 'update'])->middleware('role:marketing')->name('app.requests.drafts.update');
        Route::post('/app/requests/drafts/{creativeRequest}/autosave', [RequestWizardController::class, 'autosave'])->middleware('role:marketing')->name('app.requests.drafts.autosave');
        Route::post('/app/requests/drafts/{creativeRequest}/files', [RequestFileController::class, 'store'])->middleware('role:marketing')->name('app.requests.drafts.files.store');
        Route::delete('/app/requests/drafts/{creativeRequest}/files/{file}', [RequestFileController::class, 'destroy'])->middleware('role:marketing')->name('app.requests.drafts.files.destroy');
        Route::post('/app/requests/drafts/{creativeRequest}/submit', [RequestSubmissionController::class, 'submit'])->middleware('role:marketing')->name('app.requests.drafts.submit');
        Route::get('/app/requests/{creativeRequest}/confirmation', [RequestSubmissionController::class, 'confirmation'])->name('app.requests.confirmation');
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
