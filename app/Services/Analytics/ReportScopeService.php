<?php

namespace App\Services\Analytics;

use App\Models\CreativeRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ReportScopeService
{
    public function requests(User $user, array $filters): Builder
    {
        $query = CreativeRequest::query()->with(['requester', 'assignee', 'department', 'deliverables', 'events']);
        if ($user->hasRole('marketing')) {
            $query->where('requester_id', $user->id);
        } elseif ($user->hasRole('creative', 'design', 'video', 'render')) {
            $query->where('assignee_id', $user->id);
        } elseif ($user->hasRole('supervisor') && $user->department_id) {
            $query->where('department_id', $user->department_id);
        }

        return $query->when($filters['service'] ?? null, fn ($q, $value) => $q->where('service', $value))->when($filters['department_id'] ?? null, fn ($q, $value) => $q->where('department_id', $value))->when($filters['requester_id'] ?? null, fn ($q, $value) => $q->where('requester_id', $value))->when($filters['assignee_id'] ?? null, fn ($q, $value) => $q->where('assignee_id', $value))->when($filters['status'] ?? null, fn ($q, $value) => $q->where('status', $value))->when($filters['requested_priority'] ?? null, fn ($q, $value) => $q->where('requested_priority', $value))->when($filters['operational_priority'] ?? null, fn ($q, $value) => $q->where('operational_priority', $value))->when($filters['active'] ?? null, fn ($q) => $q->whereNotIn('status', ['draft', 'completed', 'cancelled', 'rejected']))->when($filters['finalized'] ?? null, fn ($q) => $q->whereIn('status', ['completed', 'cancelled', 'rejected']))->when($filters['from_date'] ?? null, fn ($q, $value) => $q->where(function ($q) use ($value) {
            $q->where('submitted_at', '>=', $value)->orWhere(function ($q) use ($value) {
                $q->whereNull('submitted_at')->where('created_at', '>=', $value);
            });
        }))->when($filters['to_date'] ?? null, fn ($q, $value) => $q->where(function ($q) use ($value) {
            $q->where('submitted_at', '<=', $value)->orWhere(function ($q) use ($value) {
                $q->whereNull('submitted_at')->where('created_at', '<=', $value);
            });
        }));
    }
}
