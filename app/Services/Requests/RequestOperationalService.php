<?php

namespace App\Services\Requests;

use App\Enums\RequestPriority;
use App\Models\CreativeRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RequestOperationalService
{
    public function health(CreativeRequest $request): string
    {
        if ($request->status?->value === 'waiting_for_information') {
            return 'Bloqueada';
        }
        if (in_array($request->status?->value, ['approved', 'completed'], true)) {
            return 'Completada';
        }
        if (! $request->internal_due_date) {
            return 'Sin fecha interna';
        }
        if ($request->internal_due_date->isBefore(today())) {
            return 'Vencida';
        }

        return today()->diffInDays($request->internal_due_date) <= 2 ? 'Próxima a vencer' : 'En tiempo';
    }

    public function priority(CreativeRequest $request, User $actor, RequestPriority $priority): CreativeRequest
    {
        return $this->update($request, $actor, ['operational_priority' => $priority], 'operational_priority_changed', ['priority' => $priority->value]);
    }

    public function internalDate(CreativeRequest $request, User $actor, string $date): CreativeRequest
    {
        if ($date < today()->toDateString()) {
            throw ValidationException::withMessages(['internal_due_date' => 'La fecha interna no puede ser anterior a hoy.']);
        }

        return $this->update($request, $actor, ['internal_due_date' => $date], 'internal_due_date_changed', ['internal_due_date' => $date]);
    }

    private function update(CreativeRequest $request, User $actor, array $values, string $event, array $metadata): CreativeRequest
    {
        return DB::transaction(function () use ($request, $actor, $values, $event, $metadata): CreativeRequest {
            $locked = CreativeRequest::query()->lockForUpdate()->findOrFail($request->id);
            $locked->update($values);
            $locked->events()->create(['actor_id' => $actor->id, 'event' => $event, 'metadata' => $metadata]);

            return $locked->fresh();
        });
    }
}
