<?php

namespace App\Services\Deliverables;

use App\Enums\DeliverableStatus;
use App\Enums\DeliverableVersionStatus;
use App\Models\CreativeRequest;
use App\Models\Deliverable;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DeliverableService
{
    public function principal(CreativeRequest $request, User $actor): Deliverable
    {
        return DB::transaction(function () use ($request, $actor): Deliverable {
            $deliverable = $request->deliverables()->lockForUpdate()->first();
            if ($deliverable) {
                return $deliverable->load('currentVersion');
            }
            $deliverable = $request->deliverables()->create(['uuid' => (string) Str::uuid(), 'created_by' => $actor->id, 'title' => $request->title ?: 'Entregable principal', 'status' => DeliverableStatus::DRAFT]);
            $version = $deliverable->versions()->create(['uuid' => (string) Str::uuid(), 'version_number' => 1, 'created_by' => $actor->id, 'status' => DeliverableVersionStatus::DRAFT]);
            $deliverable->update(['current_version_id' => $version->id]);
            $request->events()->create(['actor_id' => $actor->id, 'event' => 'deliverable_created', 'metadata' => ['version_number' => 1]]);

            return $deliverable->fresh('currentVersion');
        });
    }
}
