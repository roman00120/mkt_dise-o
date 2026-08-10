<?php

namespace App\Services\Requests;

use App\Enums\RequestStatus;
use App\Models\CreativeRequest;
use App\Models\CreativeRequestDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RequestDraftService
{
    public function create(array $data, int $userId): CreativeRequest
    {
        return DB::transaction(function () use ($data, $userId) {
            $service = $data['service'] ?? 'design';
            $request = CreativeRequest::create([
                'uuid' => (string) Str::uuid(),
                'folio' => app(FolioGenerator::class)->next(),
                'requester_id' => $userId,
                'service' => $service,
                'request_type' => $data['request_type'] ?? 'other',
                'other_request_type' => $data['other_request_type'] ?? null,
                'status' => RequestStatus::DRAFT,
                'current_step' => 1,
            ]);
            CreativeRequestDetail::create(['creative_request_id' => $request->id, 'data' => []]);
            $request->events()->create(['actor_id' => $userId, 'event' => 'draft_created']);

            return $request->load(['detail', 'files']);
        });
    }

    public function update(CreativeRequest $request, array $data, int $step): CreativeRequest
    {
        return DB::transaction(function () use ($request, $data, $step) {
            $general = array_intersect_key($data, array_flip(['service', 'request_type', 'other_request_type', 'title', 'description', 'objective', 'target_audience', 'channel', 'required_date', 'requested_priority', 'urgency_reason']));
            $request->fill($general);
            $request->current_step = max($request->current_step, $step);
            $request->last_autosaved_at = now();
            $request->save();
            $detail = $request->detail()->firstOrCreate([], ['data' => []]);
            $specific = $data['details'] ?? [];
            $detail->data = array_merge($detail->data ?? [], $specific);
            $detail->save();
            $request->events()->create(['actor_id' => auth()->id(), 'event' => 'draft_updated']);

            return $request->fresh(['detail', 'files']);
        });
    }
}
