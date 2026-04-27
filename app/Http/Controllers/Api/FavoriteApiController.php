<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Models\Picture;

class FavoriteApiController extends Controller
{
    public function handle(Request $request)
    {
        if (!session()->has('user_id')) {
            return response()->json(['success' => false, 'message' => 'Необходима авторизация'], 401);
        }

        $user_id = session('user_id');
        $action = $request->action;
        $picture_id = intval($request->picture_id ?? 0);

        if (!in_array($action, ['add', 'remove'])) {
            return response()->json(['success' => false, 'message' => 'Некорректное действие'], 400);
        }

        if ($picture_id <= 0) {
            return response()->json(['success' => false, 'message' => 'Некорректный ID картины'], 400);
        }

        $picture = Picture::where('id', $picture_id)->where('status', 'approved')->first();
        if (!$picture) {
            return response()->json(['success' => false, 'message' => 'Картина не найдена'], 404);
        }

        if ($action === 'add') {
            $exists = Favorite::where('user_id', $user_id)->where('picture_id', $picture_id)->exists();
            if ($exists) {
                return response()->json(['success' => false, 'message' => 'Картина уже в избранном']);
            }

            Favorite::create(['user_id' => $user_id, 'picture_id' => $picture_id]);
            $likes_count = Favorite::where('picture_id', $picture_id)->count();

            return response()->json(['success' => true, 'message' => 'Добавлено в избранное', 'action' => 'added', 'likes_count' => $likes_count]);

        }
        elseif ($action === 'remove') {
            $deleted = Favorite::where('user_id', $user_id)->where('picture_id', $picture_id)->delete();

            if ($deleted) {
                $likes_count = Favorite::where('picture_id', $picture_id)->count();
                return response()->json(['success' => true, 'message' => 'Удалено из избранного', 'action' => 'removed', 'likes_count' => $likes_count]);
            }
            return response()->json(['success' => false, 'message' => 'Картина не находилась в избранном']);
        }
    }
}
