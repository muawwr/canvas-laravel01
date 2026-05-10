<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderApiController extends Controller
{
    public function updateStatus(Request $request)
    {
        if (!session()->has('user_id') || session('user_role') != 2) {
            return response()->json(['success' => false, 'message' => 'Нет доступа'], 403);
        }

        $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'status' => ['required', 'in:waiting_shipment,in_transit,at_pickup_point,delivered'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $order = Order::with('picture')->find($request->order_id);
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Заказ не найден'], 404);
        }

        $order->update([
            'status' => $request->status,
        ]);

        $pictureName = $order->picture->name ?? 'картина';
        $statusText = match ($request->status) {
            'waiting_shipment' => 'ожидает отправки',
            'in_transit' => 'в пути',
            'at_pickup_point' => 'ожидает вас в пункте выдачи',
            'delivered' => 'доставлена',
            default => 'обновлен',
        };

        NotificationService::push(
            $order->buyer_id,
            'order_status_updated',
            'Статус заказа обновлен',
            'Картина "' . $pictureName . '" сейчас: ' . $statusText . '.',
            url('/orders'),
            $order->picture_id,
            $order->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Статус заказа обновлен',
        ]);
    }
}
