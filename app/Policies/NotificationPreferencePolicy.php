<?php

namespace App\Policies;

use App\Models\User;

class NotificationPreferencePolicy
{
    public function view(User $user, User $target): bool
    {
        return $user->id === $target->id;
    }

    public function update(User $user, User $target): bool
    {
        return $this->view($user, $target);
    }
}
