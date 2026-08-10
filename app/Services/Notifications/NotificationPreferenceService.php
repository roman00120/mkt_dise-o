<?php

namespace App\Services\Notifications;

use App\Models\NotificationPreference;
use App\Models\User;

class NotificationPreferenceService
{
    public function enabled(User $user, string $eventType): bool
    {
        $preference = $user->notificationPreferences()->where('event_type', $eventType)->first();

        return $preference?->in_app ?? true;
    }

    public function save(User $user, array $values): void
    {
        foreach (NotificationPreference::TYPES as $type) {
            $inApp = (bool) ($values[$type] ?? false);
            if (in_array($type, NotificationPreference::CRITICAL, true)) {
                $inApp = true;
            }
            $user->notificationPreferences()->updateOrCreate(['event_type' => $type], ['in_app' => $inApp, 'email' => false]);
        }
    }
}
