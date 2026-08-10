<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class CollaborationNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $eventType,
        public string $title,
        public string $message,
        public ?string $url = null,
        public ?string $entityType = null,
        public string|int|null $entityId = null,
        public ?int $actorId = null,
        public string $icon = 'bell',
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'event_type' => $this->eventType,
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'entity_type' => $this->entityType,
            'entity_id' => $this->entityId,
            'actor_id' => $this->actorId,
            'icon' => $this->icon,
        ];
    }

    public function id(): string
    {
        return (string) Str::uuid();
    }
}
