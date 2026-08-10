<?php

namespace App\Services\Admin;

use App\Enums\RequestPriority;
use App\Enums\UserStatus;
use App\Models\AuditLog;
use App\Models\CreativeRequest;
use App\Models\DeliverableVersion;
use App\Models\RequestType;
use App\Models\User;

class AdminDashboardService
{
    public function data(): array
    {
        $active = User::where('status', UserStatus::ACTIVE->value);
        $totalRequests = CreativeRequest::count();
        $completedRequests = CreativeRequest::where('status', 'completed')->count();
        $completionRate = $totalRequests > 0 ? round(($completedRequests / $totalRequests) * 100) : 0;

        return [
            'metrics' => [
                'active_users' => (clone $active)->count(),
                'inactive_users' => User::where('status', UserStatus::INACTIVE->value)->count(),
                'suspended_users' => User::where('status', UserStatus::SUSPENDED->value)->count(),
                'active_requests' => CreativeRequest::whereNotIn('status', ['completed', 'cancelled', 'rejected'])->count(),
                'overdue_requests' => CreativeRequest::whereNotIn('status', ['completed', 'cancelled', 'rejected'])->whereDate('required_date', '<', today())->count(),
                'pending_validation' => CreativeRequest::where('status', 'in_validation')->count(),
                'pending_deliverables' => DeliverableVersion::where('status', 'marketing_review')->count(),
                'total_requests' => $totalRequests,
                'completed_requests' => $completedRequests,
                'urgent_requests' => CreativeRequest::where('requested_priority', RequestPriority::URGENT->value)->count(),
                'completion_rate' => $completionRate,
            ],
            'serviceBreakdown' => [
                'design' => CreativeRequest::where('service', 'design')->count(),
                'video' => CreativeRequest::where('service', 'video')->count(),
                'render' => CreativeRequest::where('service', 'render')->count(),
            ],
            'statusBreakdown' => [
                'completed' => $completedRequests,
                'in_progress' => CreativeRequest::whereIn('status', ['assigned', 'in_progress', 'creative_review', 'marketing_review'])->count(),
                'in_validation' => CreativeRequest::where('status', 'in_validation')->count(),
                'cancelled' => CreativeRequest::whereIn('status', ['cancelled', 'rejected'])->count(),
            ],
            'recentUsers' => User::latest()->limit(8)->get(),
            'recentRequests' => CreativeRequest::with(['requester', 'assignee'])->latest()->limit(8)->get(),
            'audit' => AuditLog::with('actor')->latest()->limit(10)->get(),
            'alerts' => $this->alerts(),
        ];
    }

    private function alerts(): array
    {
        $alerts = [];

        if (! User::where('role', 'creative')->where('status', 'active')->exists()) {
            $alerts[] = 'No existe ningún creativo activo.';
        }
        if (CreativeRequest::whereNotIn('status', ['completed', 'cancelled', 'rejected'])->whereDate('required_date', '<', today())->exists()) {
            $alerts[] = 'Existen solicitudes activas vencidas.';
        }
        foreach (['design', 'video', 'render'] as $service) {
            if (! RequestType::where('service', $service)->where('is_active', true)->exists()) {
                $alerts[] = 'El catálogo de '.$service.' no tiene tipos activos.';
            }
        }
        if (CreativeRequest::whereIn('status', ['assigned', 'in_progress'])->whereNull('assignee_id')->exists()) {
            $alerts[] = 'Existen solicitudes activas sin responsable.';
        }

        return $alerts;
    }
}
