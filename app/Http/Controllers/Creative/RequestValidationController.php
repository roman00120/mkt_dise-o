<?php

namespace App\Http\Controllers\Creative;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateCreativeRequestRequest;
use App\Models\CreativeRequest;
use App\Models\User;
use App\Notifications\CreativeRequestAssignedNotification;
use App\Services\Requests\RequestAssignmentService;
use App\Services\Requests\RequestTransitionService;
use App\Services\Requests\RequestValidationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;

class RequestValidationController extends Controller
{
    public function __invoke(ValidateCreativeRequestRequest $request, CreativeRequest $creativeRequest, RequestValidationService $validation, RequestAssignmentService $assignment, RequestTransitionService $transition): RedirectResponse
    {
        $validation->validate($creativeRequest);
        $creativeRequest->update(['validated_by' => $request->user()->id, 'validated_at' => now()]);
        $assignee = User::findOrFail($request->validated('assignee_id'));
        $assignment->assign($creativeRequest, $assignee, $request->user(), $request->validated('observation'));
        if ($priority = $request->validated('operational_priority')) {
            $creativeRequest->update(['operational_priority' => $priority]);
        }
        if ($date = $request->validated('internal_due_date')) {
            $creativeRequest->update(['internal_due_date' => $date]);
        }
        $transition->transition($creativeRequest->fresh(), $request->user(), 'assigned');
        $assigned = $creativeRequest->fresh(['requester', 'assignee', 'files']);
        Notification::send($assignee, new CreativeRequestAssignedNotification($assigned, $request->user()));

        return back()->with('status', 'La solicitud fue validada y asignada.');
    }
}
