<?php

namespace App\Policies;

use App\Models\DeliverableFile;
use App\Models\User;

class DeliverableFilePolicy
{
    public function view(User $user, DeliverableFile $file): bool
    {
        return $user->can('view', $file->version);
    }

    public function download(User $user, DeliverableFile $file): bool
    {
        return $this->view($user, $file) && ($user->hasRole('marketing') ? in_array($file->version->status->value, ['marketing_review', 'approved']) : true);
    }

    public function delete(User $user, DeliverableFile $file): bool
    {
        return $user->can('deleteFile', $file->version);
    }
}
