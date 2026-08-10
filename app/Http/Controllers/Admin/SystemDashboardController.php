<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Environment\EnvironmentValidationService;
use App\Services\Health\HealthCheckService;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SystemDashboardController extends Controller
{
    public function index(EnvironmentValidationService $environment, HealthCheckService $health): View
    {
        return view('admin.system.index', ['environment' => $environment->validate(false, true, true), 'health' => $health->summary(), 'failedJobs' => DB::table('failed_jobs')->latest()->limit(20)->get()]);
    }

    public function jobs(): View
    {
        return view('admin.system.jobs', ['jobs' => DB::table('failed_jobs')->latest()->paginate(25)]);
    }
}
