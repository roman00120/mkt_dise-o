<?php

namespace App\Services\Requests;

use App\Enums\UserRole;
use App\Models\CreativeRequest;
use App\Models\User;
use App\Notifications\CreativeRequestAssignedNotification;
use App\Services\Notifications\BusinessNotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RequestAssignmentService
{
    public function assign(CreativeRequest $request, User $assignee, User $actor, ?string $observation = null, ?BusinessNotificationService $notifications = null): CreativeRequest
    {
        $this->assertValid($request, $assignee);

        return DB::transaction(function () use ($request, $assignee, $actor, $observation, $notifications): CreativeRequest {
            $locked = CreativeRequest::query()->lockForUpdate()->findOrFail($request->id);
            $locked->update(['assignee_id' => $assignee->id, 'assigned_by' => $actor->id, 'assigned_at' => now()]);
            $locked->events()->create(['actor_id' => $actor->id, 'event' => $locked->status->value === 'in_validation' ? 'request_assigned' : 'request_reassigned', 'metadata' => ['assignee' => $assignee->name, 'observation' => $observation]]);

            $result = $locked->fresh(['requester', 'assignee']);

            try {
                $assignee->notify(new CreativeRequestAssignedNotification($result, $actor));
            } catch (\Throwable $e) {
                logger()->error('Failed sending request assigned notification: '.$e->getMessage());
            }

            if ($notifications) {
                DB::afterCommit(fn () => $notifications->request($result, 'assignment', 'Solicitud asignada', 'Se te asignó una nueva solicitud creativa.', $actor, [$assignee]));
            }

            return $result;
        });
    }

    public function assertValid(CreativeRequest $request, User $assignee): void
    {
        $role = UserRole::tryFrom($request->service->value);
        if (! $assignee->isActive() || ! $role || ! $assignee->hasRole(UserRole::CREATIVE, $role)) {
            throw ValidationException::withMessages(['assignee_id' => 'El responsable debe estar activo y pertenecer al servicio solicitado.']);
        }
    }
}
