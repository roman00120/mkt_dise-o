<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/design-system', function () {
    abort_unless(config('app.env') === 'local', 404);

    return view('design-system.index');
})->name('design-system.index');
