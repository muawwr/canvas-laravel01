<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Picture;
use App\Models\Genre;
use App\Models\Style;
use App\Models\Era;
use Illuminate\Support\Facades\Schema;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $is_logged_in = session()->has('user_id');
        $user_avatar = session('user_img', 'assets/images/account/mainUser.png');
        $user_name = session('user_name', 'Гость');

        $genres = Genre::orderBy('name')->get();
        $styles = Style::orderBy('name')->get();
        $eras = Era::orderBy('name')->get();

        $query = Picture::where('status', 'approved')
            ->where('listing_type', 'gallery')
            ->with(['user', 'genre', 'style', 'era'])
            ->withCount('favoriteEntries');

        if (Schema::hasColumn('pictures', 'hidden_after_sale')) {
            $query->where('hidden_after_sale', false);
        }

        // Фильтрация
        if ($request->genre_id) {
            $query->where('genre_id', $request->genre_id);
        }
        if ($request->style_id) {
            $query->where('style_id', $request->style_id);
        }
        if ($request->era_id) {
            $query->where('era_id', $request->era_id);
        }

        // Поиск
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($q2) use ($search) {
                    $q2->where('name', 'LIKE', "%{$search}%");
                }
                );
            });
        }

        // Сортировка
        $sort = $request->sort ?? 'newest';
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->orderByDesc('favorite_entries_count');
                break;
            case 'newest':
            default:
                $query->orderByDesc('created_at');
                break;
        }

        $pictures = $query->paginate(12)->withQueryString();

        $pictures->setCollection(
            $pictures->getCollection()->map(function ($picture) {
                $picture->is_sold = $picture->show_sold_badge
                    || $picture->orders()->where('payment_status', 'succeeded')->exists();

                return $picture;
            })
        );

        return view('gallery', compact(
            'is_logged_in', 'user_avatar', 'user_name',
            'pictures', 'genres', 'styles', 'eras'
        ));
    }
}
