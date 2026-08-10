<?php

namespace App\Services\Deliverables;

use App\Enums\DeliverableStatus;
use App\Enums\DeliverableVersionStatus;
use App\Models\DeliverableVersion;
use App\Models\User;
use App\Services\Notifications\BusinessNotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MarketingReviewService
{
    public function submitFinal(DeliverableVersion $version, User $actor): DeliverableVersion
    {
        if (! $version->isEditable() || ! $version->files()->exists()) {
            throw ValidationException::withMessages(['version' => 'Agrega al menos un archivo antes de enviarlo a Marketing.']);
        }

        if (! $version->files()->where('is_primary', true)->exists()) {
            $version->files()->oldest('id')->first()?->update(['is_primary' => true]);
        }
        if (! filled($version->notes)) {
            $version->update(['notes' => 'Entrega final para revisión de Marketing.']);
        }

        return DB::transaction(function () use ($version, $actor): DeliverableVersion {
            $version->update(['status' => DeliverableVersionStatus::MARKETING_REVIEW, 'submitted_to_marketing_at' => now()]);
            $version->deliverable->update(['status' => DeliverableStatus::MARKETING_REVIEW, 'submitted_to_marketing_at' => now(), 'current_version_id' => $version->id]);
            $version->deliverable->request->update(['status' => 'marketing_review', 'last_status_changed_at' => now()]);
            $version->deliverable->request->events()->create(['actor_id' => $actor->id, 'event' => 'deliverable_sent_to_marketing', 'metadata' => ['version_number' => $version->version_number]]);

            return $version->fresh();
        });
    }

    public function send(DeliverableVersion $version, User $actor, ?BusinessNotificationService $notifications = null): DeliverableVersion
    {
        if ($version->status !== DeliverableVersionStatus::READY_FOR_MARKETING || ! $version->files()->where('is_primary', true)->exists() || ! filled($version->notes)) {
            throw ValidationException::withMessages(['version' => 'La versión no está lista para Marketing.']);
        }

        return DB::transaction(function () use ($version, $actor, $notifications): DeliverableVersion {
            $version->update(['status' => DeliverableVersionStatus::MARKETING_REVIEW, 'submitted_to_marketing_at' => now()]);
            $version->deliverable->correctionRequests()->where('status', 'open')->update(['status' => 'resolved', 'resolved_by_version_id' => $version->id, 'resolved_at' => now()]);
            $version->deliverable->update(['status' => DeliverableStatus::MARKETING_REVIEW, 'submitted_to_marketing_at' => now(), 'current_version_id' => $version->id]);
            $version->deliverable->request->update(['status' => 'marketing_review', 'last_status_changed_at' => now()]);
            $version->deliverable->request->events()->create(['actor_id' => $actor->id, 'event' => 'deliverable_sent_to_marketing', 'metadata' => ['version_number' => $version->version_number]]);

            $result = $version->fresh(['deliverable.request.requester', 'deliverable.request.assignee']);
            if ($notifications) {
                DB::afterCommit(fn () => $notifications->deliverable($result, 'deliverable_review', 'Entregable listo para revisión', 'Hay un entregable listo para tu revisión.', $actor));
            }

            return $result;
        });
    }
}
