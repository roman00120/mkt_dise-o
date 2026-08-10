<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function before(User $user): ?bool
    {
        return $user->isActive() && $user->hasRole('admin') ? null : false;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, User $target): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, User $target): bool
    {
        return $user->hasRole('admin');
    }

    public function manageStatus(User $user, User $target): bool
    {
        return $user->hasRole('admin') && $user->id !== $target->id;
    }
}
