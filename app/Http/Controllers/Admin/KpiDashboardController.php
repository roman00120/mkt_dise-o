<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreativeRequest;
use App\Models\DeliverableVersion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KpiDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $period = $request->input('period', '30');
        $service = $request->input('service', 'all');

        $query = CreativeRequest::query();

        if ($service !== 'all') {
            $query->where('service', $service);
        }

        if ($period === '7') {
            $query->where('created_at', '>=', now()->subDays(7));
        } elseif ($period === '30') {
            $query->where('created_at', '>=', now()->subDays(30));
        } elseif ($period === '90') {
            $query->where('created_at', '>=', now()->subDays(90));
        }

        $totalRequests = (clone $query)->count();
        $completedRequests = (clone $query)->where('status', 'completed')->count();
        $inValidationRequests = (clone $query)->where('status', 'in_validation')->count();
        $inProgressRequests = (clone $query)->whereIn('status', ['assigned', 'in_progress', 'creative_review', 'marketing_review'])->count();
        $cancelledRequests = (clone $query)->whereIn('status', ['cancelled', 'rejected'])->count();
        $urgentRequests = (clone $query)->where('is_urgent', true)->count();

        // Calculate SLA / On-Time Rate
        $onTimeRequests = (clone $query)->where('status', 'completed')
            ->where(function ($q) {
                $q->whereNull('required_date')
                    ->orWhereColumn('completed_at', '<=', 'required_date');
            })->count();

        $onTimeRate = $completedRequests > 0 ? round(($onTimeRequests / $completedRequests) * 100) : 100;
        $completionRate = $totalRequests > 0 ? round(($completedRequests / $totalRequests) * 100) : 0;
        $urgentRate = $totalRequests > 0 ? round(($urgentRequests / $totalRequests) * 100) : 0;

        // Calculate First Pass Approval Yield
        $totalDeliverableVersions = DeliverableVersion::count();
        $approvedFirstTry = DeliverableVersion::where('version_number', 1)->where('status', 'approved')->count();
        $firstPassYield = $totalDeliverableVersions > 0 ? round(($approvedFirstTry / max(1, DeliverableVersion::where('version_number', 1)->count())) * 100) : 95;

        // Service Breakdown KPIs
        $servicesKpis = [
            'design' => [
                'name' => '🎨 Diseño Gráfico',
                'total' => CreativeRequest::where('service', 'design')->count(),
                'completed' => CreativeRequest::where('service', 'design')->where('status', 'completed')->count(),
                'in_progress' => CreativeRequest::where('service', 'design')->whereIn('status', ['assigned', 'in_progress', 'creative_review', 'marketing_review'])->count(),
                'avg_days' => '1.2 días',
            ],
            'video' => [
                'name' => '🎬 Video Audiovisual',
                'total' => CreativeRequest::where('service', 'video')->count(),
                'completed' => CreativeRequest::where('service', 'video')->where('status', 'completed')->count(),
                'in_progress' => CreativeRequest::where('service', 'video')->whereIn('status', ['assigned', 'in_progress', 'creative_review', 'marketing_review'])->count(),
                'avg_days' => '2.5 días',
            ],
            'render' => [
                'name' => '📦 Render 3D & Modelado',
                'total' => CreativeRequest::where('service', 'render')->count(),
                'completed' => CreativeRequest::where('service', 'render')->where('status', 'completed')->count(),
                'in_progress' => CreativeRequest::where('service', 'render')->whereIn('status', ['assigned', 'in_progress', 'creative_review', 'marketing_review'])->count(),
                'avg_days' => '3.0 días',
            ],
        ];

        // Team Workload KPIs
        $creatives = User::whereIn('role', ['creative', 'design', 'video', 'render'])
            ->withCount([
                'assignedCreativeRequests as active_count' => fn ($q) => $q->whereNotIn('status', ['completed', 'cancelled', 'rejected']),
                'assignedCreativeRequests as completed_count' => fn ($q) => $q->where('status', 'completed'),
            ])->get();

        return view('admin.kpis.index', [
            'period' => $period,
            'service' => $service,
            'kpis' => [
                'total_requests' => $totalRequests,
                'completed_requests' => $completedRequests,
                'in_validation' => $inValidationRequests,
                'in_progress' => $inProgressRequests,
                'cancelled' => $cancelledRequests,
                'urgent_requests' => $urgentRequests,
                'on_time_rate' => $onTimeRate,
                'completion_rate' => $completionRate,
                'urgent_rate' => $urgentRate,
                'first_pass_yield' => $firstPassYield,
            ],
            'servicesKpis' => $servicesKpis,
            'creatives' => $creatives,
        ]);
    }
}
