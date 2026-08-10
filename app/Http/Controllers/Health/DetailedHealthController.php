<?php

namespace App\Http\Controllers\Health;

use App\Http\Controllers\Controller;
use App\Services\Health\HealthCheckService;
use Illuminate\Http\JsonResponse;

class DetailedHealthController extends Controller
{
    public function __invoke(HealthCheckService $health): JsonResponse
    {
        abort_unless(request()->user()?->isActive(), 403);

        return response()->json($health->summary());
    }
}
