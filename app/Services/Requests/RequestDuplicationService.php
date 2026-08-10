<?php

namespace App\Services\Requests;

use App\Enums\RequestStatus;
use App\Models\CreativeRequest;
use App\Models\CreativeRequestEvent;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RequestDuplicationService
{
    public function __construct(private readonly FolioGenerator $folioGenerator) {}

    public function duplicate(CreativeRequest $source, User $user): CreativeRequest
    {
        return DB::transaction(function () use ($source, $user): CreativeRequest {
            $copy = CreativeRequest::create([
                'uuid' => (string) Str::uuid(), 'folio' => $this->folioGenerator->next(), 'requester_id' => $user->id,
                'duplicated_from_id' => $source->id, 'service' => $source->service->value, 'request_type' => $source->request_type,
                'other_request_type' => $source->other_request_type, 'title' => $source->title ? 'Copia de '.$source->title : null,
                'description' => $source->description, 'objective' => $source->objective, 'target_audience' => $source->target_audience,
                'channel' => $source->channel, 'required_date' => $source->required_date, 'requested_priority' => $source->requested_priority->value,
                'urgency_reason' => $source->urgency_reason, 'status' => RequestStatus::DRAFT->value, 'current_step' => max(2, min(6, $source->current_step)),
            ]);
            if ($source->detail) {
                $copy->detail()->create(['data' => $source->detail->data]);
            }
            CreativeRequestEvent::create(['creative_request_id' => $copy->id, 'actor_id' => $user->id, 'event' => 'request_duplicated', 'metadata' => ['source_folio' => $source->folio]]);

            return $copy->fresh();
        });
    }
}
