<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\DeliverableVersion;
use App\Models\User;

class DeliverableVersionPolicy
{
    public function view(User $user, DeliverableVersion $version): bool
    {
        return $user->can('view', $version->deliverable);
    }

    public function create(User $user, DeliverableVersion $version): bool
    {
        return $version->deliverable->request->assignee_id === $user->id || $user->hasRole(UserRole::ADMIN, UserRole::SUPERVISOR);
    }

    public function update(User $user, DeliverableVersion $version): bool
    {
        return $version->isEditable() && $this->create($user, $version);
    }

    public function uploadFile(User $user, DeliverableVersion $version): bool
    {
        return $this->update($user, $version);
    }

    public function deleteFile(User $user, DeliverableVersion $version): bool
    {
        return $this->update($user, $version);
    }

    public function submitInternal(User $user, DeliverableVersion $version): bool
    {
        return $this->create($user, $version);
    }

    public function internalApprove(User $user, DeliverableVersion $version): bool
    {
        return $user->hasRole(UserRole::ADMIN, UserRole::SUPERVISOR);
    }

    public function requestInternalChanges(User $user, DeliverableVersion $version): bool
    {
        return $user->hasRole(UserRole::ADMIN, UserRole::SUPERVISOR);
    }

    public function sendMarketing(User $user, DeliverableVersion $version): bool
    {
        return $user->hasRole(UserRole::ADMIN, UserRole::SUPERVISOR);
    }

    public function approveMarketing(User $user, DeliverableVersion $version): bool
    {
        return $version->deliverable->request->requester_id === $user->id;
    }

    public function requestMarketingCorrections(User $user, DeliverableVersion $version): bool
    {
        return $version->deliverable->request->requester_id === $user->id;
    }
}
