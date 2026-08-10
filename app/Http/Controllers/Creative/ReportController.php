<?php

namespace App\Http\Controllers\Creative;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportFilterRequest;
use App\Services\Analytics\ReportMetricsService;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function operations(ReportFilterRequest $request, ReportMetricsService $reports): View
    {
        abort_unless($request->user()->hasRole('supervisor'), 403);

        return view('reports.operations', ['layout' => 'layouts.creative', 'title' => 'Analítica operativa', 'header' => 'Analítica operativa', 'report' => $reports->operations($request->user(), $request->filters())]);
    }

    public function workload(ReportFilterRequest $request, ReportMetricsService $reports): View
    {
        abort_unless($request->user()->hasRole('supervisor'), 403);

        return view('reports.simple', ['layout' => 'layouts.creative', 'title' => 'Carga del equipo', 'header' => 'Carga del equipo', 'metrics' => $reports->operations($request->user(), $request->filters())['workload']]);
    }

    public function sla(ReportFilterRequest $request, ReportMetricsService $reports): View
    {
        abort_unless($request->user()->hasRole('supervisor'), 403);

        return view('reports.simple', ['layout' => 'layouts.creative', 'title' => 'SLA', 'header' => 'SLA operativo', 'metrics' => $reports->operations($request->user(), $request->filters())]);
    }

    public function mine(ReportFilterRequest $request, ReportMetricsService $reports): View
    {
        abort_unless($request->user()->hasRole('creative', 'design', 'video', 'render'), 403);

        return view('reports.dashboard', ['layout' => 'layouts.creative', 'title' => 'Mis métricas', 'header' => 'Mis métricas', 'report' => $reports->dashboard($request->user(), $request->filters()), 'mode' => 'personal']);
    }
}
