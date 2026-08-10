<?php

namespace App\Http\Controllers\Creative;

use App\Http\Requests\ReassignCreativeRequestRequest;
use App\Models\CreativeRequest;
use App\Models\User;
use App\Services\Requests\RequestAssignmentService;
use Illuminate\Http\RedirectResponse;

class RequestReassignmentController
{
    public function __invoke(ReassignCreativeRequestRequest $request, CreativeRequest $creativeRequest, RequestAssignmentService $assignment): RedirectResponse
    {
        $assignee = User::findOrFail($request->validated('assignee_id'));
        $assignment->assign($creativeRequest, $assignee, $request->user(), $request->validated('reason'));

        return back()->with('status', 'La solicitud fue reasignada.');
    }
}
