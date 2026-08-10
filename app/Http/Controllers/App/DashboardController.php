<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\MarketingDashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, MarketingDashboardService $dashboard): View
    {
        $filter = $request->string('filter', 'all')->toString();
        $allowedFilters = ['all', 'pending', 'in-progress', 'review', 'completed'];

        return view('app.dashboard', array_merge(
            $dashboard->forUser(in_array($filter, $allowedFilters, true) ? $filter : 'all', $request->user()),
            []
        ));
    }
}
