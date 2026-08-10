<?php

namespace App\Services\Comments;

use App\Models\Comment;
use App\Models\User;
use App\Services\Notifications\NotificationDispatchService;
use App\Services\Notifications\NotificationRecipientResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CommentService
{
    public function __construct(private CommentMentionService $mentions, private NotificationRecipientResolver $recipients, private NotificationDispatchService $notifications) {}

    public function create(Model $resource, User $author, string $body, string $visibility = 'public', array $mentionIds = [], ?int $parentId = null): Comment
    {
        return DB::transaction(function () use ($resource, $author, $body, $visibility, $mentionIds, $parentId): Comment {
            $parent = $parentId ? Comment::query()->whereKey($parentId)->lockForUpdate()->firstOrFail() : null;
            if ($parent && ($parent->commentable_type !== $resource->getMorphClass() || $parent->commentable_id !== $resource->getKey() || $parent->parent_id || $parent->visibility !== $visibility || $parent->trashed())) {
                abort(422, 'La respuesta no pertenece a esta conversación.');
            }
            $comment = $resource->comments()->create(['uuid' => (string) Str::uuid(), 'user_id' => $author->id, 'parent_id' => $parent?->id, 'visibility' => $visibility, 'body' => trim($body)]);
            $this->mentions->sync($comment, $mentionIds);
            $comment->load('author', 'mentions.user', 'commentable');
            DB::afterCommit(fn () => $this->notifications->comment($comment, $this->recipients->forComment($comment), $visibility === 'internal' ? 'comment' : 'comment'));

            return $comment;
        });
    }
}
