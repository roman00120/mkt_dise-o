<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProvideRequestedInformationRequest;
use App\Models\CreativeRequest;
use App\Services\Requests\RequestInformationService;
use Illuminate\Http\RedirectResponse;

class RequestInformationResponseController extends Controller
{
    public function __invoke(ProvideRequestedInformationRequest $request, CreativeRequest $creativeRequest, RequestInformationService $information): RedirectResponse
    {
        $open = $creativeRequest->informationRequests()->where('status', 'open')->latest('requested_at')->firstOrFail();
        $information->provide($open, $request->user(), $request->validated('response'));

        return back()->with('status', 'La información fue enviada al equipo creativo.');
    }
}
