<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;

class FailedJobController extends Controller
{
    public function retry(string $uuid): RedirectResponse
    {
        Artisan::call('queue:retry', [$uuid]);

        return back()->with('status', 'Job reenviado a la cola.');
    }

    public function forget(string $uuid): RedirectResponse
    {
        Artisan::call('queue:forget', [$uuid]);

        return back()->with('status', 'Job eliminado del registro de fallos.');
    }
}
