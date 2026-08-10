<?php

namespace App\Queries;

use App\Models\AuditLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AuditLogQuery
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        return AuditLog::query()->with(['actor', 'targetUser'])->when($filters['action'] ?? null, fn ($q, $value) => $q->where('action', 'like', "%{$value}%"))->when($filters['actor_id'] ?? null, fn ($q, $value) => $q->where('actor_id', $value))->latest()->paginate(25)->withQueryString();
    }
}
