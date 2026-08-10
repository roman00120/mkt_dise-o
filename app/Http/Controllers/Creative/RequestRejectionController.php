<?php

namespace App\Http\Controllers\Creative;

use App\Http\Controllers\Controller;
use App\Http\Requests\RejectCreativeRequestRequest;
use App\Models\CreativeRequest;
use App\Services\Requests\RequestTransitionService;
use Illuminate\Http\RedirectResponse;

class RequestRejectionController extends Controller
{
    public function __invoke(RejectCreativeRequestRequest $request, CreativeRequest $creativeRequest, RequestTransitionService $transition): RedirectResponse
    {
        $transition->transition($creativeRequest, $request->user(), 'rejected');
        $creativeRequest->events()->latest('id')->first()->update(['metadata' => ['category' => $request->validated('category'), 'reason' => $request->validated('reason')]]);

        return back()->with('status', 'La solicitud fue rechazada.');
    }
}
