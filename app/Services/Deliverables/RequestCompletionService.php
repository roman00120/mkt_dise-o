<?php

namespace App\Services\Deliverables;

use App\Enums\DeliverableStatus;
use App\Models\CreativeRequest;
use App\Models\User;
use App\Services\Notifications\BusinessNotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RequestCompletionService
{
    public function complete(CreativeRequest $request, User $actor, ?BusinessNotificationService $notifications = null): CreativeRequest
    {
        $deliverable = $request->deliverables()->with('approvedVersion.files')->first();
        if ($request->status->value !== 'approved' || ! $deliverable || $deliverable->status !== DeliverableStatus::APPROVED || ! $deliverable->approvedVersion || ! $deliverable->approvedVersion->files()->where(fn ($q) => $q->where('category', 'final')->orWhere('is_primary', true))->exists()) {
            throw ValidationException::withMessages(['request' => 'La solicitud no puede finalizarse sin entregable aprobado y archivo final.']);
        }

        return DB::transaction(function () use ($request, $deliverable, $actor, $notifications): CreativeRequest {
            $request->update(['status' => 'completed', 'completed_at' => now(), 'last_status_changed_at' => now()]);
            $deliverable->update(['status' => DeliverableStatus::COMPLETED, 'completed_at' => now()]);
            $request->events()->create(['actor_id' => $actor->id, 'event' => 'request_completed', 'metadata' => ['version_number' => $deliverable->approvedVersion->version_number]]);

            $result = $request->fresh(['requester', 'assignee']);

            if ($requester = $result->requester) {
                try {
                    $requester->notify(new \App\Notifications\CreativeRequestCompletedNotification($result, $actor));
                } catch (\Throwable $e) {
                    logger()->error('Failed sending request completed notification: '.$e->getMessage());
                }
            }

            if ($notifications) {
                DB::afterCommit(fn () => $notifications->request($result, 'completion', 'Solicitud finalizada', 'La solicitud fue finalizada.', $actor));
            }

            return $result;
        });
    }
}
