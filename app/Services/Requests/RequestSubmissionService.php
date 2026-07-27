<?php

namespace App\Services\Requests;

use App\Enums\RequestStatus;
use App\Models\CreativeRequest;
use Illuminate\Support\Facades\DB;

class RequestSubmissionService
{
    public function submit(CreativeRequest $request): CreativeRequest
    {
        return DB::transaction(function () use ($request) {
            if (! $request->isDraft()) {
                return $request;
            } $request->update(['status' => RequestStatus::PENDING, 'submitted_at' => now(), 'current_step' => 7]);
            $request->events()->create(['actor_id' => auth()->id(), 'event' => 'request_submitted', 'metadata' => ['folio' => $request->folio]]);

            return $request->fresh(['detail', 'files']);
        });
    }
}
