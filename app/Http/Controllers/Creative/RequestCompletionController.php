<?php

namespace App\Http\Controllers\Creative;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompleteCreativeRequestRequest;
use App\Models\CreativeRequest;
use App\Services\Deliverables\RequestCompletionService;
use Illuminate\Http\RedirectResponse;

class RequestCompletionController extends Controller
{
    public function __invoke(CompleteCreativeRequestRequest $request, CreativeRequest $creativeRequest, RequestCompletionService $completion): RedirectResponse
    {
        $completion->complete($creativeRequest, $request->user());

        return back()->with('status', 'La solicitud fue finalizada.');
    }
}
