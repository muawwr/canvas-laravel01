<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Picture;
use App\Models\Favorite;
use App\Models\Order;
use App\Models\User;
use App\Models\Genre;
use App\Models\Style;
use App\Models\Era;
use App\Services\UserBanService;
use Illuminate\Support\Facades\Schema;

class PictureController extends Controller
{
    public function show(Request $request, $id)
    {
        $is_logged_in = session()->has('user_id');
        $user_avatar = session('user_img', 'assets/images/account/mainUser.png');
        $user_name = session('user_name', 'Гость');

        $currentUser = $is_logged_in ? User::find(session('user_id')) : null;
        $cartRestrictionMessage = $currentUser ? UserBanService::getRestrictionMessage($currentUser) : null;
        $isUserBlocked = (bool) $cartRestrictionMessage;

        $pictureQuery = Picture::with(['user', 'genre', 'style', 'era', 'latestAuctionBid.user'])
            ->withCount('auctionBids')
            ->when(session()->has('user_id'), function ($query) {
                $query->with(['auctionBids' => function ($bidsQuery) {
                    $bidsQuery
                        ->where('user_id', session('user_id'))
                        ->orderByDesc('created_at');
                }]);
            })
            ->where('status', 'approved');

        if (Schema::hasColumn('pictures', 'hidden_after_sale')) {
            $pictureQuery->where('hidden_after_sale', false);
        }

        $picture = $pictureQuery->findOrFail($id);

        // Количество лайков
        $likes_count = Favorite::where('picture_id', $id)->count();

        // Проверка в избранном
        $is_in_favorites = false;
        if ($is_logged_in) {
            $is_in_favorites = Favorite::where('user_id', session('user_id'))
                ->where('picture_id', $id)->exists();
        }

        // Проверка продано
        $is_sold = $picture->show_sold_badge
            || Order::where('picture_id', $id)
                ->where('payment_status', 'succeeded')
                ->exists();

        $fallbackBackUrl = $picture->listing_type === 'auction' ? url('/auction') : url('/gallery');
        $requestedBackUrl = (string) $request->query('return_to', '');
        $backUrl = $fallbackBackUrl;

        if ($requestedBackUrl !== '' && str_starts_with($requestedBackUrl, url('/'))) {
            $backUrl = $requestedBackUrl;
        } else {
            $previousUrl = url()->previous();
            if (is_string($previousUrl) && !str_contains($previousUrl, '/picture/')) {
                $backUrl = $previousUrl;
            }
        }

        return view('picture.show', compact(
            'is_logged_in', 'user_avatar', 'user_name',
            'picture', 'likes_count', 'is_in_favorites', 'is_sold', 'backUrl',
            'isUserBlocked', 'cartRestrictionMessage'
        ));
    }

    public function create(Request $request)
    {
        $is_logged_in = session()->has('user_id');
        $user_avatar = session('user_img', 'assets/images/account/mainUser.png');
        $user_name = session('user_name', 'Гость');

        $genres = Genre::orderBy('name')->get();
        $styles = Style::orderBy('name')->get();
        $eras = Era::orderBy('name')->get();
        $auctionFromPicture = null;

        if ($request->filled('auction_from')) {
            $auctionFromPicture = Picture::with(['genre', 'style', 'era'])
                ->where('user_id', session('user_id'))
                ->where('status', 'approved')
                ->where('listing_type', 'gallery')
                ->findOrFail((int) $request->query('auction_from'));

            $isSold = $auctionFromPicture->show_sold_badge
                || Order::where('picture_id', $auctionFromPicture->id)
                    ->where('payment_status', 'succeeded')
                    ->exists();

            if ($isSold) {
                abort(404);
            }
        }

        return view('picture.create', compact(
            'is_logged_in', 'user_avatar', 'user_name',
            'genres', 'styles', 'eras', 'auctionFromPicture'
        ));
    }

    public function edit($id)
    {
        $is_logged_in = session()->has('user_id');
        $user_avatar = session('user_img', 'assets/images/account/mainUser.png');
        $user_name = session('user_name', 'Гость');

        $picture = Picture::where('user_id', session('user_id'))->findOrFail($id);

        $genres = Genre::orderBy('name')->get();
        $styles = Style::orderBy('name')->get();
        $eras = Era::orderBy('name')->get();

        return view('picture.edit', compact(
            'is_logged_in', 'user_avatar', 'user_name',
            'picture', 'genres', 'styles', 'eras'
        ));
    }
}
