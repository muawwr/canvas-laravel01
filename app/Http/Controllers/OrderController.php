<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $is_logged_in = session()->has('user_id');
        $user_avatar = session('user_img', 'assets/images/account/mainUser.png');
        $user_name = session('user_name', 'Гость');
        $user_id = session('user_id');

        // Купленные заказы
        $purchased_orders = Order::where('buyer_id', $user_id)
            ->with(['picture', 'seller'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($order) {
            $order->seller_sold_count = DB::table('orders')
                ->join('pictures', 'orders.picture_id', '=', 'pictures.id')
                ->where('pictures.user_id', $order->seller_id)
                ->where('orders.payment_status', 'succeeded')
                ->distinct('orders.picture_id')
                ->count('orders.picture_id');
            return $order;
        });

        // Проданные заказы
        $sold_orders = Order::where('seller_id', $user_id)
            ->with(['picture', 'buyer'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($order) {
            $order->buyer_sold_count = DB::table('orders')
                ->join('pictures', 'orders.picture_id', '=', 'pictures.id')
                ->where('pictures.user_id', $order->buyer_id)
                ->where('orders.payment_status', 'succeeded')
                ->distinct('orders.picture_id')
                ->count('orders.picture_id');
            return $order;
        });

        $total_orders = $purchased_orders->count() + $sold_orders->count();

        return view('orders', compact(
            'is_logged_in', 'user_avatar', 'user_name',
            'purchased_orders', 'sold_orders', 'total_orders'
        ));
    }

    public static function getStatusIcon($status)
    {
        return match ($status) {
                'delivered' => 'assets/images/orders/delivered.svg',
                'waiting_shipment' => 'assets/images/orders/waiting.svg',
                'at_pickup_point' => 'assets/images/orders/pick-upPoint.svg',
                'in_transit' => 'assets/images/orders/road.svg',
                default => 'assets/images/orders/waiting.svg',
            };
    }

    public static function getStatusText($status)
    {
        return match ($status) {
                'delivered' => 'Доставлен',
                'waiting_shipment' => 'Ожидает отправки',
                'at_pickup_point' => 'Ожидает на пункте выдачи',
                'in_transit' => 'В пути',
                default => 'Неизвестный статус',
            };
    }

    public static function getUserRank($sold_count)
    {
        if ($sold_count < 5) {
            return ['label' => 'НОВИЧОК', 'icon' => 'newbie.svg', 'class' => 'order_buyer_badge_newbie'];
        }
        elseif ($sold_count < 10) {
            return ['label' => 'ОПЫТНЫЙ', 'icon' => 'experienced.svg', 'class' => 'order_buyer_badge_experienced'];
        }
        else {
            return ['label' => 'ЭКСПЕРТ', 'icon' => 'expert.svg', 'class' => 'order_buyer_badge_expert'];
        }
    }
}
