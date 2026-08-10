<?php

namespace App\Services\Notifications;

use App\Models\CreativeRequest;
use App\Models\Deliverable;
use App\Models\DeliverableVersion;
use App\Models\User;
use App\Notifications\CollaborationNotification;
use Illuminate\Support\Collection;

class BusinessNotificationService
{
    public function __construct(private NotificationPreferenceService $preferences) {}

    public function send(Collection|array $recipients, string $eventType, string $title, string $message, ?string $url, ?string $entityType, string|int|null $entityId, ?int $actorId = null): void
    {
        foreach (collect($recipients)->filter()->unique('id') as $recipient) {
            if ($recipient instanceof User && $recipient->isActive() && $this->preferences->enabled($recipient, $eventType) && $recipient->id !== $actorId) {
                $recipientUrl = $url;
                if (! $recipient->hasRole('marketing', 'admin')) {
                    $recipientUrl = $entityType === 'creative_request'
                        ? route('creative.requests.show', $entityId)
                        : ($entityType === 'deliverable' && ($deliverable = Deliverable::with('request')->find($entityId))
                            ? route('creative.requests.deliverable.show', [$deliverable->request, $deliverable])
                            : $url);
                }
                $recipient->notify(new CollaborationNotification($eventType, $title, $message, $recipientUrl, $entityType, $entityId, $actorId));
            }
        }
    }

    public function request(CreativeRequest $request, string $eventType, string $title, string $message, User $actor, array $recipients = []): void
    {
        $this->send($recipients ?: [$request->requester, $request->assignee], $eventType, $title, $message, route('app.requests.show', $request), 'creative_request', $request->id, $actor->id);
    }

    public function deliverable(DeliverableVersion $version, string $eventType, string $title, string $message, User $actor): void
    {
        $request = $version->deliverable->request;
        $this->send([$request->requester, $request->assignee], $eventType, $title, $message, route('app.requests.deliverables.show', [$request, $version->deliverable]), 'deliverable', $version->deliverable_id, $actor->id);
    }
}
