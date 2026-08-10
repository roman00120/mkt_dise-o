<?php

namespace App\Services\Requests;

use App\Enums\RequestStatus;
use App\Models\CreativeRequest;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class MarketingRequestQuery
{
    public function paginate(User $user, array $filters): LengthAwarePaginator
    {
        $query = $this->base($user, $filters);
        $sort = $filters['sort'] ?? 'updated_at';
        $direction = $filters['direction'] ?? 'desc';
        if ($sort === 'requested_priority') {
            $query->orderByRaw("CASE requested_priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 ELSE 4 END {$direction}");
        } else {
            $query->orderBy($sort, $direction);
        }

        return $query->paginate($filters['per_page'] ?? 10)->withQueryString();
    }

    public function metrics(User $user): array
    {
        $counts = CreativeRequest::query()->where('requester_id', $user->id)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        $active = collect([RequestStatus::PENDING->value, RequestStatus::IN_VALIDATION->value, RequestStatus::ASSIGNED->value, RequestStatus::IN_PROGRESS->value, RequestStatus::WAITING_FOR_INFORMATION->value, RequestStatus::INTERNAL_REVIEW->value, RequestStatus::MARKETING_REVIEW->value, RequestStatus::CORRECTIONS_REQUESTED->value])->sum(fn ($status) => (int) ($counts[$status] ?? 0));

        return ['all' => $counts->sum(), 'draft' => (int) ($counts[RequestStatus::DRAFT->value] ?? 0), 'active' => $active, 'review' => (int) ($counts[RequestStatus::MARKETING_REVIEW->value] ?? 0), 'completed' => (int) ($counts[RequestStatus::APPROVED->value] ?? 0) + (int) ($counts[RequestStatus::COMPLETED->value] ?? 0)];
    }

    private function base(User $user, array $filters): Builder
    {
        $query = CreativeRequest::query()->where('requester_id', $user->id)->with(['requester', 'detail', 'files.uploader', 'events' => fn ($events) => $events->with('actor')->latest()->limit(30)]);
        if ($filters['q'] ?? false) {
            $term = $filters['q'];
            $query->where(fn (Builder $q) => $q->where('folio', 'like', "%{$term}%")->orWhere('title', 'like', "%{$term}%"));
        }
        foreach (['status' => 'status', 'service' => 'service', 'priority' => 'requested_priority'] as $input => $column) {
            if (isset($filters[$input]) && $filters[$input] !== '') {
                $query->where($column, $filters[$input]);
            }
        }
        foreach ([['required_from', 'required_date', '>='], ['required_to', 'required_date', '<='], ['created_from', 'created_at', '>='], ['created_to', 'created_at', '<=']] as [$input, $column, $operator]) {
            if (! empty($filters[$input])) {
                $query->whereDate($column, $operator, $filters[$input]);
            }
        }
        if (! empty($filters['drafts'])) {
            $query->where('status', RequestStatus::DRAFT->value);
        }
        if (($filters['scope'] ?? null) === 'active') {
            $query->whereIn('status', [RequestStatus::PENDING->value, RequestStatus::IN_VALIDATION->value, RequestStatus::ASSIGNED->value, RequestStatus::IN_PROGRESS->value, RequestStatus::WAITING_FOR_INFORMATION->value, RequestStatus::INTERNAL_REVIEW->value, RequestStatus::MARKETING_REVIEW->value, RequestStatus::CORRECTIONS_REQUESTED->value]);
        }
        if (($filters['scope'] ?? null) === 'completed') {
            $query->whereIn('status', [RequestStatus::APPROVED->value, RequestStatus::COMPLETED->value]);
        }
        if (($filters['scope'] ?? null) === 'review') {
            $query->where('status', RequestStatus::MARKETING_REVIEW->value);
        }
        if (! empty($filters['attention'])) {
            $query->where(fn (Builder $q) => $q->whereIn('status', [RequestStatus::WAITING_FOR_INFORMATION->value, RequestStatus::MARKETING_REVIEW->value, RequestStatus::CORRECTIONS_REQUESTED->value, RequestStatus::REJECTED->value])->orWhere(fn (Builder $due) => $due->whereNotNull('required_date')->whereDate('required_date', '<=', now()->addDays(3))->whereNotIn('status', [RequestStatus::APPROVED->value, RequestStatus::COMPLETED->value, RequestStatus::CANCELLED->value])));
        }

        return $query;
    }
}
