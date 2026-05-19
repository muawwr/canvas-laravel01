<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Picture;
use App\Models\User;
use App\Models\UserNotification;
use App\Models\Cart;
use Illuminate\Support\Facades\Schema;

class UserBanService
{
    public static function getRestrictionMessage(User $user): ?string
    {
        $activeBan = self::getActiveBan($user);
        if (!$activeBan) {
            return null;
        }

        return 'Покупки временно недоступны до ' . $activeBan->banned_until->format('d.m.Y H:i') . ' из-за неоплаты выигранной аукционной картины.';
    }

    public static function getActiveBan(User $user): ?User
    {
        if (!$user->banned_until) {
            return null;
        }

        if ($user->banned_until->isPast()) {
            $user->update(['banned_until' => null]);
            return null;
        }

        return $user;
    }

    public static function processAuctionPaymentViolations(User $user): ?User
    {
        $activeBan = self::getActiveBan($user);
        if ($activeBan) {
            self::ensureBlockNotification($activeBan);
            return $activeBan;
        }

        $overduePictures = Picture::where('status', 'approved')
            ->where('listing_type', 'auction')
            ->whereNotNull('auction_ends_at')
            ->where('auction_ends_at', '<=', now()->subDay())
            ->where('user_id', '!=', $user->id)
            ->whereHas('latestAuctionBid', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereDoesntHave('orders', function ($query) {
                $query->where('payment_status', 'succeeded');
            })
            ->whereDoesntHave('orders', function ($query) use ($user) {
                $query->where('buyer_id', $user->id)
                    ->where('payment_status', 'canceled');
            })
            ->with('user')
            ->get();

        if ($overduePictures->isEmpty()) {
            return null;
        }

        foreach ($overduePictures as $picture) {
            Order::create([
                'buyer_id' => $user->id,
                'seller_id' => $picture->user_id,
                'picture_id' => $picture->id,
                'price' => $picture->price,
                'pickup_point' => 'Не оплачено в течение 24 часов',
                'recipient_name' => $user->name,
                'unique_code' => 'BAN' . random_int(1000, 9999),
                'status' => 'waiting_shipment',
                'payment_status' => 'canceled',
            ]);
        }

        $user->update([
            'banned_until' => now()->addDays(7),
        ]);

        $freshUser = $user->fresh();

        self::ensureBlockNotification($freshUser);

        return $freshUser;
    }

    public static function processAuctionWins(User $user): void
    {
        $wonPictures = Picture::where('status', 'approved')
            ->where('listing_type', 'auction')
            ->whereNotNull('auction_ends_at')
            ->where('auction_ends_at', '<=', now())
            ->where('user_id', '!=', $user->id)
            ->whereHas('latestAuctionBid', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereDoesntHave('orders', function ($query) {
                $query->where('payment_status', 'succeeded');
            })
            ->with('latestAuctionBid')
            ->get();

        foreach ($wonPictures as $picture) {
            Cart::firstOrCreate([
                'user_id' => $user->id,
                'picture_id' => $picture->id,
            ]);

            if (!Schema::hasTable('user_notifications')) {
                continue;
            }

            NotificationService::pushOnce(
                $user->id,
                'auction_winner',
                'Аукцион завершен',
                'Вы победили в аукционе по картине "' . $picture->name . '". Перейдите в корзину для оформления заказа. В случае неоплаты в течение 24 часов доступ к покупкам будет ограничен на 7 дней.',
                url('/cart'),
                $picture->id
            );
        }
    }

    private static function ensureBlockNotification(User $user): void
    {
        if (!$user->banned_until || !Schema::hasTable('user_notifications')) {
            return;
        }

        $banStartedAt = $user->banned_until->copy()->subDays(7)->subMinute();

        $alreadyNotified = UserNotification::where('user_id', $user->id)
            ->where('type', 'user_blocked')
            ->where('created_at', '>=', $banStartedAt)
            ->exists();

        if ($alreadyNotified) {
            return;
        }

        NotificationService::push(
            $user->id,
            'user_blocked',
            'Доступ к покупкам ограничен',
            'Вы временно заблокированы до ' . $user->banned_until->format('d.m.Y H:i') . ' из-за неоплаты выигранной аукционной картины.',
            url('/cart')
        );
    }
}
