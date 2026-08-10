<?php

namespace App\Policies;

use App\Models\RequestType;
use App\Models\User;

class RequestTypePolicy
{
    public function before(User $user): ?bool
    {
        return $user->isActive() && $user->hasRole('admin') ? null : false;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, RequestType $type): bool
    {
        return $user->hasRole('admin');
    }
}
