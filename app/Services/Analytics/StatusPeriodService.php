<?php

namespace App\Services\Analytics;

use App\Models\CreativeRequest;
use App\Models\RequestStatusPeriod;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class StatusPeriodService
{
    public function transition(CreativeRequest $request, string $from, string $to, User $actor, CarbonInterface $at): void
    {
        DB::transaction(function () use ($request, $to, $actor, $at): void {
            $open = RequestStatusPeriod::where('creative_request_id', $request->id)->whereNull('ended_at')->latest('started_at')->first();
            if ($open) {
                $open->update(['ended_at' => $at, 'duration_seconds' => $open->started_at->diffInSeconds($at)]);
            } RequestStatusPeriod::create(['creative_request_id' => $request->id, 'status' => $to, 'started_at' => $at, 'changed_by' => $actor->id]);
        });
    }

    public function rebuild(?int $requestId = null, bool $dryRun = false): array
    {
        $query = CreativeRequest::query()->when($requestId, fn ($q) => $q->whereKey($requestId));
        $created = 0;
        $inconsistencies = [];
        foreach ($query->with('events')->cursor() as $request) {
            $events = $request->events->sortBy('created_at')->values();
            $previous = 'draft';
            $started = $request->submitted_at ?? $request->created_at;
            foreach ($events as $event) {
                $to = $this->statusFromEvent($event->event, $event->metadata ?? []);
                if (! $to || $event->created_at->lessThan($started)) {
                    continue;
                } if (! $dryRun) {
                    RequestStatusPeriod::updateOrCreate(['creative_request_id' => $request->id, 'status' => $previous, 'started_at' => $started], ['ended_at' => $event->created_at, 'duration_seconds' => $started->diffInSeconds($event->created_at), 'changed_by' => $event->actor_id]);
                }$created++;
                $previous = $to;
                $started = $event->created_at;
            } if (! $dryRun && $started) {
                RequestStatusPeriod::updateOrCreate(['creative_request_id' => $request->id, 'status' => $previous, 'started_at' => $started], ['ended_at' => null, 'duration_seconds' => null]);
            }
        }

        return ['periods' => $created, 'inconsistencies' => $inconsistencies];
    }

    private function statusFromEvent(string $event, array $metadata): ?string
    {
        return match ($event) {
            'validation_started' => 'in_validation','request_assigned','status_changed' => ($metadata['to'] ?? 'assigned'),'work_started' => 'in_progress','information_requested' => 'waiting_for_information','internal_review_started' => 'internal_review','deliverable_review_started' => 'marketing_review','marketing_corrections_requested' => 'corrections_requested','deliverable_approved' => 'approved','request_completed' => 'completed','request_rejected' => 'rejected',default => null
        };
    }
}
