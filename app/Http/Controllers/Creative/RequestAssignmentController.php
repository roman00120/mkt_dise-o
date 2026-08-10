<?php

namespace App\Http\Controllers\Creative;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignCreativeRequestRequest;
use App\Models\CreativeRequest;
use App\Models\User;
use App\Services\Requests\RequestAssignmentService;
use App\Services\Requests\RequestTransitionService;
use Illuminate\Http\RedirectResponse;

class RequestAssignmentController extends Controller
{
    public function __invoke(AssignCreativeRequestRequest $request, CreativeRequest $creativeRequest, RequestAssignmentService $assignment, RequestTransitionService $transition): RedirectResponse
    {
        $assignee = User::findOrFail($request->validated('assignee_id'));
        $assignment->assign($creativeRequest, $assignee, $request->user(), $request->validated('observation'));
        $creativeRequest->update(array_filter(['operational_priority' => $request->validated('operational_priority'), 'internal_due_date' => $request->validated('internal_due_date')]));
        if ($creativeRequest->status->value === 'in_validation') {
            $transition->transition($creativeRequest->fresh(), $request->user(), 'assigned');
        }

        return back()->with('status', 'La solicitud fue asignada.');
    }
}
