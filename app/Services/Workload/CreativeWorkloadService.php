<?php

namespace App\Services\Workload;

use App\Enums\UserRole;
use App\Models\CreativeRequest;
use App\Models\User;

class CreativeWorkloadService
{
    public function forUser(User $user): array
    {
        $query = CreativeRequest::query()->with('assignee')->whereNotIn('status', ['draft', 'completed', 'approved', 'cancelled', 'rejected']);
        if (! $user->hasRole(UserRole::SUPERVISOR)) {
            $query->where('service', $user->role->value)->where('assignee_id', $user->id);
        }
        $requests = $query->get();
        $groups = $user->hasRole(UserRole::SUPERVISOR) ? $requests->groupBy('assignee_id') : collect([$user->id => $requests]);

        return $groups->map(fn ($items, $id) => ['user' => $items->first()?->assignee?->name ?: ($id === $user->id ? $user->name : 'Sin asignar'), 'total' => $items->count(), 'in_progress' => $items->where('status', 'in_progress')->count(), 'waiting' => $items->where('status', 'waiting_for_information')->count(), 'review' => $items->where('status', 'internal_review')->count(), 'overdue' => $items->filter(fn ($item) => $item->internal_due_date?->isPast())->count(), 'load' => $items->count() >= 8 ? 'Alta' : ($items->count() >= 4 ? 'Moderada' : 'Disponible')])->values()->all();
    }
}
