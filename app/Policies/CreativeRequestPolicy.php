<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\CreativeRequest;
use App\Models\User;

class CreativeRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(UserRole::MARKETING, UserRole::ADMIN, UserRole::SUPERVISOR);
    }

    public function viewCreativePanel(User $user): bool
    {
        return $user->isActive() && $user->hasRole(UserRole::ADMIN, UserRole::CREATIVE, UserRole::DESIGN, UserRole::VIDEO, UserRole::RENDER, UserRole::SUPERVISOR);
    }

    public function viewCreative(User $user, CreativeRequest $request): bool
    {
        if (! $this->viewCreativePanel($user)) {
            return false;
        }

        return $user->hasRole(UserRole::ADMIN, UserRole::SUPERVISOR, UserRole::CREATIVE) || $request->service->value === $user->role->value;
    }

    public function view(User $user, CreativeRequest $request): bool
    {
        return $user->id === $request->requester_id || $user->hasRole(UserRole::ADMIN, UserRole::SUPERVISOR);
    }

    public function duplicate(User $user, CreativeRequest $request): bool
    {
        return $user->id === $request->requester_id && ! $request->trashed();
    }

    public function cancel(User $user, CreativeRequest $request): bool
    {
        return $user->id === $request->requester_id && in_array($request->status?->value, ['pending', 'in_validation', 'waiting_for_information', 'cancelled'], true);
    }

    public function downloadFile(User $user, CreativeRequest $request): bool
    {
        return $this->view($user, $request) || $this->viewCreative($user, $request);
    }

    public function resumeDraft(User $user, CreativeRequest $request): bool
    {
        return $this->update($user, $request);
    }

    public function update(User $user, CreativeRequest $request): bool
    {
        return $user->id === $request->requester_id && $request->isDraft();
    }

    public function deleteFile(User $user, CreativeRequest $request): bool
    {
        return $this->update($user, $request);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(UserRole::MARKETING);
    }

    public function validate(User $user, CreativeRequest $request): bool
    {
        return $this->viewCreative($user, $request) && $user->hasRole(UserRole::ADMIN, UserRole::SUPERVISOR) && $request->status?->value === 'in_validation';
    }

    public function assign(User $user, CreativeRequest $request): bool
    {
        return $this->viewCreative($user, $request) && $user->hasRole(UserRole::ADMIN, UserRole::SUPERVISOR) && in_array($request->status?->value, ['in_validation', 'assigned'], true);
    }

    public function reassign(User $user, CreativeRequest $request): bool
    {
        return $this->viewCreative($user, $request) && $user->hasRole(UserRole::SUPERVISOR) && in_array($request->status?->value, ['assigned', 'in_progress', 'waiting_for_information', 'internal_review'], true);
    }

    public function updateOperationalPriority(User $user, CreativeRequest $request): bool
    {
        return $this->viewCreative($user, $request) && $user->hasRole(UserRole::SUPERVISOR);
    }

    public function updateInternalDueDate(User $user, CreativeRequest $request): bool
    {
        return $this->viewCreative($user, $request) && $user->hasRole(UserRole::SUPERVISOR);
    }

    public function transition(User $user, CreativeRequest $request): bool
    {
        return ($this->viewCreative($user, $request) && ($user->hasRole(UserRole::ADMIN, UserRole::SUPERVISOR) || $request->assignee_id === $user->id)) || ($user->id === $request->requester_id && in_array($request->status?->value, ['marketing_review'], true));
    }

    public function requestInformation(User $user, CreativeRequest $request): bool
    {
        return $this->transition($user, $request);
    }

    public function reject(User $user, CreativeRequest $request): bool
    {
        return $this->viewCreative($user, $request) && $user->hasRole(UserRole::SUPERVISOR) && in_array($request->status?->value, ['pending', 'in_validation'], true);
    }

    public function viewWorkload(User $user): bool
    {
        return $this->viewCreativePanel($user);
    }
}
