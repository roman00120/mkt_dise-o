<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SlaPolicyRequest;
use App\Models\SlaPolicy;
use App\Services\Audit\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SlaPolicyController extends Controller
{
    public function index(): View
    {
        return view('reports.sla-policies', ['policies' => SlaPolicy::with('requestType')->latest()->paginate(25)]);
    }

    public function store(SlaPolicyRequest $request, AuditLogService $audit): RedirectResponse
    {
        $policy = SlaPolicy::create($request->validated() + ['uuid' => (string) Str::uuid(), 'created_by' => $request->user()->id, 'updated_by' => $request->user()->id]);
        $audit->record('sla_policy_created', $request->user(), $policy);

        return back()->with('status', 'Política SLA creada.');
    }

    public function update(SlaPolicyRequest $request, SlaPolicy $slaPolicy, AuditLogService $audit): RedirectResponse
    {
        $slaPolicy->update($request->validated() + ['updated_by' => $request->user()->id]);
        $audit->record('sla_policy_updated', $request->user(), $slaPolicy);

        return back()->with('status', 'Política SLA actualizada.');
    }

    public function toggle(SlaPolicy $slaPolicy): RedirectResponse
    {
        $slaPolicy->update(['is_active' => ! $slaPolicy->is_active, 'updated_by' => request()->user()->id]);

        return back();
    }
}
