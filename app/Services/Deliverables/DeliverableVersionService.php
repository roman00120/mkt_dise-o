<?php

namespace App\Services\Deliverables;

use App\Enums\DeliverableVersionStatus;
use App\Models\Deliverable;
use App\Models\DeliverableVersion;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DeliverableVersionService
{
    public function create(Deliverable $deliverable, User $actor, ?string $notes = null, ?string $internalNotes = null): DeliverableVersion
    {
        return DB::transaction(function () use ($deliverable, $actor, $notes, $internalNotes): DeliverableVersion {
            $locked = Deliverable::query()->lockForUpdate()->findOrFail($deliverable->id);
            $latest = $locked->versions()->lockForUpdate()->max('version_number') ?: 0;
            $version = $locked->versions()->create(['uuid' => (string) Str::uuid(), 'version_number' => $latest + 1, 'created_by' => $actor->id, 'status' => DeliverableVersionStatus::DRAFT, 'notes' => $notes, 'internal_notes' => $internalNotes]);
            $locked->update(['current_version_id' => $version->id, 'status' => 'draft']);
            $locked->request->events()->create(['actor_id' => $actor->id, 'event' => 'deliverable_version_created', 'metadata' => ['version_number' => $version->version_number]]);

            return $version;
        });
    }

    public function assertCanCreate(Deliverable $deliverable): void
    {
        if (! in_array($deliverable->request->status->value, ['in_progress', 'internal_review', 'corrections_requested'], true)) {
            throw ValidationException::withMessages(['version' => 'La solicitud no está lista para una nueva versión.']);
        }
    }

    public function openCorrections(DeliverableVersion $version): array
    {
        return $version->corrections()->where('status', 'open')->get()->all();
    }
}
