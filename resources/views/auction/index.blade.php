@extends('layouts.app')

@section('nav-auction-active', 'active')

@section('content')
<main class="auction_main container">
    <div class="auction_header">
        <div>
            <h1 class="auction_title">Аукцион</h1>
            <p class="auction_subtitle">Картины, которые можно приобрести по ставке или сразу по блиц-цене.</p>
        </div>
        <a href="{{ session()->has('user_id') ? url('/add') : url('/auth') }}" class="auction_add_link">Выставить работу</a>
    </div>

    <div class="auction_grid">
        @forelse($auctions as $picture)
            @php
                $currentUserId = session('user_id');
                $endsAt = $picture->auction_ends_at;
                $isFinished = $endsAt && $endsAt->isPast();
                $currentPrice = $picture->auction_current_price ?? $picture->auction_start_price ?? $picture->price;
                $minStep = $picture->auction_min_step ?? 50;
                $minNextBid = $currentPrice + $minStep;
                $latestBidUserId = optional($picture->latestAuctionBid)->user_id;
                $hasUserBid = session()->has('user_id') && $picture->relationLoaded('auctionBids') && $picture->auctionBids->isNotEmpty();
                $canAct = session()->has('user_id') && $currentUserId != $picture->user_id && !$isFinished;
                $userStatus = null;

                if ($isFinished && $latestBidUserId && $latestBidUserId == $currentUserId) {
                    $userStatus = 'Вы выиграли';
                } elseif ($isFinished) {
                    $userStatus = 'Аукцион завершен';
                } elseif ($latestBidUserId && $latestBidUserId == $currentUserId) {
                    $userStatus = 'Вы лидируете';
                } elseif ($hasUserBid) {
                    $userStatus = 'Ваша ставка перебита';
                }
            @endphp

            <article class="auction_card {{ $isFinished ? 'auction_card_finished' : '' }}" data-auction-card data-picture-id="{{ $picture->id }}">
                <a href="{{ url('/picture/' . $picture->id) }}" class="auction_card_image">
                    <img src="{{ asset($picture->img) }}" alt="{{ $picture->name }}">
                    <span class="auction_status">{{ $isFinished ? 'Завершен' : 'Идет торг' }}</span>
                </a>

                <div class="auction_card_body">
                    <div class="auction_author">
                        <img src="{{ asset($picture->user->img ?? 'assets/images/account/mainUser.png') }}" alt="{{ $picture->user->name ?? 'Автор' }}">
                        <a href="{{ url('/account?user_id=' . $picture->user_id) }}">{{ $picture->user->name ?? 'Неизвестный автор' }}</a>
                    </div>

                    <h2 class="auction_card_title">{{ $picture->name }}</h2>

                    <div class="auction_meta">
                        <span>{{ $picture->technique }}</span>
                        <span>{{ $picture->width }} x {{ $picture->height }} см</span>
                        @if($picture->genre)
                            <span>{{ $picture->genre->name }}</span>
                        @endif
                    </div>

                    <div class="auction_prices">
                        <div>
                            <span class="auction_price_label">Текущая ставка</span>
                            <strong>{{ number_format($currentPrice, 0, '.', ' ') }} ₽</strong>
                        </div>
                        <div>
                            <span class="auction_price_label">До завершения</span>
                            <strong data-auction-timer data-ends-at="{{ $endsAt ? $endsAt->toIso8601String() : '' }}">{{ $isFinished ? 'Завершен' : '...' }}</strong>
                        </div>
                        <div>
                            <span class="auction_price_label">Ставок</span>
                            <strong>{{ $picture->auction_bids_count }}</strong>
                        </div>
                        @if($picture->auction_buyout_price)
                            <div>
                                <span class="auction_price_label">Блиц-цена</span>
                                <strong>{{ number_format($picture->auction_buyout_price, 0, '.', ' ') }} ₽</strong>
                            </div>
                        @endif
                    </div>

                    <a class="auction_login_link" href="{{ url('/picture/' . $picture->id) }}">Открыть картину</a>
                </div>
            </article>
        @empty
            <div class="auction_empty">
                <h2>Активных аукционов пока нет</h2>
                <p>Когда продавцы выставят картины на торги и модератор их одобрит, они появятся здесь.</p>
            </div>
        @endforelse
    </div>
</main>

<script>
(() => {
    function updateTimers() {
        document.querySelectorAll('[data-auction-timer]').forEach((timer) => {
            const endsAt = timer.dataset.endsAt ? new Date(timer.dataset.endsAt) : null;
            if (!endsAt) {
                timer.textContent = 'Не указано';
                return;
            }

            const diff = endsAt.getTime() - Date.now();
            if (diff <= 0) {
                timer.textContent = 'Аукцион завершен';
                const card = timer.closest('[data-auction-card]');
                if (card) {
                    card.classList.add('auction_card_finished');
                    const status = card.querySelector('.auction_status');
                    if (status) status.textContent = 'Завершен';
                }
                return;
            }

            const totalSeconds = Math.floor(diff / 1000);
            const days = Math.floor(totalSeconds / 86400);
            const hours = Math.floor((totalSeconds % 86400) / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;

            timer.textContent = days > 0
                ? `Осталось: ${days} д ${hours} ч ${minutes} мин`
                : `Осталось: ${hours} ч ${minutes} мин ${seconds} сек`;
        });
    }

    updateTimers();
    window.setInterval(updateTimers, 1000);
})();
</script>
@endsection
