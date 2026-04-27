<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Genre;
use App\Models\Style;
use App\Models\Era;

class CategoryApiController extends Controller
{
    public function handle(Request $request)
    {
        if (!session()->has('user_id') || session('user_role') != 2) {
            return response()->json(['success' => false, 'message' => 'Нет доступа'], 403);
        }

        $action = $request->action;
        $type = $request->type;
        $name = trim($request->name ?? '');
        $id = intval($request->id ?? 0);

        if ($action === 'add') {
            if (empty($name)) {
                return response()->json(['success' => false, 'message' => 'Введите название категории']);
            }

            $model = match ($type) {
                    'genre' => Genre::class ,
                    'style' => Style::class ,
                    'era' => Era::class ,
                    default => null,
                };

            if (!$model) {
                return response()->json(['success' => false, 'message' => 'Некорректный тип категории']);
            }

            $item = $model::create(['name' => $name]);
            return response()->json(['success' => true, 'id' => $item->id, 'message' => 'Категория добавлена']);

        }
        elseif ($action === 'delete') {
            $model = match ($type) {
                    'genre' => Genre::class ,
                    'style' => Style::class ,
                    'era' => Era::class ,
                    default => null,
                };

            if (!$model) {
                return response()->json(['success' => false, 'message' => 'Некорректный тип категории']);
            }

            $model::where('id', $id)->delete();
            return response()->json(['success' => true, 'message' => 'Категория удалена']);
        }

        return response()->json(['success' => false, 'message' => 'Некорректное действие']);
    }
}
