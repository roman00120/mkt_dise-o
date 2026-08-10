<?php

namespace App\Http\Controllers\Creative;

use App\Http\Controllers\Controller;
use App\Models\CreativeRequest;
use App\Services\Workload\CreativeWorkloadService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkloadController extends Controller
{
    public function __invoke(Request $request, CreativeWorkloadService $workload): View
    {
        $this->authorize('viewWorkload', CreativeRequest::class);

        return view('creative.workload', ['workload' => $workload->forUser($request->user())]);
    }
}
