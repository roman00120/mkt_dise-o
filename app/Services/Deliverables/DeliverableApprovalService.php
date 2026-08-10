<?php

namespace App\Services\Deliverables;

use App\Enums\DeliverableStatus;
use App\Enums\DeliverableVersionStatus;
use App\Models\DeliverableVersion;
use App\Models\User;
use App\Services\Notifications\BusinessNotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DeliverableApprovalService
{
    public function approve(DeliverableVersion $version, User $actor, ?string $comments = null, ?BusinessNotificationService $notifications = null): DeliverableVersion
    {
        if ($version->status !== DeliverableVersionStatus::MARKETING_REVIEW || $version->deliverable->current_version_id !== $version->id || $version->corrections()->where(['type' => 'marketing', 'status' => 'open'])->exists()) {
            throw ValidationException::withMessages(['version' => 'Solo puede aprobarse la versión vigente en revisión de Marketing.']);
        }

        return DB::transaction(function () use ($version, $actor, $comments, $notifications): DeliverableVersion {
            $version->update(['status' => DeliverableVersionStatus::APPROVED, 'approved_at' => now()]);
            $version->deliverable->update(['status' => DeliverableStatus::APPROVED, 'approved_version_id' => $version->id, 'approved_at' => now()]);
            $version->reviews()->create(['uuid' => (string) Str::uuid(), 'reviewer_id' => $actor->id, 'review_type' => 'marketing', 'decision' => 'approved', 'comments' => $comments]);
            $version->deliverable->request->update(['status' => 'completed', 'completed_at' => now(), 'last_status_changed_at' => now()]);
            $version->deliverable->request->events()->create(['actor_id' => $actor->id, 'event' => 'deliverable_approved', 'metadata' => ['version_number' => $version->version_number]]);
            $version->deliverable->request->events()->create(['actor_id' => $actor->id, 'event' => 'request_completed', 'metadata' => ['completed_automatically' => true]]);

            $result = $version->fresh(['deliverable.request.requester', 'deliverable.request.assignee']);

            if ($requester = $result->deliverable?->request?->requester) {
                try {
                    $requester->notify(new \App\Notifications\CreativeRequestCompletedNotification($result->deliverable->request, $actor));
                } catch (\Throwable $e) {
                    logger()->error('Failed sending request completed notification: '.$e->getMessage());
                }
            }

            if ($notifications) {
                DB::afterCommit(fn () => $notifications->deliverable($result, 'approval', 'Entregable aprobado', 'Marketing aprobó el entregable.', $actor));
            }

            return $result;
        });
    }
}
