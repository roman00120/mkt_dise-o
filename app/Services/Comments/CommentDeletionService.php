<?php

namespace App\Services\Comments;

use App\Models\Comment;
use App\Models\User;

class CommentDeletionService
{
    public function delete(Comment $comment, User $user): void
    {
        abort_unless($comment->user_id === $user->id && ! $comment->trashed(), 403);
        $comment->update(['deleted_by' => $user->id]);
        $comment->delete();
    }
}
