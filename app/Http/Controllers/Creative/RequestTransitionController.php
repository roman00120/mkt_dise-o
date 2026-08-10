<?php

namespace App\Http\Controllers\Creative;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransitionCreativeRequestRequest;
use App\Models\CreativeRequest;
use App\Services\Requests\RequestTransitionService;
use Illuminate\Http\RedirectResponse;

class RequestTransitionController extends Controller
{
    public function __invoke(TransitionCreativeRequestRequest $request, CreativeRequest $creativeRequest, RequestTransitionService $transition): RedirectResponse
    {
        $transition->transition($creativeRequest, $request->user(), $request->validated('status'));

        return back()->with('status', 'El estado de la solicitud fue actualizado.');
    }
}
