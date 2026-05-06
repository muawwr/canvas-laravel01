@extends('layouts.app')

@section('nav-auction-active', 'active')

@section('content')
<main class="auction_workspace container">
    <div class="auction_workspace_title">
        <h1>Аукцион:</h1>
        <span data-auction-count>{{ $auctions->count() }}</span>
    </div>

    @if($auctions->isNotEmpty())
        <div class="auction_workspace_layout">
            <section class="auction_lots_list" aria-label="Картины на аукционе">
                @foreach($auctions as $picture)
                    @php
                        $currentUserId = session('user_id');
                        $endsAt = $picture->auction_ends_at;
                        $isFinished = $endsAt && $endsAt->isPast();
                        $currentPrice = $picture->auction_current_price ?? $picture->auction_start_price ?? $picture->price;
                        $minStep = $picture->auction_min_step ?? 50;
                        $minNextBid = $currentPrice + $minStep;
                        $latestBidUserId = optional($picture->latestAuctionBid)->user_id;
                        $hasUserBid = session()->has('user_id') && $picture->relationLoaded('auctionBids') && $picture->auctionBids->isNotEmpty();
                        $leaderName = $picture->latestAuctionBid
                            ? ($latestBidUserId == $currentUserId ? 'вы' : ($picture->latestAuctionBid->user->name ?? 'Пользователь'))
                            : 'Ставок пока нет';
                        $userStatus = '';

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

                    <button
                        class="auction_lot_card {{ $loop->first ? 'active' : '' }} {{ $isFinished ? 'finished' : '' }}"
                        type="button"
                        data-auction-lot
                        data-picture-id="{{ $picture->id }}"
                        data-name="{{ $picture->name }}"
                        data-author="{{ $picture->user->name ?? 'Неизвестный автор' }}"
                        data-image="{{ asset($picture->img) }}"
                        data-current-price="{{ $currentPrice }}"
                        data-min-step="{{ $minStep }}"
                        data-next-bid="{{ $minNextBid }}"
                        data-bids-count="{{ $picture->auction_bids_count }}"
                        data-buyout-price="{{ $picture->auction_buyout_price ?? '' }}"
                        data-ends-at="{{ $endsAt ? $endsAt->toIso8601String() : '' }}"
                        data-is-finished="{{ $isFinished ? '1' : '0' }}"
                        data-is-auth="{{ session()->has('user_id') ? '1' : '0' }}"
                        data-is-owner="{{ session('user_id') == $picture->user_id ? '1' : '0' }}"
                        data-user-status="{{ $userStatus }}"
                        data-leader-name="{{ $leaderName }}"
                    >
                        <span class="auction_lot_check"></span>

                        <span class="auction_lot_image">
                            <img src="{{ asset($picture->img) }}" alt="{{ $picture->name }}">
                            <span data-lot-timer data-ends-at="{{ $endsAt ? $endsAt->toIso8601String() : '' }}">{{ $isFinished ? 'Завершен' : '...' }}</span>
                        </span>

                        <span class="auction_lot_content">
                            <span class="auction_lot_top">
                                <span>
                                    <strong>{{ $picture->name }}</strong>
                                    <small>{{ $picture->user->name ?? 'Неизвестный автор' }}</small>
                                </span>
                                <span class="auction_lot_tags">
                                    @if($picture->genre)
                                        <em>{{ $picture->genre->name }}</em>
                                    @endif
                                    @if($picture->style)
                                        <em>{{ $picture->style->name }}</em>
                                    @endif
                                    @if($picture->era)
                                        <em>{{ $picture->era->name }}</em>
                                    @endif
                                </span>
                            </span>

                            <span class="auction_lot_specs">
                                <span><b>Техника</b><i>{{ $picture->technique }}</i></span>
                                <span><b>Размеры</b><i>{{ $picture->width }} x {{ $picture->height }} см</i></span>
                                <span><b>Год</b><i>{{ $picture->year }}</i></span>
                            </span>
                        </span>
                    </button>
                @endforeach
            </section>

            <aside class="auction_side_panel" data-auction-side>
                <div class="auction_side_status" data-side-user-status style="display: none;"></div>

                <section class="auction_side_card auction_side_card_stats">
                    <div class="auction_side_head">
                        <h2>Ставки</h2>
                        <a data-side-picture-link href="#">Открыть картину</a>
                    </div>
                    <div class="auction_side_line"></div>
                    <div class="auction_side_stats">
                        <div>
                            <span>Текущая ставка</span>
                            <strong data-side-current-price></strong>
                        </div>
                        <div>
                            <span>Минимальный шаг</span>
                            <strong data-side-min-step></strong>
                        </div>
                        <div>
                            <span>Ставок</span>
                            <strong data-side-bids-count></strong>
                        </div>
                    </div>
                    <div class="auction_side_meta">
                        <span data-side-leader></span>
                    </div>
                </section>

                <section class="auction_side_card" data-side-bid-panel>
                    <div class="auction_side_head">
                        <h2>Введите свою сумму для ставки</h2>
                    </div>
                    <div class="auction_side_line"></div>
                    <form class="auction_side_bid_form" data-side-bid-form>
                        <input type="hidden" name="picture_id" data-side-bid-picture-id>
                        <input class="auction_side_input" type="number" name="amount" data-side-bid-input>
                        <button class="auction_side_btn auction_side_btn_dashed" type="submit" data-side-bid-button>Поставить</button>
                    </form>
                    <p class="auction_side_hint" data-side-bid-hint></p>
                </section>

                <section class="auction_side_card auction_side_buy" data-side-buyout-panel>
                    <div>
                        <h2>Купить сейчас</h2>
                        <strong data-side-buyout-price></strong>
                        <span>Аукцион завершится немедленно</span>
                    </div>
                    <form data-side-buyout-form>
                        <input type="hidden" name="picture_id" data-side-buyout-picture-id>
                        <button class="auction_side_btn auction_side_btn_buy" type="submit" data-side-buyout-button>Выкупить</button>
                    </form>
                </section>

                <div class="auction_side_message" data-side-message></div>
            </aside>
        </div>
    @else
        <div class="auction_empty">
            <h2>Активных аукционов пока нет</h2>
            <p>Когда продавцы выставят картины на торги и модератор их одобрит, они появятся здесь.</p>
        </div>
    @endif
</main>

<div class="auction_confirm_overlay" id="auctionConfirmModal" aria-hidden="true">
    <div class="auction_confirm_modal" role="dialog" aria-modal="true">
        <h2>Подтверждение</h2>
        <p id="auctionConfirmText"></p>
        <div class="auction_confirm_actions">
            <button class="auction_confirm_cancel" type="button" id="auctionConfirmCancel">Отмена</button>
            <button class="auction_confirm_submit" type="button" id="auctionConfirmSubmit">Подтвердить</button>
        </div>
    </div>
</div>

<script>
(() => {
    let lots = Array.from(document.querySelectorAll('[data-auction-lot]'));
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const confirmModal = document.getElementById('auctionConfirmModal');
    const confirmText = document.getElementById('auctionConfirmText');
    const confirmSubmit = document.getElementById('auctionConfirmSubmit');
    const confirmCancel = document.getElementById('auctionConfirmCancel');
    let activeLot = lots[0] || null;
    let pendingAction = null;

    if (!activeLot) {
        return;
    }

    function formatPrice(value) {
        return new Intl.NumberFormat('ru-RU').format(Number(value) || 0) + ' ₽';
    }

    function getTimeText(endsAtValue) {
        const endsAt = endsAtValue ? new Date(endsAtValue) : null;
        if (!endsAt) return 'Не указано';

        const diff = endsAt.getTime() - Date.now();
        if (diff <= 0) return 'Завершен';

        const totalSeconds = Math.floor(diff / 1000);
        const days = Math.floor(totalSeconds / 86400);
        const hours = Math.floor((totalSeconds % 86400) / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;

        return days > 0
            ? `${days} д ${hours} ч ${minutes} мин`
            : `${hours} ч ${minutes} мин ${seconds} сек`;
    }

    function isEnded(endsAtValue) {
        const endsAt = endsAtValue ? new Date(endsAtValue) : null;
        return Boolean(endsAt && endsAt.getTime() <= Date.now());
    }

    function openConfirm(message, onConfirm, submitText = 'Подтвердить') {
        pendingAction = onConfirm;
        confirmText.textContent = message;
        confirmSubmit.textContent = submitText;
        confirmSubmit.disabled = false;
        confirmModal.classList.add('show');
        confirmModal.setAttribute('aria-hidden', 'false');
    }

    function closeConfirm() {
        pendingAction = null;
        confirmModal.classList.remove('show');
        confirmModal.setAttribute('aria-hidden', 'true');
    }

    function showMessage(message, isSuccess) {
        const node = document.querySelector('[data-side-message]');
        node.textContent = message;
        node.classList.toggle('success', isSuccess);
        node.classList.toggle('error', !isSuccess);
    }

    function setSideDisabled(disabled, reason = '') {
        const bidButton = document.querySelector('[data-side-bid-button]');
        const bidInput = document.querySelector('[data-side-bid-input]');
        const buyoutButton = document.querySelector('[data-side-buyout-button]');

        bidButton.disabled = disabled;
        bidInput.disabled = disabled;
        buyoutButton.disabled = disabled;

        if (reason) {
            document.querySelector('[data-side-bid-hint]').textContent = reason;
        }
    }

    function renderSidePanel(lot) {
        const dataset = lot.dataset;
        const currentPrice = Number(dataset.currentPrice || 0);
        const minStep = Number(dataset.minStep || 0);
        const nextBid = Number(dataset.nextBid || currentPrice + minStep);
        const buyoutPrice = Number(dataset.buyoutPrice || 0);
        const isFinished = dataset.isFinished === '1' || isEnded(dataset.endsAt);
        const isAuth = dataset.isAuth === '1';
        const isOwner = dataset.isOwner === '1';
        const userStatus = dataset.userStatus || '';

        document.querySelector('[data-side-current-price]').textContent = formatPrice(currentPrice);
        document.querySelector('[data-side-min-step]').textContent = '+' + formatPrice(minStep);
        document.querySelector('[data-side-bids-count]').textContent = dataset.bidsCount || '0';
        document.querySelector('[data-side-leader]').textContent = 'Лидер: ' + (dataset.leaderName || 'Ставок пока нет');
        document.querySelector('[data-side-picture-link]').href = `/picture/${dataset.pictureId}`;

        const bidInput = document.querySelector('[data-side-bid-input]');
        const previousPictureId = document.querySelector('[data-side-bid-picture-id]').value;
        bidInput.min = nextBid;
        if (previousPictureId !== dataset.pictureId) {
            bidInput.value = '';
        }
        bidInput.placeholder = 'Минимум ' + formatPrice(nextBid);
        document.querySelector('[data-side-bid-picture-id]').value = dataset.pictureId;
        document.querySelector('[data-side-buyout-picture-id]').value = dataset.pictureId;
        document.querySelector('[data-side-bid-hint]').textContent = 'Минимум ' + formatPrice(nextBid);

        const statusNode = document.querySelector('[data-side-user-status]');
        if (userStatus) {
            statusNode.textContent = userStatus;
            statusNode.style.display = '';
            statusNode.classList.toggle('is-leading', userStatus === 'Вы лидируете' || userStatus === 'Вы выиграли');
            statusNode.classList.toggle('is-outbid', userStatus !== 'Вы лидируете' && userStatus !== 'Вы выиграли');
        } else {
            statusNode.style.display = 'none';
        }

        const bidPanel = document.querySelector('[data-side-bid-panel]');
        const buyoutPanel = document.querySelector('[data-side-buyout-panel]');
        const buyoutPriceNode = document.querySelector('[data-side-buyout-price]');
        bidPanel.style.display = isOwner ? 'none' : '';
        buyoutPanel.style.display = isOwner ? 'none' : '';

        if (isOwner) {
            setSideDisabled(true);
            return;
        }

        if (buyoutPrice && currentPrice <= buyoutPrice && !isFinished) {
            buyoutPanel.style.display = '';
            buyoutPriceNode.textContent = formatPrice(buyoutPrice);
        } else {
            buyoutPanel.style.display = 'none';
        }

        if (isFinished) {
            setSideDisabled(true, 'Аукцион завершен');
        } else if (!isAuth) {
            setSideDisabled(true, 'Войдите, чтобы сделать ставку');
        } else if (isOwner) {
            setSideDisabled(true, 'Это ваша картина. Покупатели могут делать ставки здесь.');
        } else {
            setSideDisabled(false);
            document.querySelector('[data-side-buyout-button]').disabled = false;
        }
    }

    function selectLot(lot) {
        lots.forEach((item) => item.classList.toggle('active', item === lot));
        activeLot = lot;
        renderSidePanel(lot);
    }

    function updateTimers() {
        lots.forEach((lot) => {
            const timer = lot.querySelector('[data-lot-timer]');
            const timeText = getTimeText(lot.dataset.endsAt);
            timer.textContent = timeText;

            if (isEnded(lot.dataset.endsAt)) {
                const wasActive = lot === activeLot;
                lot.remove();
                lots = lots.filter((item) => item !== lot);
                document.querySelector('[data-auction-count]').textContent = lots.length;

                if (wasActive) {
                    activeLot = lots[0] || null;
                    if (activeLot) {
                        selectLot(activeLot);
                    } else {
                        window.location.reload();
                    }
                }
            }
        });
    }

    async function sendAuctionRequest(url, form) {
        const response = await fetch(url, {
            method: 'POST',
            body: new FormData(form),
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (response.redirected) {
            window.location.href = response.url;
            return {
                response,
                result: {
                    success: false,
                    message: 'Нужно войти в аккаунт',
                },
            };
        }

        const text = await response.text();
        let result = null;

        try {
            result = text ? JSON.parse(text) : {};
        } catch (error) {
            result = {
                success: false,
                message: response.status === 419
                    ? 'Сессия истекла. Обновите страницу и попробуйте еще раз.'
                    : 'Сервер вернул некорректный ответ. Код: ' + response.status,
            };
        }

        return { response, result };
    }

    async function submitBid(form) {
        const button = document.querySelector('[data-side-bid-button]');
        button.disabled = true;

        try {
            const { response, result } = await sendAuctionRequest('{{ url('/api/auction/bid') }}', form);
            showMessage(result.message || 'Готово', response.ok && result.success);

            if (response.ok && result.success && activeLot) {
                activeLot.dataset.currentPrice = result.current_price;
                activeLot.dataset.nextBid = result.min_next_bid;
                activeLot.dataset.bidsCount = result.bids_count;
                activeLot.dataset.userStatus = result.user_status || 'Вы лидируете';
                activeLot.dataset.leaderName = 'вы';
                renderSidePanel(activeLot);
            }
        } catch (error) {
            showMessage('Не удалось отправить ставку. Попробуйте еще раз.', false);
        } finally {
            button.disabled = false;
        }
    }

    async function submitBuyout(form) {
        const button = document.querySelector('[data-side-buyout-button]');
        button.disabled = true;

        try {
            const { response, result } = await sendAuctionRequest('{{ url('/api/auction/buyout') }}', form);
            showMessage(result.message || 'Готово', response.ok && result.success);

            if (response.ok && result.success) {
                window.setTimeout(() => {
                    window.location.href = result.redirect_url || '{{ url('/cart') }}';
                }, 700);
            }
        } catch (error) {
            showMessage(error.message || 'Не удалось оформить блиц-покупку. Попробуйте еще раз.', false);
        } finally {
            button.disabled = false;
        }
    }

    lots.forEach((lot) => {
        lot.addEventListener('click', () => selectLot(lot));
    });

    document.querySelector('[data-side-bid-form]').addEventListener('submit', (event) => {
        event.preventDefault();
        const form = event.currentTarget;
        const amount = Number(new FormData(form).get('amount'));
        const min = Number(document.querySelector('[data-side-bid-input]').min || 0);

        if (amount < min) {
            showMessage('Минимальная ставка: ' + formatPrice(min), false);
            return;
        }

        openConfirm('Вы собираетесь поставить ' + formatPrice(amount) + '. Отменить ставку будет нельзя.', () => submitBid(form));
    });

    document.querySelector('[data-side-buyout-form]').addEventListener('submit', (event) => {
        event.preventDefault();
        const form = event.currentTarget;
        const buyoutPrice = Number(activeLot?.dataset.buyoutPrice || 0);
        openConfirm('Вы покупаете картину за ' + formatPrice(buyoutPrice) + '. Аукцион завершится немедленно.', () => submitBuyout(form), 'Купить');
    });

    confirmSubmit.addEventListener('click', async () => {
        if (!pendingAction) return;
        confirmSubmit.disabled = true;
        const action = pendingAction;
        closeConfirm();
        await action();
    });

    confirmCancel.addEventListener('click', closeConfirm);
    confirmModal.addEventListener('click', (event) => {
        if (event.target === confirmModal) {
            closeConfirm();
        }
    });

    selectLot(activeLot);
    updateTimers();
    window.setInterval(updateTimers, 1000);
})();
</script>
@endsection
