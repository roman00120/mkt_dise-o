<?php

namespace App\Http\Controllers\Creative;

use App\Enums\RequestPriority;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOperationalPriorityRequest;
use App\Models\CreativeRequest;
use App\Services\Requests\RequestOperationalService;
use Illuminate\Http\RedirectResponse;

class OperationalPriorityController extends Controller
{
    public function __invoke(UpdateOperationalPriorityRequest $request, CreativeRequest $creativeRequest, RequestOperationalService $operations): RedirectResponse
    {
        $operations->priority($creativeRequest, $request->user(), RequestPriority::from($request->validated('operational_priority')));

        return back()->with('status', 'La prioridad operativa fue actualizada.');
    }
}
