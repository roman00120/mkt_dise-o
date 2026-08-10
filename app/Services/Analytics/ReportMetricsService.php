<?php

namespace App\Services\Analytics;

use App\Models\CorrectionRequest;
use App\Models\DeliverableVersion;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ReportMetricsService
{
    public function __construct(private ReportScopeService $scope) {}

    public function dashboard(User $user, array $rawFilters): array
    {
        $filters = app(ReportFilterService::class)->normalize($rawFilters);
        $base = $this->scope->requests($user, $filters);
        $sent = (clone $base)->whereNotNull('submitted_at');
        $counts = ['created' => (clone $base)->whereBetween('created_at', [$filters['from_date'], $filters['to_date']])->count(), 'sent' => (clone $sent)->count(), 'drafts' => (clone $base)->where('status', 'draft')->count(), 'active' => (clone $base)->whereNotIn('status', ['draft', 'completed', 'cancelled', 'rejected'])->count(), 'completed' => (clone $base)->where('status', 'completed')->count(), 'cancelled' => (clone $base)->where('status', 'cancelled')->count(), 'rejected' => (clone $base)->where('status', 'rejected')->count(), 'overdue' => (clone $base)->whereNotIn('status', ['completed', 'cancelled', 'rejected'])->whereDate('required_date', '<', today())->count()];
        $rows = (clone $base)->whereNotNull('submitted_at')->whereNotNull('completed_at')->get(['id', 'submitted_at', 'completed_at']);
        $cycles = $rows->filter(fn ($row) => $row->completed_at->greaterThan($row->submitted_at))->map(fn ($row) => $row->submitted_at->diffInMinutes($row->completed_at))->values()->all();
        $distribution = (clone $base)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status')->all();
        $services = (clone $base)->selectRaw('service, count(*) as total')->groupBy('service')->pluck('total', 'service')->all();
        $departments = (clone $base)->selectRaw('department_id, count(*) as total')->groupBy('department_id')->pluck('total', 'department_id')->all();
        $previous = $this->periodSummary($user, app(ReportFilterService::class)->previous($rawFilters));

        return ['filters' => $filters, 'counts' => $counts, 'cycle' => $this->distribution($cycles), 'services' => $services, 'departments' => $departments, 'statuses' => $distribution, 'trend' => $this->trend($base), 'previous' => $previous, 'comparison' => $this->comparison($counts, $previous), 'quality' => app(DataQualityService::class)->summary($user, $filters)];
    }

    public function periodSummary(User $user, array $rawFilters): array
    {
        $filters = app(ReportFilterService::class)->normalize($rawFilters);
        $q = $this->scope->requests($user, $filters);

        return ['sent' => (clone $q)->whereNotNull('submitted_at')->count(), 'completed' => (clone $q)->where('status', 'completed')->count(), 'active' => (clone $q)->whereNotIn('status', ['draft', 'completed', 'cancelled', 'rejected'])->count(), 'overdue' => (clone $q)->whereNotIn('status', ['completed', 'cancelled', 'rejected'])->whereDate('required_date', '<', today())->count()];
    }

    public function operations(User $user, array $rawFilters): array
    {
        $filters = app(ReportFilterService::class)->normalize($rawFilters);
        $q = $this->scope->requests($user, $filters);
        $statuses = (clone $q)->selectRaw('status,count(*) as total')->groupBy('status')->pluck('total', 'status')->all();

        return ['filters' => $filters, 'statuses' => $statuses, 'unassigned' => (clone $q)->whereIn('status', ['assigned', 'in_progress'])->whereNull('assignee_id')->count(), 'soon_due' => (clone $q)->whereNotIn('status', ['completed', 'cancelled', 'rejected'])->whereBetween('required_date', [today(), today()->addDays(3)])->count(), 'overdue' => (clone $q)->whereNotIn('status', ['completed', 'cancelled', 'rejected'])->whereDate('required_date', '<', today())->count(), 'workload' => $this->workload($q), 'bottlenecks' => $this->bottlenecks($statuses)];
    }

    public function detail(User $user, array $rawFilters): LengthAwarePaginator
    {
        $filters = app(ReportFilterService::class)->normalize($rawFilters);

        return $this->scope->requests($user, $filters)->latest('submitted_at')->paginate(25)->withQueryString();
    }

    public function workload(Builder $query): array
    {
        return (clone $query)->whereNotNull('assignee_id')->selectRaw('assignee_id,count(*) as total')->groupBy('assignee_id')->with('assignee')->get()->map(fn ($row) => ['name' => $row->assignee?->name ?? 'Sin responsable', 'total' => $row->total])->all();
    }

    public function deliverables(User $user, array $rawFilters): array
    {
        $filters = app(ReportFilterService::class)->normalize($rawFilters);
        $ids = $this->scope->requests($user, $filters)->select('creative_requests.id');
        $q = DeliverableVersion::whereHas('deliverable', fn ($q) => $q->whereIn('creative_request_id', $ids));

        return ['total' => (clone $q)->count(), 'approved' => (clone $q)->where('status', 'approved')->count(), 'marketing_review' => (clone $q)->where('status', 'marketing_review')->count(), 'corrections' => (clone $q)->whereIn('status', ['marketing_changes_requested', 'internal_changes_requested'])->count()];
    }

    public function corrections(User $user, array $rawFilters): array
    {
        $filters = app(ReportFilterService::class)->normalize($rawFilters);
        $ids = $this->scope->requests($user, $filters)->select('creative_requests.id');
        $q = CorrectionRequest::whereIn('creative_request_id', $ids);

        return ['total' => (clone $q)->count(), 'open' => (clone $q)->where('status', 'open')->count(), 'resolved' => (clone $q)->where('status', 'resolved')->count(), 'marketing' => (clone $q)->where('type', 'marketing')->count(), 'internal' => (clone $q)->where('type', 'internal')->count()];
    }

    private function trend(Builder $query): array
    {
        return (clone $query)->whereNotNull('submitted_at')->selectRaw('date(submitted_at) as day,count(*) as total')->groupBy('day')->orderBy('day')->limit(62)->get()->map(fn ($row) => ['label' => $row->day, 'value' => (int) $row->total])->all();
    }

    private function distribution(array $values): array
    {
        if ($values === []) {
            return ['count' => 0, 'average_minutes' => null, 'median_minutes' => null, 'p75_minutes' => null, 'p90_minutes' => null, 'min_minutes' => null, 'max_minutes' => null];
        } sort($values);

        return ['count' => count($values), 'average_minutes' => round(array_sum($values) / count($values), 1), 'median_minutes' => $this->percentile($values, 50), 'p75_minutes' => $this->percentile($values, 75), 'p90_minutes' => $this->percentile($values, 90), 'min_minutes' => min($values), 'max_minutes' => max($values)];
    }

    private function percentile(array $values, int $percentile): int
    {
        $index = max(0, (int) ceil(($percentile / 100) * count($values)) - 1);

        return (int) $values[$index];
    }

    private function comparison(array $current, array $previous): array
    {
        $result = [];
        foreach ($current as $key => $value) {
            $old = $previous[$key] ?? 0;
            $result[$key] = ['absolute' => $value - $old, 'percent' => $old === 0 ? null : round((($value - $old) / $old) * 100, 1)];
        }

        return $result;
    }

    private function bottlenecks(array $statuses): array
    {
        arsort($statuses);

        return array_slice($statuses, 0, 3, true);
    }
}
