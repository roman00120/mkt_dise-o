<?php

namespace App\Services\Deliverables;

use App\Enums\DeliverableStatus;
use App\Enums\DeliverableVersionStatus;
use App\Models\CorrectionRequest;
use App\Models\DeliverableVersion;
use App\Models\User;
use App\Services\Notifications\BusinessNotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CorrectionRequestService
{
    public function marketing(DeliverableVersion $version, User $actor, array $data, ?BusinessNotificationService $notifications = null): CorrectionRequest
    {
        if ($version->status !== DeliverableVersionStatus::MARKETING_REVIEW || $version->corrections()->where(['type' => 'marketing', 'status' => 'open'])->exists()) {
            throw ValidationException::withMessages(['version' => 'La versión no admite otra corrección.']);
        }

        return DB::transaction(function () use ($version, $actor, $data, $notifications): CorrectionRequest {
            $correction = $version->corrections()->create(['uuid' => (string) Str::uuid(), 'creative_request_id' => $version->deliverable->creative_request_id, 'deliverable_id' => $version->deliverable_id, 'requested_by' => $actor->id, 'type' => 'marketing', 'status' => 'open', 'summary' => $data['summary'], 'details' => $data['details'], 'category' => $data['category'] ?? null, 'due_date' => $data['due_date'] ?? null]);
            $version->update(['status' => DeliverableVersionStatus::MARKETING_CHANGES_REQUESTED]);
            $version->deliverable->update(['status' => DeliverableStatus::CHANGES_REQUESTED_MARKETING]);
            $version->deliverable->request->update(['status' => 'corrections_requested', 'last_status_changed_at' => now()]);
            $version->reviews()->create(['uuid' => (string) Str::uuid(), 'reviewer_id' => $actor->id, 'review_type' => 'marketing', 'decision' => 'changes_requested', 'comments' => $data['details']]);
            $version->deliverable->request->events()->create(['actor_id' => $actor->id, 'event' => 'marketing_corrections_requested', 'metadata' => ['version_number' => $version->version_number, 'summary' => $data['summary']]]);

            if ($notifications) {
                DB::afterCommit(fn () => $notifications->deliverable($version->fresh(['deliverable.request.requester', 'deliverable.request.assignee']), 'corrections', 'Correcciones solicitadas', 'Marketing solicitó correcciones sobre un entregable.', $actor));
            }

            return $correction;
        });
    }

    public function resolveForVersion(DeliverableVersion $version, User $actor): void
    {
        $version->corrections()->where('status', 'open')->update(['status' => 'resolved', 'resolved_by_version_id' => $version->id, 'resolved_at' => now()]);
    }
}
