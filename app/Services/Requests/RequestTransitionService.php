<?php

namespace App\Services\Requests;

use App\Enums\RequestStatus;
use App\Models\CreativeRequest;
use App\Models\User;
use App\Notifications\CreativeRequestAssignedNotification;
use App\Services\Analytics\StatusPeriodService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RequestTransitionService
{
    private const MAP = [
        'pending' => ['in_validation'], 'in_validation' => ['assigned', 'waiting_for_information', 'rejected'],
        'waiting_for_information' => ['in_validation', 'assigned'], 'assigned' => ['in_progress', 'waiting_for_information'],
        'in_progress' => ['waiting_for_information', 'internal_review'], 'internal_review' => ['in_progress', 'marketing_review'], 'marketing_review' => ['corrections_requested', 'approved'], 'corrections_requested' => ['in_progress', 'internal_review'], 'approved' => ['completed'],
    ];

    public function allowed(string $from): array
    {
        return self::MAP[$from] ?? [];
    }

    public function transition(CreativeRequest $request, User $actor, string $to): CreativeRequest
    {
        return DB::transaction(function () use ($request, $actor, $to): CreativeRequest {
            $locked = CreativeRequest::query()->lockForUpdate()->findOrFail($request->id);
            $from = $locked->status->value;
            if (! in_array($to, $this->allowed($from), true)) {
                throw ValidationException::withMessages(['status' => 'La transición solicitada no está permitida.']);
            }
            if (! $actor->can('transition', $locked)) {
                abort(403);
            }
            if ($to === 'assigned' && ! $locked->assignee_id) {
                throw ValidationException::withMessages(['assignee_id' => 'La solicitud requiere responsable.']);
            }
            $updates = ['status' => RequestStatus::from($to), 'last_status_changed_at' => now()];
            if ($to === 'in_progress') {
                $updates['started_at'] = $locked->started_at ?: now();
            }
            if ($to === 'waiting_for_information') {
                $updates['waiting_information_since'] = now();
            }
            $locked->update($updates);
            $locked->events()->create(['actor_id' => $actor->id, 'event' => $this->eventFor($to), 'metadata' => ['from' => $from, 'to' => $to]]);
            app(StatusPeriodService::class)->transition($locked, $from, $to, $actor, now());

            $result = $locked->fresh(['assignee', 'requester']);

            // Send Email Notification to Carolina (assignee) when Hugo approves / assigns / moves to in_progress
            if (in_array($to, ['assigned', 'in_progress'], true) && $assignee = $result->assignee) {
                try {
                    $assignee->notify(new CreativeRequestAssignedNotification($result, $actor));
                } catch (\Throwable $e) {
                    logger()->error('Failed sending request assigned notification on transition: '.$e->getMessage());
                }
            }

            return $result;
        });
    }

    private function eventFor(string $to): string
    {
        return match ($to) {
            'in_validation' => 'validation_started', 'assigned' => 'status_changed', 'in_progress' => 'work_started', 'waiting_for_information' => 'information_requested', 'internal_review' => 'internal_review_started', 'marketing_review' => 'deliverable_review_started', 'corrections_requested' => 'marketing_corrections_requested', 'approved' => 'deliverable_approved', 'completed' => 'request_completed', 'rejected' => 'request_rejected', default => 'status_changed'
        };
    }
}
