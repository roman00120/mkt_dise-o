<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Queries\CreativeRequestQuery;

class CreativeDashboardService
{
    public function __construct(private readonly CreativeRequestQuery $query) {}

    public function forUser(User $user): array
    {
        $base = $this->query->base($user);
        $items = (clone $base)->latest('updated_at')->limit(12)->get();
        $pendingValidation = (clone $base)->whereIn('status', ['pending', 'in_validation', 'assigned'])->latest('updated_at')->limit(12)->get();

        return ['mine' => $items->where('assignee_id', $user->id)->values(), 'pendingValidation' => $pendingValidation, 'unassigned' => $items->whereNull('assignee_id')->values(), 'blocked' => $items->where('status.value', 'waiting_for_information')->values(), 'metrics' => ['total' => (clone $base)->count(), 'pending' => (clone $base)->whereIn('status', ['pending', 'in_validation', 'assigned'])->count(), 'in_progress' => (clone $base)->where('status', 'in_progress')->count(), 'blocked' => (clone $base)->where('status', 'waiting_for_information')->count()]];
    }
}
