<?php

namespace App\Services\Analytics;

use App\Models\User;

class DataQualityService
{
    public function summary(User $user, array $filters): array
    {
        $q = app(ReportScopeService::class)->requests($user, $filters);

        return ['invalid_cycle' => (clone $q)->whereNotNull('submitted_at')->whereNotNull('completed_at')->whereColumn('completed_at', '<', 'submitted_at')->count(), 'active_completed' => (clone $q)->whereNotIn('status', ['completed', 'cancelled', 'rejected'])->whereNotNull('completed_at')->count(), 'missing_service' => (clone $q)->whereNull('service')->count(), 'missing_required_dates' => (clone $q)->whereNotIn('status', ['draft', 'completed', 'cancelled', 'rejected'])->whereNull('required_date')->count()];
    }
}
