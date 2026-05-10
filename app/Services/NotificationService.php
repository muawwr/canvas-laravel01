<?php

namespace App\Services;

use App\Models\UserNotification;

class NotificationService
{
    public static function push(
        int $userId,
        string $type,
        string $title,
        string $message,
        ?string $url = null,
        ?int $pictureId = null,
        ?int $orderId = null
    ): UserNotification {
        return UserNotification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'url' => $url,
            'picture_id' => $pictureId,
            'order_id' => $orderId,
        ]);
    }

    public static function pushOnce(
        int $userId,
        string $type,
        string $title,
        string $message,
        ?string $url = null,
        ?int $pictureId = null,
        ?int $orderId = null
    ): UserNotification {
        $notification = UserNotification::where('user_id', $userId)
            ->where('type', $type)
            ->when($pictureId !== null, fn ($query) => $query->where('picture_id', $pictureId))
            ->when($orderId !== null, fn ($query) => $query->where('order_id', $orderId))
            ->first();

        if ($notification) {
            return $notification;
        }

        return self::push($userId, $type, $title, $message, $url, $pictureId, $orderId);
    }
}
