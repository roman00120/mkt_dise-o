<?php

namespace App\Http\Controllers\Creative;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateInternalDueDateRequest;
use App\Models\CreativeRequest;
use App\Services\Requests\RequestOperationalService;
use Illuminate\Http\RedirectResponse;

class InternalDueDateController extends Controller
{
    public function __invoke(UpdateInternalDueDateRequest $request, CreativeRequest $creativeRequest, RequestOperationalService $operations): RedirectResponse
    {
        $operations->internalDate($creativeRequest, $request->user(), $request->validated('internal_due_date'));

        return back()->with('status', 'La fecha interna fue actualizada.');
    }
}
