<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\CancelCreativeRequestRequest;
use App\Models\CreativeRequest;
use App\Services\Requests\RequestCancellationService;
use Illuminate\Http\RedirectResponse;

class RequestCancellationController extends Controller
{
    public function __invoke(CancelCreativeRequestRequest $request, CreativeRequest $creativeRequest, RequestCancellationService $cancellation): RedirectResponse
    {
        $this->authorize('cancel', $creativeRequest);
        $cancellation->cancel($creativeRequest, $request->user(), $request->validated('reason'));

        return redirect()->route('app.requests.show', $creativeRequest)->with('status', 'La solicitud fue cancelada.');
    }
}
