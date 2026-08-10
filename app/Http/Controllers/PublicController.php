<?php

namespace App\Http\Controllers;

use App\Services\Auth\LoginRedirectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function home(Request $request, LoginRedirectService $redirects): RedirectResponse
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        return redirect()->to($redirects->pathFor($request->user()));
    }

    public function designSystem(): View
    {
        abort_unless(config('app.env') === 'local', 404);

        return view('design-system.index');
    }
}
