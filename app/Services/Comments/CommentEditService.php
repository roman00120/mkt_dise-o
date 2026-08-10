<?php

namespace App\Services\Comments;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CommentEditService
{
    public function update(Comment $comment, User $user, string $body, array $mentions = []): Comment
    {
        abort_unless($comment->user_id === $user->id && ! $comment->trashed() && $comment->created_at->gt(now()->subMinutes(15)), 403);

        return DB::transaction(function () use ($comment, $user, $body, $mentions): Comment {
            $comment->revisions()->create(['edited_by' => $user->id, 'previous_body' => $comment->body]);
            $comment->update(['body' => trim($body), 'edited_at' => now()]);
            $comment->mentions()->delete();
            app(CommentMentionService::class)->sync($comment, $mentions);

            return $comment->fresh(['author', 'mentions.user', 'attachments']);
        });
    }
}
