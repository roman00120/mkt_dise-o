<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SavedReportViewRequest;
use App\Models\SavedReportView;
use App\Services\Audit\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SavedReportViewController extends Controller
{
    public function index(): View
    {
        return view('reports.saved-views', ['views' => SavedReportView::where('user_id', request()->user()->id)->latest()->get()]);
    }

    public function store(SavedReportViewRequest $request, AuditLogService $audit): RedirectResponse
    {
        $view = SavedReportView::create(['user_id' => $request->user()->id] + $request->validated());
        $audit->record('report_saved_view_created', $request->user(), $view);

        return back()->with('status', 'Vista guardada.');
    }

    public function update(SavedReportViewRequest $request, SavedReportView $savedView, AuditLogService $audit): RedirectResponse
    {
        abort_unless($savedView->user_id === $request->user()->id, 403);
        $savedView->update($request->validated());
        $audit->record('report_saved_view_updated', $request->user(), $savedView);

        return back()->with('status', 'Vista actualizada.');
    }

    public function destroy(SavedReportView $savedView, AuditLogService $audit): RedirectResponse
    {
        abort_unless($savedView->user_id === request()->user()->id, 403);
        $savedView->delete();
        $audit->record('report_saved_view_deleted', request()->user(), $savedView);

        return back()->with('status', 'Vista eliminada.');
    }
}
