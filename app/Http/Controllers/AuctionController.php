<?php

namespace App\Http\Controllers;

use App\Models\Picture;
use App\Models\User;
use App\Services\UserBanService;

class AuctionController extends Controller
{
    public function index()
    {
        $user = session()->has('user_id') ? User::find(session('user_id')) : null;
        $ban = $user ? UserBanService::processAuctionPaymentViolations($user) : null;
        $isBlocked = (bool) $ban;
        $restrictionMessage = $ban ? UserBanService::getRestrictionMessage($ban) : null;
        $bannedUntil = $ban?->banned_until;

        $auctions = Picture::where('status', 'approved')
            ->where('listing_type', 'auction')
            ->whereNotNull('auction_ends_at')
            ->where('auction_ends_at', '>', now())
            ->whereDoesntHave('orders', function ($query) {
                $query->where('payment_status', 'succeeded');
            })
            ->with(['user', 'genre', 'style', 'era', 'latestAuctionBid.user'])
            ->withCount('auctionBids')
            ->when(session()->has('user_id'), function ($query) {
                $query->with(['auctionBids' => function ($bidsQuery) {
                    $bidsQuery
                        ->where('user_id', session('user_id'))
                        ->orderByDesc('created_at');
                }]);
            })
            ->orderBy('auction_ends_at')
            ->get();

        return view('auction.index', compact('auctions', 'isBlocked', 'restrictionMessage', 'bannedUntil'));
    }
}
