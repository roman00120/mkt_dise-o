<?php

namespace App\Services\Requests;

use App\Enums\RequestStatus;
use App\Models\CreativeRequest;
use App\Models\CreativeRequestEvent;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RequestCancellationService
{
    public function cancel(CreativeRequest $request, User $user, string $reason): CreativeRequest
    {
        return DB::transaction(function () use ($request, $user, $reason): CreativeRequest {
            $locked = CreativeRequest::query()->lockForUpdate()->findOrFail($request->id);
            if (! in_array($locked->status?->value, ['pending', 'in_validation', 'waiting_for_information'], true)) {
                return $locked;
            }
            $locked->update(['status' => RequestStatus::CANCELLED, 'cancelled_at' => now(), 'cancellation_reason' => $reason]);
            CreativeRequestEvent::create(['creative_request_id' => $locked->id, 'actor_id' => $user->id, 'event' => 'request_cancelled', 'metadata' => ['reason' => $reason]]);

            return $locked->fresh();
        });
    }
}
