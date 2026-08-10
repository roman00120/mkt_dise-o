<?php

namespace App\Services\Notifications;

use App\Models\Deliverable;
use App\Models\User;

class NotificationLinkResolver
{
    public function resolve(array $data, User $user): ?string
    {
        $type = $data['entity_type'] ?? null;
        $id = $data['entity_id'] ?? null;
        if (! $type || ! $id) {
            return null;
        }

        return match ($type) {
            'creative_request' => ($user->hasRole('marketing') || $user->hasRole('admin', 'supervisor'))
                ? route('app.requests.show', $id)
                : route('creative.requests.show', $id),
            'deliverable' => $this->deliverableUrl($id, $user),
            default => null,
        };
    }

    private function deliverableUrl(string|int $id, User $user): ?string
    {
        $deliverable = Deliverable::query()->with('request')->find($id);
        if (! $deliverable?->request) {
            return null;
        }

        return ($user->hasRole('marketing') || $user->hasRole('admin', 'supervisor'))
            ? route('app.requests.deliverables.show', [$deliverable->request, $deliverable])
            : route('creative.requests.deliverable.show', [$deliverable->request, $deliverable]);
    }
}
