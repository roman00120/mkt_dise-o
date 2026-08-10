<?php

namespace App\Policies;

use App\Models\User;

class HealthDetailsPolicy
{
    public function view(User $user): bool
    {
        return $user->isActive() && $user->hasRole('admin');
    }
}
