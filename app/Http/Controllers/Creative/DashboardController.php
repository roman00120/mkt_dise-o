<?php

namespace App\Http\Controllers\Creative;

use App\Http\Controllers\Controller;
use App\Models\CreativeRequest;
use App\Services\Dashboard\CreativeDashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, CreativeDashboardService $dashboard): View
    {
        $this->authorize('viewCreativePanel', CreativeRequest::class);

        return view('creative.dashboard', $dashboard->forUser($request->user()));
    }
}
