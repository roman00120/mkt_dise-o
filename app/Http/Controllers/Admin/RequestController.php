<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequestIndexRequest;
use App\Models\CreativeRequest;
use App\Queries\AdminRequestQuery;
use App\Services\Audit\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RequestController extends Controller
{
    public function index(AdminRequestIndexRequest $request, AdminRequestQuery $query): View
    {
        return view('admin.requests.index', ['requests' => $query->paginate($request->validated()), 'filters' => $request->validated()]);
    }

    public function show(CreativeRequest $creativeRequest): View
    {
        return view('admin.requests.show', ['requestModel' => $creativeRequest->load(['requester', 'assignee', 'events.actor', 'deliverables'])]);
    }

    public function destroy(CreativeRequest $creativeRequest, AuditLogService $audit): RedirectResponse
    {
        $snapshot = ['folio' => $creativeRequest->folio, 'title' => $creativeRequest->title, 'status' => $creativeRequest->status->value];
        $creativeRequest->delete();
        $audit->record('request.deleted', request()->user(), $creativeRequest, $creativeRequest->requester, $snapshot);

        return redirect()->route('admin.requests.index')->with('status', 'La solicitud fue eliminada del listado operativo.');
    }
}
