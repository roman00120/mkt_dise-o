<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Deliverable;
use App\Models\User;

class DeliverablePolicy
{
    public function view(User $user, Deliverable $deliverable): bool
    {
        $request = $deliverable->request;

        return ($user->hasRole(UserRole::MARKETING) ? $user->can('view', $request) : $user->can('viewCreative', $request)) && ($user->hasRole(UserRole::ADMIN, UserRole::SUPERVISOR, UserRole::CREATIVE) || $request->service->value === $user->role->value || $user->id === $request->requester_id);
    }

    public function create(User $user, Deliverable $deliverable): bool
    {
        return $deliverable->request->assignee_id === $user->id || $user->hasRole(UserRole::ADMIN, UserRole::SUPERVISOR);
    }

    public function update(User $user, Deliverable $deliverable): bool
    {
        return $this->create($user, $deliverable) && $deliverable->status?->value !== 'approved';
    }

    public function submitInternal(User $user, Deliverable $deliverable): bool
    {
        return $this->create($user, $deliverable);
    }

    public function approveInternal(User $user, Deliverable $deliverable): bool
    {
        return $user->hasRole(UserRole::ADMIN, UserRole::SUPERVISOR);
    }

    public function requestInternalChanges(User $user, Deliverable $deliverable): bool
    {
        return $user->hasRole(UserRole::ADMIN, UserRole::SUPERVISOR);
    }

    public function sendToMarketing(User $user, Deliverable $deliverable): bool
    {
        return $user->hasRole(UserRole::ADMIN, UserRole::SUPERVISOR);
    }

    public function complete(User $user, Deliverable $deliverable): bool
    {
        return $user->hasRole(UserRole::ADMIN, UserRole::SUPERVISOR);
    }
}
