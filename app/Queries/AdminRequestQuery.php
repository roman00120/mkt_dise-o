<?php

namespace App\Queries;

use App\Models\CreativeRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminRequestQuery
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        return CreativeRequest::query()->with(['requester', 'assignee'])->when($filters['q'] ?? null, fn ($q, $value) => $q->where(fn ($q) => $q->where('folio', 'like', "%{$value}%")->orWhere('title', 'like', "%{$value}%")))->when($filters['service'] ?? null, fn ($q, $value) => $q->where('service', $value))->when($filters['status'] ?? null, fn ($q, $value) => $q->where('status', $value))->latest('updated_at')->paginate(25)->withQueryString();
    }
}
