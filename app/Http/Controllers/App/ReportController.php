<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportFilterRequest;
use App\Services\Analytics\ReportMetricsService;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(ReportFilterRequest $request, ReportMetricsService $reports): View
    {
        abort_unless($request->user()->hasRole('marketing'), 403);

        return view('reports.dashboard', ['layout' => 'layouts.app', 'title' => 'Mis reportes', 'header' => 'Mis reportes', 'report' => $reports->dashboard($request->user(), $request->filters()), 'mode' => 'personal']);
    }

    public function requests(ReportFilterRequest $request, ReportMetricsService $reports): View
    {
        abort_unless($request->user()->hasRole('marketing'), 403);

        return view('reports.requests', ['layout' => 'layouts.app', 'title' => 'Reporte de solicitudes', 'header' => 'Reporte de solicitudes', 'requests' => $reports->detail($request->user(), $request->filters()), 'filters' => $request->filters()]);
    }
}
