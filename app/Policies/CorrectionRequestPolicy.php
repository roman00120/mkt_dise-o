<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\CorrectionRequest;
use App\Models\User;

class CorrectionRequestPolicy
{
    public function view(User $user, CorrectionRequest $correction): bool
    {
        return $correction->type !== 'internal' && $user->id === $correction->request->requester_id || $user->hasRole(UserRole::SUPERVISOR) || $correction->request->assignee_id === $user->id;
    }

    public function createMarketing(User $user, CorrectionRequest $correction): bool
    {
        return $user->id === $correction->request->requester_id;
    }
}
