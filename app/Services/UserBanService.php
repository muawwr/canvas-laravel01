<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Picture;
use App\Models\User;

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

        return $user->fresh();
    }
}
