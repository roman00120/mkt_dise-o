<?php

namespace App\Policies;

use App\Models\User;

class ReportPolicy
{
    public function view(User $user): bool
    {
        return $user->isActive() && ($user->hasRole('admin', 'supervisor', 'marketing', 'creative', 'design', 'video', 'render'));
    }

    public function executive(User $user): bool
    {
        return $user->isActive() && $user->hasRole('admin');
    }

    public function operations(User $user): bool
    {
        return $user->isActive() && $user->hasRole('admin', 'supervisor');
    }

    public function personal(User $user): bool
    {
        return $user->isActive() && $user->hasRole('marketing', 'creative', 'design', 'video', 'render');
    }

    public function save(User $user): bool
    {
        return $user->isActive();
    }

    public function schedule(User $user): bool
    {
        return $user->isActive() && $user->hasRole('admin');
    }
}
