<?php

namespace App\Services\Requests;

use App\Models\CreativeRequest;
use App\Models\RequestInformationRequest;
use App\Models\User;
use App\Services\Notifications\BusinessNotificationService;
use Illuminate\Support\Facades\DB;

class RequestInformationService
{
    public function request(CreativeRequest $request, User $actor, string $message, ?string $category, ?BusinessNotificationService $notifications = null): RequestInformationRequest
    {
        return DB::transaction(function () use ($request, $actor, $message, $category, $notifications): RequestInformationRequest {
            $previous = $request->status->value;
            if (! in_array($previous, ['in_validation', 'assigned', 'in_progress'], true)) {
                abort(409, 'No se puede solicitar información en el estado actual.');
            }
            if ($request->informationRequests()->where('status', 'open')->exists()) {
                abort(409, 'Ya existe una solicitud de información abierta.');
            }
            $request->update(['status' => 'waiting_for_information', 'waiting_information_since' => now(), 'last_status_changed_at' => now()]);
            $info = $request->informationRequests()->create(['requested_by' => $actor->id, 'message' => $message, 'category' => $category, 'previous_status' => $previous, 'requested_at' => now(), 'status' => 'open']);
            $request->events()->create(['actor_id' => $actor->id, 'event' => 'information_requested', 'metadata' => ['category' => $category, 'message' => $message]]);

            if ($notifications) {
                DB::afterCommit(fn () => $notifications->request($request->fresh(['requester']), 'information_request', 'Información solicitada', 'Marketing necesita información para continuar tu solicitud.', $actor, [$request->requester]));
            }

            return $info;
        });
    }

    public function provide(RequestInformationRequest $info, User $actor, string $response): RequestInformationRequest
    {
        return DB::transaction(function () use ($info, $actor, $response): RequestInformationRequest {
            $locked = RequestInformationRequest::query()->lockForUpdate()->findOrFail($info->id);
            if ($locked->status !== 'open' || $locked->request->status->value !== 'waiting_for_information') {
                abort(409, 'La solicitud ya cambió.');
            }
            $target = in_array($locked->previous_status, ['assigned', 'in_progress'], true) ? 'assigned' : 'in_validation';
            $locked->update(['responded_by' => $actor->id, 'response' => $response, 'responded_at' => now(), 'status' => 'answered']);
            $locked->request->update(['status' => $target, 'waiting_information_since' => null, 'last_status_changed_at' => now()]);
            $locked->request->events()->create(['actor_id' => $actor->id, 'event' => 'information_provided', 'metadata' => ['information_request_id' => $locked->id]]);

            $result = $locked->fresh(['request.requester', 'request.assignee']);

            return $result;
        });
    }
}
