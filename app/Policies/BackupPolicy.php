<?php

namespace App\Policies;

use App\Models\User;

class BackupPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isActive() && $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function verify(User $user): bool
    {
        return $this->viewAny($user);
    }
}
