<?php

namespace App\Services\Notifications;

use App\Models\Comment;
use App\Notifications\CollaborationNotification;
use Illuminate\Support\Collection;

class NotificationDispatchService
{
    public function __construct(private NotificationPreferenceService $preferences) {}

    public function comment(Comment $comment, Collection $recipients, string $eventType = 'comment'): void
    {
        foreach ($recipients as $recipient) {
            if (! $this->preferences->enabled($recipient, $eventType)) {
                continue;
            }
            $recipient->notify(new CollaborationNotification(
                $eventType,
                $comment->isInternal() ? 'Nueva nota interna' : 'Nuevo comentario',
                $comment->author->name.' agregó actividad en una conversación.',
                null,
                $comment->commentable_type,
                $comment->commentable_id,
                $comment->user_id,
                $comment->isInternal() ? 'lock' : 'message-circle',
            ));
        }
    }
}
