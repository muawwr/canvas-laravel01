<?php

namespace App\Services;

use App\Models\UserNotification;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

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
        $data = [
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'url' => $url,
            'picture_id' => $pictureId,
            'order_id' => $orderId,
        ];

        try {
            return UserNotification::create($data);
        } catch (QueryException $e) {
            if (($e->errorInfo[1] ?? null) !== 1062) {
                throw $e;
            }

            $now = now();
            $id = ((int) DB::table('user_notifications')->max('id')) + 1;

            DB::table('user_notifications')->insert($data + [
                'id' => $id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            return UserNotification::findOrFail($id);
        }
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
