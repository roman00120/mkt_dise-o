<?php

namespace App\Policies;

use App\Models\User;

class FailedJobPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isActive() && $user->hasRole('admin');
    }

    public function retry(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function forget(User $user): bool
    {
        return $this->viewAny($user);
    }
}
