<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class ProfileApiController extends Controller
{
    public function update(Request $request)
    {
        if (!session()->has('user_id')) {
            return response()->json(['success' => false, 'message' => 'Необходима авторизация'], 401);
        }

        $user_id = session('user_id');
        $user = User::find($user_id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Пользователь не найден']);
        }

        $name = trim($request->name ?? '');

        if (empty($name) || mb_strlen($name) < 2) {
            return response()->json(['success' => false, 'message' => 'Имя должно содержать минимум 2 символа']);
        }

        if (mb_strlen($name) > 50) {
            return response()->json(['success' => false, 'message' => 'Имя не должно превышать 50 символов']);
        }

        $user->name = $name;

        $avatar_url = null;

        // Обработка аватара
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $file = $request->file('avatar');

            if (!str_starts_with($file->getMimeType(), 'image/')) {
                return response()->json(['success' => false, 'message' => 'Пожалуйста, выберите изображение']);
            }

            if ($file->getSize() > 5 * 1024 * 1024) {
                return response()->json(['success' => false, 'message' => 'Размер изображения не должен превышать 5 МБ']);
            }

            $filename = 'avatar_' . $user_id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/avatars'), $filename);
            $avatar_url = 'uploads/avatars/' . $filename;
            $user->img = $avatar_url;
        }

        $user->save();

        // Обновляем сессию
        session(['user_name' => $name]);
        if ($avatar_url) {
            session(['user_img' => $avatar_url]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Профиль обновлен',
            'avatar_url' => $avatar_url,
        ]);
    }
}
