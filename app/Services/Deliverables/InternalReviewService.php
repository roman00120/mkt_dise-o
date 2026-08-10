<?php

namespace App\Services\Deliverables;

use App\Enums\DeliverableStatus;
use App\Enums\DeliverableVersionStatus;
use App\Models\DeliverableVersion;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InternalReviewService
{
    public function submit(DeliverableVersion $version, User $actor): DeliverableVersion
    {
        if (! $version->isEditable() || ! $version->files()->exists() || ! $version->files()->where('is_primary', true)->exists() || ! filled($version->notes)) {
            throw ValidationException::withMessages(['version' => 'La versión requiere archivo principal y notas de entrega.']);
        }

        return DB::transaction(fn () => $this->move($version, $actor, DeliverableVersionStatus::INTERNAL_REVIEW, DeliverableStatus::INTERNAL_REVIEW, 'deliverable_submitted_internal_review', ['submitted_for_internal_review_at' => now()]));
    }

    public function approve(DeliverableVersion $version, User $actor): DeliverableVersion
    {
        return DB::transaction(fn () => $this->review($version, $actor, DeliverableVersionStatus::READY_FOR_MARKETING, DeliverableStatus::READY_FOR_MARKETING, 'deliverable_internal_approved', 'approved'));
    }

    public function requestChanges(DeliverableVersion $version, User $actor, string $reason): DeliverableVersion
    {
        return DB::transaction(function () use ($version, $actor, $reason): DeliverableVersion {
            $result = $this->review($version, $actor, DeliverableVersionStatus::INTERNAL_CHANGES_REQUESTED, DeliverableStatus::CHANGES_REQUESTED_INTERNAL, 'internal_changes_requested', 'changes_requested');
            $result->corrections()->create(['uuid' => (string) Str::uuid(), 'creative_request_id' => $result->deliverable->creative_request_id, 'deliverable_id' => $result->deliverable_id, 'requested_by' => $actor->id, 'type' => 'internal', 'status' => 'open', 'summary' => $reason, 'details' => $reason]);
            $result->deliverable->request->update(['status' => 'in_progress', 'last_status_changed_at' => now()]);

            return $result;
        });
    }

    private function review(DeliverableVersion $version, User $actor, DeliverableVersionStatus $vStatus, DeliverableStatus $dStatus, string $event, string $decision): DeliverableVersion
    {
        if ($version->status !== DeliverableVersionStatus::INTERNAL_REVIEW) {
            throw ValidationException::withMessages(['version' => 'La versión no está en revisión interna.']);
        } $version->update(['status' => $vStatus, 'reviewed_at' => now()]);
        $version->deliverable->update(['status' => $dStatus]);
        if ($dStatus === DeliverableStatus::INTERNAL_REVIEW) {
            $version->deliverable->request->update(['status' => 'internal_review', 'last_status_changed_at' => now()]);
        }
        $version->reviews()->create(['uuid' => (string) Str::uuid(), 'reviewer_id' => $actor->id, 'review_type' => 'internal', 'decision' => $decision]);
        $version->deliverable->request->events()->create(['actor_id' => $actor->id, 'event' => $event, 'metadata' => ['version_number' => $version->version_number]]);

        return $version->fresh();
    }

    private function move(DeliverableVersion $version, User $actor, DeliverableVersionStatus $vStatus, DeliverableStatus $dStatus, string $event, array $fields): DeliverableVersion
    {
        $version->update($fields + ['status' => $vStatus]);
        $version->deliverable->update(['status' => $dStatus]);
        $version->deliverable->request->events()->create(['actor_id' => $actor->id, 'event' => $event, 'metadata' => ['version_number' => $version->version_number]]);

        return $version->fresh();
    }
}
