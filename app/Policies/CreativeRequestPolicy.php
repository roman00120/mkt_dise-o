<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\CreativeRequest;
use App\Models\User;

class CreativeRequestPolicy
{
    public function view(User $user, CreativeRequest $request): bool
    {
        return $user->id === $request->requester_id || $user->hasRole(UserRole::ADMIN, UserRole::SUPERVISOR);
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
}
