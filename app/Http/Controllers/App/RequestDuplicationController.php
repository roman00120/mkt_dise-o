<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\CreativeRequest;
use App\Services\Requests\RequestDuplicationService;
use Illuminate\Http\RedirectResponse;

class RequestDuplicationController extends Controller
{
    public function __invoke(CreativeRequest $creativeRequest, RequestDuplicationService $duplication): RedirectResponse
    {
        $this->authorize('duplicate', $creativeRequest);
        $copy = $duplication->duplicate($creativeRequest, request()->user());

        return redirect()->route('app.requests.drafts.edit', $copy)->with('status', 'Se creó un borrador a partir de la solicitud original.');
    }
}
