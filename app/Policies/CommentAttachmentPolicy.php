<?php

namespace App\Policies;

use App\Models\CommentAttachment;
use App\Models\User;

class CommentAttachmentPolicy
{
    public function view(User $user, CommentAttachment $attachment): bool
    {
        return $user->can('view', $attachment->comment);
    }

    public function download(User $user, CommentAttachment $attachment): bool
    {
        return $this->view($user, $attachment);
    }

    public function delete(User $user, CommentAttachment $attachment): bool
    {
        return $attachment->uploaded_by === $user->id && ! $attachment->comment->trashed();
    }
}
