<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Picture;
use App\Models\User;
use App\Models\Order;
use App\Models\AuctionBid;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PictureApiController extends Controller
{
    public function store(Request $request)
    {
        if (!session()->has('user_id')) {
            return response()->json(['success' => false, 'message' => 'Необходима авторизация'], 403);
        }

        $user_id = session('user_id');

        try {
            $validator = Validator::make($request->all(), [
                'image' => ['required', 'file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:10240'],
                'width' => ['required', 'integer', 'min:1'],
                'height' => ['required', 'integer', 'min:1'],
                'name' => ['required', 'string', 'min:3', 'max:255'],
                'technique' => ['required', 'string', 'min:2', 'max:255'],
                'year' => ['required', 'integer', 'min:1000', 'max:' . date('Y')],
                'description' => ['required', 'string', 'min:10'],
                'genre_id' => ['required', 'integer', 'exists:genres,id'],
                'style_id' => ['required', 'integer', 'exists:styles,id'],
                'era_id' => ['required', 'integer', 'exists:eras,id'],
                'price' => ['required', 'integer', 'min:100'],
                'listing_type' => ['nullable', Rule::in(['gallery', 'auction'])],
                'auction_start_price' => ['required_if:listing_type,auction', 'nullable', 'integer', 'min:100'],
                'auction_min_step' => ['required_if:listing_type,auction', 'nullable', 'integer', 'min:50'],
                'auction_buyout_price' => ['nullable', 'integer', 'min:100', 'gte:auction_start_price'],
                'auction_duration_hours' => ['required_if:listing_type,auction', 'nullable', 'integer', 'min:1', 'max:720'],
            ], [
                'image.required' => 'Загрузите изображение картины',
                'image.file' => 'Не удалось обработать загруженный файл',
                'image.image' => 'Файл должен быть изображением',
                'image.mimes' => 'Допустимы только изображения JPEG, JPG, PNG и WEBP',
                'image.max' => 'Размер файла не должен превышать 10 МБ',
                'width.required' => 'Укажите ширину картины',
                'width.integer' => 'Ширина должна быть числом',
                'width.min' => 'Ширина должна быть больше 0',
                'height.required' => 'Укажите высоту картины',
                'height.integer' => 'Высота должна быть числом',
                'height.min' => 'Высота должна быть больше 0',
                'name.required' => 'Введите название картины',
                'name.min' => 'Название должно содержать минимум 3 символа',
                'name.max' => 'Название не должно превышать 255 символов',
                'technique.required' => 'Укажите технику написания',
                'technique.min' => 'Техника должна содержать минимум 2 символа',
                'technique.max' => 'Техника не должна превышать 255 символов',
                'year.required' => 'Укажите год написания',
                'year.integer' => 'Год должен быть числом',
                'year.min' => 'Год должен быть не меньше 1000',
                'year.max' => 'Укажите корректный год написания',
                'description.required' => 'Введите описание картины',
                'description.min' => 'Описание должно содержать минимум 10 символов',
                'genre_id.required' => 'Выберите жанр',
                'genre_id.exists' => 'Выбранный жанр не найден',
                'style_id.required' => 'Выберите стиль',
                'style_id.exists' => 'Выбранный стиль не найден',
                'era_id.required' => 'Выберите эпоху',
                'era_id.exists' => 'Выбранная эпоха не найдена',
                'price.required' => 'Укажите цену картины',
                'price.integer' => 'Цена должна быть числом',
                'price.min' => 'Минимальная цена - 100 ₽',
                'listing_type.in' => 'Выберите корректный способ размещения',
                'auction_start_price.required_if' => 'Укажите стартовую цену аукциона',
                'auction_start_price.min' => 'Стартовая цена аукциона должна быть не меньше 100 ₽',
                'auction_min_step.required_if' => 'Укажите минимальный шаг ставки',
                'auction_min_step.min' => 'Минимальный шаг ставки - 50 ₽',
                'auction_buyout_price.gte' => 'Блиц-цена не может быть ниже стартовой цены',
                'auction_duration_hours.required_if' => 'Укажите длительность аукциона',
                'auction_duration_hours.min' => 'Минимальная длительность аукциона - 1 час',
                'auction_duration_hours.max' => 'Максимальная длительность аукциона - 30 дней',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ], 422);
            }

            $validated = $validator->validated();
            $listingType = $validated['listing_type'] ?? 'gallery';

            if (
                $listingType === 'auction'
                && !empty($validated['auction_buyout_price'])
                && ((int) $validated['auction_start_price'] + (int) $validated['auction_min_step']) > (int) $validated['auction_buyout_price']
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Стартовая цена вместе с минимальным шагом не должна превышать блиц-цену',
                    'errors' => [
                        'auction_buyout_price' => ['Стартовая цена вместе с минимальным шагом не должна превышать блиц-цену'],
                    ],
                ], 422);
            }

            $displayPrice = $listingType === 'auction'
                ? $validated['auction_start_price']
                : $validated['price'];
            $file = $request->file('image');

            if (!$file || !$file->isValid()) {
                return response()->json(['success' => false, 'message' => 'Не удалось загрузить изображение'], 422);
            }

            File::ensureDirectoryExists(public_path('uploads/pictures'));

            $filename = 'picture_' . uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/pictures'), $filename);
            $filepath = 'uploads/pictures/' . $filename;

            $picture = Picture::create([
                'user_id' => $user_id,
                'img' => $filepath,
                'width' => $validated['width'],
                'height' => $validated['height'],
                'name' => trim($validated['name']),
                'technique' => trim($validated['technique']),
                'year' => $validated['year'],
                'description' => trim($validated['description']),
                'genre_id' => $validated['genre_id'],
                'style_id' => $validated['style_id'],
                'era_id' => $validated['era_id'],
                'price' => $displayPrice,
                'listing_type' => $listingType,
                'auction_start_price' => $listingType === 'auction' ? $validated['auction_start_price'] : null,
                'auction_current_price' => $listingType === 'auction' ? $validated['auction_start_price'] : null,
                'auction_min_step' => $listingType === 'auction' ? $validated['auction_min_step'] : null,
                'auction_buyout_price' => $listingType === 'auction' ? ($validated['auction_buyout_price'] ?? null) : null,
                'auction_starts_at' => $listingType === 'auction' ? now() : null,
                'auction_ends_at' => $listingType === 'auction' ? now()->addHours((int) $validated['auction_duration_hours']) : null,
                'status' => 'pending',
            ]);

            User::where('id', $user_id)->increment('pictures_count');

            return response()->json([
                'success' => true,
                'message' => $listingType === 'auction'
                    ? 'Картина для аукциона отправлена на модерацию'
                    : 'Картина отправлена на модерацию',
                'picture_id' => $picture->id,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        if (!session()->has('user_id')) {
            return response()->json(['success' => false, 'message' => 'Необходима авторизация'], 403);
        }

        $user_id = session('user_id');
        $picture_id = intval($request->picture_id ?? 0);

        $picture = Picture::where('id', $picture_id)->where('user_id', $user_id)->first();
        if (!$picture) {
            return response()->json(['success' => false, 'message' => 'Картина не найдена']);
        }

        $data = [];
        if ($request->name) {
            $data['name'] = trim($request->name);
        }
        if ($request->technique) {
            $data['technique'] = trim($request->technique);
        }
        if ($request->year) {
            $data['year'] = intval($request->year);
        }
        if ($request->description) {
            $data['description'] = trim($request->description);
        }
        if ($request->genre_id) {
            $data['genre_id'] = intval($request->genre_id);
        }
        if ($request->style_id) {
            $data['style_id'] = intval($request->style_id);
        }
        if ($request->era_id) {
            $data['era_id'] = intval($request->era_id);
        }
        if ($request->price) {
            $data['price'] = intval($request->price);
        }
        if ($request->width) {
            $data['width'] = intval($request->width);
        }
        if ($request->height) {
            $data['height'] = intval($request->height);
        }

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            File::ensureDirectoryExists(public_path('uploads/pictures'));
            $file = $request->file('image');
            $filename = 'picture_' . uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/pictures'), $filename);
            $data['img'] = 'uploads/pictures/' . $filename;
        }

        $data['status'] = 'pending';

        try {
            $picture->update($data);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Ошибка сохранения: ' . $e->getMessage()], 500);
        }

        return response()->json(['success' => true, 'message' => 'Изменения отправлены на модерацию']);
    }

    public function destroy(Request $request)
    {
        if (!session()->has('user_id')) {
            return response()->json(['success' => false, 'message' => 'Необходима авторизация'], 403);
        }

        $user_id = session('user_id');
        $picture_id = intval($request->picture_id ?? 0);

        $picture = Picture::where('id', $picture_id)->where('user_id', $user_id)->first();
        if (!$picture) {
            return response()->json(['success' => false, 'message' => 'Картина не найдена']);
        }

        $pictureName = $picture->name;
        $picture->delete();
        User::where('id', $user_id)->decrement('pictures_count');

        NotificationService::push(
            $user_id,
            'picture_deleted',
            'Картина удалена',
            'Картина "' . $pictureName . '" удалена из вашего профиля.',
            url('/account')
        );

        return response()->json(['success' => true, 'message' => 'Картина удалена']);
    }

    public function moderate(Request $request)
    {
        if (!session()->has('user_id') || session('user_role') != 2) {
            return response()->json(['success' => false, 'message' => 'Нет доступа'], 403);
        }

        $picture_id = intval($request->picture_id ?? 0);
        $action = $request->action;

        $picture = Picture::find($picture_id);
        if (!$picture) {
            return response()->json(['success' => false, 'message' => 'Картина не найдена']);
        }

        if ($action === 'approve') {
            $picture->update(['status' => 'approved']);

            NotificationService::push(
                $picture->user_id,
                'picture_approved',
                'Картина прошла модерацию',
                'Картина "' . $picture->name . '" опубликована и доступна пользователям.',
                $picture->listing_type === 'auction' ? url('/auction') : url('/picture/' . $picture->id),
                $picture->id
            );

            return response()->json(['success' => true, 'message' => 'Картина одобрена']);
        }

        if ($action === 'reject') {
            $reason = trim((string) $request->input('rejection_reason', ''));

            if ($reason === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'Укажите причину отклонения картины',
                ], 422);
            }
            $picture->update(['status' => 'rejected']);

            NotificationService::push(
                $picture->user_id,
                'picture_rejected',
                'Картина отклонена',
                'Картина "' . $picture->name . '" не прошла модерацию. Причина: ' . $reason,
                url('/account'),
                $picture->id
            );

            return response()->json(['success' => true, 'message' => 'Картина отклонена']);
        }

        return response()->json(['success' => false, 'message' => 'Некорректное действие']);
    }

    public function relistAuction(Request $request)
    {
        if (!session()->has('user_id')) {
            return response()->json(['success' => false, 'message' => 'Необходима авторизация'], 403);
        }

        $user_id = session('user_id');
        $picture_id = intval($request->picture_id ?? 0);

        $picture = Picture::where('id', $picture_id)
            ->where('user_id', $user_id)
            ->where('status', 'approved')
            ->where('listing_type', 'auction')
            ->first();

        if (!$picture) {
            return response()->json(['success' => false, 'message' => 'Картина не найдена'], 404);
        }

        $hasCanceledAuction = Order::where('picture_id', $picture->id)
            ->where('payment_status', 'canceled')
            ->exists();

        if (!$hasCanceledAuction) {
            return response()->json(['success' => false, 'message' => 'Эту картину нельзя повторно выставить'], 422);
        }

        $durationInHours = 24;
        if ($picture->auction_starts_at && $picture->auction_ends_at) {
            $durationInHours = max(1, $picture->auction_starts_at->diffInHours($picture->auction_ends_at));
        }

        AuctionBid::where('picture_id', $picture->id)->delete();

        $picture->update([
            'price' => $picture->auction_start_price,
            'auction_current_price' => $picture->auction_start_price,
            'auction_starts_at' => now(),
            'auction_ends_at' => now()->addHours($durationInHours),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Картина снова опубликована на аукционе',
            'redirect_url' => url('/auction'),
        ]);
    }
}
