<?php

namespace App\Services;

use App\Models\GpiUserNotification;
use App\Models\User;

class InAppNotificationService
{
    public function notify(
        User|int $user,
        string $title,
        string $message,
        ?string $actionUrl = null,
        ?string $actionLabel = null
    ): GpiUserNotification {
        $userId = $user instanceof User ? $user->id : $user;

        return GpiUserNotification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'action_url' => $actionUrl,
            'action_label' => $actionLabel,
        ]);
    }

    public function unreadCount(User $user): int
    {
        return $user->gpiNotifications()->whereNull('read_at')->count();
    }
}
