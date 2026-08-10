<?php

namespace App\Queries;

use App\Enums\UserRole;
use App\Models\CreativeRequest;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class CreativeRequestQuery
{
    public function paginate(User $user, array $filters): LengthAwarePaginator
    {
        $query = $this->base($user, $filters);
        $sort = $filters['sort'] ?? 'updated_at';
        $direction = $filters['direction'] ?? 'desc';

        return $query->orderBy($sort, $direction)->paginate($filters['per_page'] ?? 25)->withQueryString();
    }

    public function kanban(User $user, array $filters): array
    {
        $items = $this->base($user, $filters)->latest('updated_at')->limit(150)->get();

        return collect(['pending', 'in_validation', 'assigned', 'in_progress', 'waiting_for_information', 'internal_review', 'marketing_review'])->mapWithKeys(fn ($status) => [$status => $items->where('status.value', $status)->values()])->all();
    }

    public function base(User $user, array $filters = []): Builder
    {
        $query = CreativeRequest::query()->with(['requester', 'assignee', 'detail', 'files', 'informationRequests' => fn ($q) => $q->where('status', 'open')]);
        if ($user->hasRole(UserRole::CREATIVE)) {
            $query->where('assignee_id', $user->id);
        } elseif (! $user->hasRole(UserRole::ADMIN, UserRole::SUPERVISOR)) {
            $query->where('service', $user->role->value);
        }
        foreach (['service', 'status', 'assignee_id', 'operational_priority', 'requested_priority'] as $field) {
            if (filled($filters[$field] ?? null)) {
                $query->where($field, $filters[$field]);
            }
        }
        if (($filters['unassigned'] ?? false)) {
            $query->whereNull('assignee_id');
        }
        if (($filters['mine'] ?? false)) {
            $query->where('assignee_id', $user->id);
        }
        if (filled($filters['q'] ?? null)) {
            $term = $filters['q'];
            $query->where(fn (Builder $q) => $q->where('folio', 'like', "%{$term}%")->orWhere('title', 'like', "%{$term}%")->orWhereHas('requester', fn ($r) => $r->where('name', 'like', "%{$term}%"))->orWhere('request_type', 'like', "%{$term}%"));
        }

        return $query;
    }
}
