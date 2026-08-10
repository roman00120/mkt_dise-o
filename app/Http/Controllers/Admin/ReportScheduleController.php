<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportScheduleRequest;
use App\Models\ReportSchedule;
use App\Services\Audit\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReportScheduleController extends Controller
{
    public function index(): View
    {
        return view('reports.schedules', ['schedules' => ReportSchedule::where('user_id', request()->user()->id)->latest()->get()]);
    }

    public function store(ReportScheduleRequest $request, AuditLogService $audit): RedirectResponse
    {
        $schedule = ReportSchedule::create(['user_id' => $request->user()->id, 'next_run_at' => now()->addDay()] + $request->validated());
        $audit->record('report_schedule_created', $request->user(), $schedule);

        return back()->with('status', 'Programación creada.');
    }

    public function update(ReportScheduleRequest $request, ReportSchedule $reportSchedule, AuditLogService $audit): RedirectResponse
    {
        abort_unless($reportSchedule->user_id === $request->user()->id, 403);
        $reportSchedule->update($request->validated());
        $audit->record('report_schedule_updated', $request->user(), $reportSchedule);

        return back();
    }

    public function activate(ReportSchedule $reportSchedule): RedirectResponse
    {
        abort_unless($reportSchedule->user_id === request()->user()->id, 403);
        $reportSchedule->update(['is_active' => true]);

        return back();
    }

    public function deactivate(ReportSchedule $reportSchedule): RedirectResponse
    {
        abort_unless($reportSchedule->user_id === request()->user()->id, 403);
        $reportSchedule->update(['is_active' => false]);

        return back();
    }
}
