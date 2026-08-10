<?php

namespace App\Services\Dashboard;

use App\Enums\DeliverableStatus;
use App\Models\CreativeRequest;
use App\Models\Deliverable;
use App\Models\User;
use App\Support\RequestCatalog;
use Carbon\Carbon;

class MarketingDashboardService
{
    public function forUser(string $filter = 'all', ?User $user = null): array
    {
        abort_unless($user, 403);

        $all = CreativeRequest::query()
            ->where('requester_id', $user->id)
            ->with(['assignee', 'deliverables.currentVersion'])
            ->latest('updated_at')
            ->get();

        $activeStatuses = ['pending', 'in_validation', 'assigned', 'in_progress', 'waiting_for_information', 'internal_review', 'marketing_review', 'corrections_requested'];
        $filtered = $all->filter(fn (CreativeRequest $item): bool => match ($filter) {
            'pending' => in_array($item->status->value, ['pending', 'waiting_for_information', 'corrections_requested'], true),
            'in-progress' => $item->status->value === 'in_progress',
            'review' => $item->status->value === 'marketing_review',
            'completed' => in_array($item->status->value, ['approved', 'completed'], true),
            default => true,
        });

        $map = fn (CreativeRequest $item): array => [
            'id' => $item->folio,
            'request_id' => $item->id,
            'title' => $item->title ?: 'Sin título',
            'service' => $item->service->label(),
            'status' => $item->status->label(),
            'owner' => $item->assignee?->name ?? 'Sin asignar',
            'due_at' => $item->required_date,
            'updated_at' => $item->updated_at,
            'priority' => $item->requested_priority->label(),
        ];

        $pendingDeliverables = Deliverable::query()
            ->whereIn('status', [DeliverableStatus::READY_FOR_MARKETING, DeliverableStatus::MARKETING_REVIEW])
            ->whereHas('request', fn ($query) => $query->where('requester_id', $user->id))
            ->with(['request', 'creator', 'currentVersion'])
            ->latest('submitted_to_marketing_at')
            ->get()
            ->map(fn (Deliverable $deliverable): array => [
                'request_id' => $deliverable->request?->folio,
                'title' => $deliverable->title,
                'service' => $deliverable->request?->service?->label(),
                'file' => $deliverable->currentVersion?->files()->latest()->value('original_name'),
                'version' => $deliverable->currentVersion ? 'v'.$deliverable->currentVersion->version_number : null,
                'delivered_at' => $deliverable->submitted_to_marketing_at,
                'owner' => $deliverable->creator?->name ?? 'Sin asignar',
            ])->all();

        return [
            'metrics' => [
                ['label' => 'Solicitudes activas', 'value' => $all->whereIn('status', $activeStatuses)->count(), 'context' => 'Estados en curso', 'icon' => 'layers-3', 'tone' => 'primary'],
                ['label' => 'En proceso', 'value' => $all->where('status', 'in_progress')->count(), 'context' => 'Trabajo creativo activo', 'icon' => 'loader-circle', 'tone' => 'info'],
                ['label' => 'Pendientes de revisión', 'value' => $all->where('status', 'marketing_review')->count(), 'context' => 'Requieren tu atención', 'icon' => 'search-check', 'tone' => 'warning'],
                ['label' => 'Próximas a vencer', 'value' => $all->filter(fn (CreativeRequest $item): bool => $item->required_date && $item->required_date->between(today(), today()->addDays(3)) && ! in_array($item->status->value, ['approved', 'completed', 'cancelled'], true))->count(), 'context' => 'Dentro de 3 días', 'icon' => 'alarm-clock', 'tone' => 'danger'],
            ],
            'attentionItems' => $all->filter(fn (CreativeRequest $item): bool => in_array($item->status->value, ['waiting_for_information', 'marketing_review', 'corrections_requested', 'rejected'], true))->map($map)->values()->all(),
            'recentRequests' => $filtered->map($map)->values()->all(),
            'pendingDeliverables' => $pendingDeliverables,
            'recentActivity' => [],
            'serviceCards' => RequestCatalog::services(),
            'filter' => $filter,
        ];
    }

    public function dateHealth(Carbon|string|null $date, bool $completed = false, ?Carbon $today = null): string
    {
        if (! $date) {
            return 'without_date';
        }

        if ($completed) {
            return 'on_time';
        }

        $date = $date instanceof Carbon ? $date->copy()->startOfDay() : Carbon::parse($date)->startOfDay();
        $today ??= now()->startOfDay();

        return $date->isBefore($today) ? 'overdue' : ($today->diffInDays($date, true) <= 3 ? 'due_soon' : 'on_time');
    }
}
