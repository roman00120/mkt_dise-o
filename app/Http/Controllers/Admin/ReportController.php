<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportFilterRequest;
use App\Services\Analytics\ReportMetricsService;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(ReportFilterRequest $request, ReportMetricsService $reports): View
    {
        return view('reports.dashboard', ['layout' => 'layouts.admin', 'title' => 'Reportes', 'header' => 'Reportes y analítica', 'report' => $reports->dashboard($request->user(), $request->filters()), 'mode' => 'executive']);
    }

    public function executive(ReportFilterRequest $request, ReportMetricsService $reports): View
    {
        return view('reports.dashboard', ['layout' => 'layouts.admin', 'title' => 'Resumen ejecutivo', 'header' => 'Resumen ejecutivo', 'report' => $reports->dashboard($request->user(), $request->filters()), 'mode' => 'executive']);
    }

    public function operations(ReportFilterRequest $request, ReportMetricsService $reports): View
    {
        return view('reports.operations', ['layout' => 'layouts.admin', 'title' => 'Operación creativa', 'header' => 'Operación creativa', 'report' => $reports->operations($request->user(), $request->filters())]);
    }

    public function requests(ReportFilterRequest $request, ReportMetricsService $reports): View
    {
        return view('reports.requests', ['layout' => 'layouts.admin', 'title' => 'Métricas de solicitudes', 'header' => 'Métricas de solicitudes', 'requests' => $reports->detail($request->user(), $request->filters()), 'filters' => $request->filters()]);
    }

    public function deliverables(ReportFilterRequest $request, ReportMetricsService $reports): View
    {
        return view('reports.simple', ['layout' => 'layouts.admin', 'title' => 'Entregables', 'header' => 'Reportes de entregables', 'metrics' => $reports->deliverables($request->user(), $request->filters())]);
    }

    public function corrections(ReportFilterRequest $request, ReportMetricsService $reports): View
    {
        return view('reports.simple', ['layout' => 'layouts.admin', 'title' => 'Correcciones', 'header' => 'Reportes de correcciones', 'metrics' => $reports->corrections($request->user(), $request->filters())]);
    }

    public function sla(ReportFilterRequest $request, ReportMetricsService $reports): View
    {
        return view('reports.simple', ['layout' => 'layouts.admin', 'title' => 'SLA', 'header' => 'Cumplimiento de SLA', 'metrics' => $reports->operations($request->user(), $request->filters())]);
    }

    public function workload(ReportFilterRequest $request, ReportMetricsService $reports): View
    {
        return view('reports.simple', ['layout' => 'layouts.admin', 'title' => 'Carga de trabajo', 'header' => 'Carga de trabajo', 'metrics' => $reports->operations($request->user(), $request->filters())['workload']]);
    }
}
