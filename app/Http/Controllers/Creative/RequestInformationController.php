<?php

namespace App\Http\Controllers\Creative;

use App\Http\Controllers\Controller;
use App\Http\Requests\RequestInformationRequest;
use App\Models\CreativeRequest;
use App\Services\Requests\RequestInformationService;
use Illuminate\Http\RedirectResponse;

class RequestInformationController extends Controller
{
    public function __invoke(RequestInformationRequest $request, CreativeRequest $creativeRequest, RequestInformationService $information): RedirectResponse
    {
        $information->request($creativeRequest, $request->user(), $request->validated('message'), $request->validated('category'));

        return back()->with('status', 'Se solicitó información a Marketing.');
    }
}
