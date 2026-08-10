<?php

namespace App\Policies;

use App\Models\User;

class SystemStatusPolicy
{
    public function view(User $user): bool
    {
        return $user->isActive() && $user->hasRole('admin');
    }
}
