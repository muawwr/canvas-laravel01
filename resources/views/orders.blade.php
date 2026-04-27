@extends('layouts.app')

@section('content')
<main class="orders_main container">
    <div class="orders_header">
        <h1 class="orders_title">Заказы: <span class="orders_badge">{{ $total_orders }}</span></h1>
    </div>
    <div class="orders_tabs">
        <div class="o_tab active" data-tab="purchased">
            <p>КУПЛЕННЫЕ</p>
            <div class="o_line"></div>
        </div>
        <div class="o_tab" data-tab="sold">
            <p>ПРОДАННЫЕ</p>
            <div class="o_line"></div>
        </div>
    </div>
    
    <!-- Купленные заказы -->
    <div class="orders_list orders_content" data-content="purchased">
        @if($purchased_orders->isEmpty())
            <div class="empty-orders" style="text-align: center; padding: 60px 20px; color: #939393;">
                <img src="{{ asset('assets/images/header/orders.svg') }}" alt="Заказы" style="width: 80px; height: 80px; opacity: 0.3; margin-bottom: 20px;">
                <h3 style="font-size: 24px; margin-bottom: 10px;">Купленных заказов пока нет</h3>
                <p style="font-size: 16px; margin-bottom: 30px;">Ваши покупки будут отображаться здесь</p>
            </div>
        @else
            @foreach($purchased_orders as $index => $order)
            <div class="order_card">
                <div class="order_section_main">
                    @if($index === 0)<h3>Информация о картине</h3>@endif
                    <div class="order_section">
                        <div class="order_painting_info">
                            <div class="order_painting_image">
                                <img src="{{ asset($order->picture->img) }}" alt="{{ $order->picture->name }}">
                            </div>
                            <div class="order_painting_details">
                                <div class="order_detail">
                                    <p class="order_painting_name">Картина:</p>
                                    <p class="order_painting_title">{{ $order->picture->name }}</p>
                                </div>
                                <div class="order_detail">
                                    <p class="order_painting_price">Цена:</p>
                                    <p class="order_painting_price_value">{{ number_format($order->price, 0, '.', ' ') }} <span>₽</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="order_section_main">
                    @if($index === 0)<h3>Информация о доставке</h3>@endif
                    <div class="order_section">
                        <div class="order_buyer_info">
                            <div class="order_buyer_row">
                                <p class="order_buyer_label">Продавец:</p>
                                <div class="order_buyer_name">
                                    <a href="{{ url('/account?user_id=' . $order->seller_id) }}" style="color: inherit; text-decoration: none;">
                                        <span style="cursor: pointer;">{{ $order->seller->name }}</span>
                                    </a>
                                    @php $seller_rank = \App\Http\Controllers\OrderController::getUserRank($order->seller_sold_count); @endphp
                                    <div class="order_buyer_badge {{ $seller_rank['class'] }}">
                                        <img src="{{ asset('assets/images/account/' . $seller_rank['icon']) }}" alt="{{ $seller_rank['label'] }}">
                                    </div>
                                </div>
                            </div>
                            <div class="order_buyer_row">
                                <p class="order_buyer_label">Пункт выдачи:</p>
                                <p class="order_buyer_value">{{ $order->pickup_point }}</p>
                            </div>
                            <div class="order_buyer_row">
                                <p class="order_buyer_label">Получатель:</p>
                                <p class="order_buyer_value">{{ $order->recipient_name }}</p>
                            </div>
                            <div class="order_buyer_row">
                                <p class="order_buyer_label">Уникальный код:</p>
                                <div class="order_code">
                                    @php $code = str_pad($order->unique_code, 4, '0', STR_PAD_LEFT); @endphp
                                    @for($i = 0; $i < 4; $i++)
                                    <span class="code_digit">{{ $code[$i] }}</span>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="order_section_main">
                    @if($index === 0)<h3>Статус</h3>@endif
                    <div class="order_status_section">
                        <div class="order_status tooltip-container">
                            <img src="{{ asset(\App\Http\Controllers\OrderController::getStatusIcon($order->status)) }}" alt="{{ \App\Http\Controllers\OrderController::getStatusText($order->status) }}">
                            <span class="tooltip">{{ \App\Http\Controllers\OrderController::getStatusText($order->status) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @endif
    </div>
    
    <!-- Проданные заказы -->
    <div class="orders_list orders_content" data-content="sold" style="display: none;">
        @if($sold_orders->isEmpty())
            <div class="empty-orders" style="text-align: center; padding: 60px 20px; color: #939393;">
                <img src="{{ asset('assets/images/header/orders.svg') }}" alt="Заказы" style="width: 80px; height: 80px; opacity: 0.3; margin-bottom: 20px;">
                <h3 style="font-size: 24px; margin-bottom: 10px;">Проданных заказов пока нет</h3>
                <p style="font-size: 16px; margin-bottom: 30px;">Ваши продажи будут отображаться здесь</p>
            </div>
        @else
            @foreach($sold_orders as $index => $order)
            <div class="order_card">
                <div class="order_section_main">
                    @if($index === 0)<h3>Информация о картине</h3>@endif
                    <div class="order_section">
                        <div class="order_painting_info">
                            <div class="order_painting_image">
                                <img src="{{ asset($order->picture->img) }}" alt="{{ $order->picture->name }}">
                            </div>
                            <div class="order_painting_details">
                                <div class="order_detail">
                                    <p class="order_painting_name">Картина:</p>
                                    <p class="order_painting_title">{{ $order->picture->name }}</p>
                                </div>
                                <div class="order_detail">
                                    <p class="order_painting_price">Цена:</p>
                                    <p class="order_painting_price_value">{{ number_format($order->price, 0, '.', ' ') }} <span>₽</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="order_section_main">
                    @if($index === 0)<h3>Информация о покупателе</h3>@endif
                    <div class="order_section">
                        <div class="order_buyer_info">
                            <div class="order_buyer_row">
                                <p class="order_buyer_label">Покупатель:</p>
                                <div class="order_buyer_name">
                                    <a href="{{ url('/account?user_id=' . $order->buyer_id) }}" style="color: inherit; text-decoration: none;">
                                        <span style="cursor: pointer;">{{ $order->buyer->name }}</span>
                                    </a>
                                    @php $buyer_rank = \App\Http\Controllers\OrderController::getUserRank($order->buyer_sold_count); @endphp
                                    <div class="order_buyer_badge {{ $buyer_rank['class'] }}">
                                        <img src="{{ asset('assets/images/account/' . $buyer_rank['icon']) }}" alt="{{ $buyer_rank['label'] }}">
                                    </div>
                                </div>
                            </div>
                            <div class="order_buyer_row">
                                <p class="order_buyer_label">Пункт выдачи:</p>
                                <p class="order_buyer_value">{{ $order->pickup_point }}</p>
                            </div>
                            <div class="order_buyer_row">
                                <p class="order_buyer_label">Получатель:</p>
                                <p class="order_buyer_value">{{ $order->recipient_name }}</p>
                            </div>
                            <div class="order_buyer_row">
                                <p class="order_buyer_label">Уникальный код:</p>
                                <div class="order_code">
                                    @php $code = str_pad($order->unique_code, 4, '0', STR_PAD_LEFT); @endphp
                                    @for($i = 0; $i < 4; $i++)
                                    <span class="code_digit">{{ $code[$i] }}</span>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="order_section_main">
                    @if($index === 0)<h3>Статус</h3>@endif
                    <div class="order_status_section">
                        <div class="order_status tooltip-container">
                            <img src="{{ asset(\App\Http\Controllers\OrderController::getStatusIcon($order->status)) }}" alt="{{ \App\Http\Controllers\OrderController::getStatusText($order->status) }}">
                            <span class="tooltip">{{ \App\Http\Controllers\OrderController::getStatusText($order->status) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @endif
    </div>
</main>
@endsection

@section('scripts')
<style>
.tooltip-container { position: relative; display: flex; }
.tooltip { visibility: hidden; opacity: 0; background-color: #2D2D2D; color: #E0E0E0; text-align: center; padding: 8px 16px; border-radius: 8px; position: absolute; z-index: 1000; bottom: 100%; left: 50%; transform: translateX(-50%); white-space: nowrap; font-size: 14px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); transition: opacity 0.3s, visibility 0.3s; }
.tooltip::after { content: ""; position: absolute; top: 100%; left: 50%; margin-left: -5px; border-width: 5px; border-style: solid; border-color: #2D2D2D transparent transparent transparent; }
.tooltip-container:hover .tooltip { visibility: visible; opacity: 1; }
.o_tab { cursor: pointer; transition: opacity 0.3s ease; }
.o_tab:not(.active) p { color: #939393; }
.o_tab:not(.active) .o_line { opacity: 0; }
.o_tab.active p { color: #E0E0E0; }
.o_tab.active .o_line { opacity: 1; }
.o_line { transition: opacity 0.3s ease; }
.orders_content { animation: fadeIn 0.4s ease-in-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.o_tab');
    const contents = document.querySelectorAll('.orders_content');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            contents.forEach(content => { content.style.display = 'none'; });
            const targetContent = document.querySelector(`[data-content="${targetTab}"]`);
            if (targetContent) { targetContent.style.display = 'flex'; }
        });
    });
});
</script>
@endsection
