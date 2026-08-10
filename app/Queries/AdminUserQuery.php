<?php

namespace App\Queries;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminUserQuery
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        $allowedSorts = ['name', 'created_at', 'last_login_at', 'status'];
        $sort = in_array($filters['sort'] ?? 'created_at', $allowedSorts, true) ? ($filters['sort'] ?? 'created_at') : 'created_at';
        $direction = ($filters['direction'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        return User::query()->with('department')->withCount(['assignedCreativeRequests as active_assignments' => fn ($q) => $q->whereNotIn('status', ['completed', 'cancelled', 'rejected'])])->when($filters['q'] ?? null, fn ($q, $value) => $q->where(fn ($q) => $q->where('name', 'like', "%{$value}%")->orWhere('email', 'like', "%{$value}%")))->when($filters['role'] ?? null, fn ($q, $value) => $q->where('role', $value))->when($filters['status'] ?? null, fn ($q, $value) => $q->where('status', $value))->when($filters['department_id'] ?? null, fn ($q, $value) => $q->where('department_id', $value))->when(($filters['access'] ?? null) === 'recent', fn ($q) => $q->whereNotNull('last_login_at'))->when(($filters['access'] ?? null) === 'never', fn ($q) => $q->whereNull('last_login_at'))->orderBy($sort, $direction)->paginate((int) ($filters['per_page'] ?? 25))->withQueryString();
    }
}
