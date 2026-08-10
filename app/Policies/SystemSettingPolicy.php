<?php

namespace App\Policies;

use App\Models\User;

class SystemSettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isActive() && $user->hasRole('admin');
    }

    public function update(User $user): bool
    {
        return $this->viewAny($user);
    }
}
