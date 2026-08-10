<?php

namespace App\Services\Comments;

use App\Models\Comment;
use App\Models\User;

class CommentReplyService
{
    public function __construct(private CommentService $comments) {}

    public function create(Comment $parent, User $author, string $body, array $mentions = []): Comment
    {
        if ($parent->parent_id || $parent->trashed()) {
            abort(422, 'Solo se permiten respuestas de un nivel.');
        }

        return $this->comments->create($parent->commentable, $author, $body, $parent->visibility, $mentions, $parent->id);
    }
}
