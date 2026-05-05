@extends('layouts.app')

@section('content')
<main>
    <div class="one_product container {{ $picture->listing_type === 'auction' ? 'one_product_auction' : '' }}">
        @if($picture->listing_type !== 'auction')
        <div>
            <a class="btn_back" href="{{ url('/gallery') }}"><img src="{{ asset('assets/images/oneProduct/back.svg') }}" alt=""></a>
        </div>
        @endif
        <div class="product_image">
            <img src="{{ asset($picture->img) }}" alt="{{ $picture->name }}">
        </div>
        <div class="product_info">
            <div class="product_card_author">
                <img src="{{ asset($picture->user->img ?? 'assets/images/account/mainUser.png') }}" alt="{{ $picture->user->name }}" class="product_author_avatar">
                <a href="{{ url('/account?user_id=' . $picture->user_id) }}" style="color: inherit; text-decoration: none;">
                    <span>{{ $picture->user->name }}</span>
                </a>
            </div>
            <h3 class="product_name">{{ $picture->name }}</h3>
            <div class="description_accordion">
                @php
                    $description = $picture->description;
                    $description_length = mb_strlen($description);
                @endphp
                @if($description_length > 70)
                    @php
                        $temp = mb_substr($description, 0, 70);
                        $last_space = mb_strrpos($temp, ' ');
                        $short = $last_space !== false ? mb_substr($description, 0, $last_space) : $temp;
                        $remaining = $last_space !== false ? mb_substr($description, $last_space + 1) : mb_substr($description, 70);
                    @endphp
                    <p class="description">{!! nl2br(e($short)) !!}</p>
                    <div class="description_hidden">
                        <p>{!! nl2br(e($remaining)) !!}</p>
                    </div>
                    <button class="description_toggle">Читать далее</button>
                @else
                    <p class="description">{!! nl2br(e($description)) !!}</p>
                @endif
            </div>
            <div class="filters">
                @if($picture->genre)
                <div class="filter_card">
                    <div class="dote"></div>
                    <p class="filter">{{ $picture->genre->name }}</p>
                </div>
                @endif
                @if($picture->style)
                <div class="filter_card">
                    <div class="dote"></div>
                    <p class="filter">{{ $picture->style->name }}</p>
                </div>
                @endif
                @if($picture->era)
                <div class="filter_card">
                    <div class="dote"></div>
                    <p class="filter">{{ $picture->era->name }}</p>
                </div>
                @endif
            </div>
            <div class="product_about">
                <div class="product_about_card">
                    <div class="info_card">
                        <p class="info_title">Техника</p>
                        <p class="info_text">{{ $picture->technique }}</p>
                    </div>
                    <div class="product_line"></div>
                </div>
                <div class="product_about_card">
                    <div class="info_card">
                        <p class="info_title">Размеры</p>
                        <p class="info_text">{{ $picture->width }} ✕ {{ $picture->height }} см</p>
                    </div>
                    <div class="product_line"></div>
                </div>
                <div class="product_about_card">
                    <div class="info_card">
                        <p class="info_title">Год</p>
                        <p class="info_text">{{ $picture->year }}</p>
                    </div>
                    <div class="product_line"></div>
                </div>
                @if($picture->listing_type === 'auction')
                <div class="product_about_card">
                    <div class="info_card">
                        <p class="info_title">Шаг ставки</p>
                        <p class="info_text">{{ number_format($picture->auction_min_step ?? 0, 0, '.', ' ') }} ₽</p>
                    </div>
                    <div class="product_line"></div>
                </div>
                <div class="product_about_card">
                    <div class="info_card">
                        <p class="info_title">До завершения</p>
                        <p class="info_text" data-auction-timer data-ends-at="{{ $picture->auction_ends_at ? $picture->auction_ends_at->toIso8601String() : '' }}">...</p>
                    </div>
                    <div class="product_line"></div>
                </div>
                @else
                <div class="product_about_card">
                    <div class="info_card">
                        <p class="info_title">Цена</p>
                        <p class="info_text price">{{ number_format($picture->price, 0, '.', ' ') }} ₽</p>
                    </div>
                    <div class="product_line"></div>
                </div>
                @endif
            </div>
            @if($picture->listing_type === 'auction')
                @php
                    $currentUserId = session('user_id');
                    $endsAt = $picture->auction_ends_at;
                    $isFinishedAuction = $endsAt && $endsAt->isPast();
                    $currentPrice = $picture->auction_current_price ?? $picture->auction_start_price ?? $picture->price;
                    $minStep = $picture->auction_min_step ?? 50;
                    $minNextBid = $currentPrice + $minStep;
                    $latestBidUserId = optional($picture->latestAuctionBid)->user_id;
                    $hasUserBid = session()->has('user_id') && $picture->relationLoaded('auctionBids') && $picture->auctionBids->isNotEmpty();
                    $userStatus = null;

                    if ($isFinishedAuction && $latestBidUserId && $latestBidUserId == $currentUserId) {
                        $userStatus = 'Вы выиграли';
                    } elseif ($isFinishedAuction) {
                        $userStatus = 'Аукцион завершен';
                    } elseif ($latestBidUserId && $latestBidUserId == $currentUserId) {
                        $userStatus = 'Вы лидируете';
                    } elseif ($hasUserBid) {
                        $userStatus = 'Ваша ставка перебита';
                    }
                @endphp
            @endif
            @if($picture->listing_type !== 'auction')
            <div class="product_buttons">
                @if(session()->has('user_id') && session('user_id') == $picture->user_id)
                    <a href="{{ url('/edit/' . $picture->id) }}" class="edit_product_btn">
                        <img src="{{ asset('assets/images/oneProduct/edit.svg') }}" alt="Редактировать">
                        Редактировать
                    </a>
                    <button class="delete_product_btn" onclick="showDeleteModal({{ $picture->id }}, '{{ e($picture->name) }}')">
                        <img src="{{ asset('assets/images/oneProduct/Trash.svg') }}" alt="Удалить">
                        Удалить
                    </button>
                @else
                    @if(session()->has('user_id'))
                    <div class="fav {{ $is_in_favorites ? 'active' : '' }}" data-picture-id="{{ $picture->id }}" data-is-favorite="{{ $is_in_favorites ? 'true' : 'false' }}" style="cursor: pointer;">
                        <img src="{{ asset('assets/images/oneProduct/Fav.svg') }}" alt="">
                        <div class="fav_count">
                            <span class="likes-number">{{ $likes_count }}</span>
                            <span class="likes-text">@php
                                $last_digit = $likes_count % 10;
                                $last_two_digits = $likes_count % 100;
                                if ($last_two_digits >= 11 && $last_two_digits <= 19) echo 'лайков';
                                elseif ($last_digit == 1) echo 'лайк';
                                elseif ($last_digit >= 2 && $last_digit <= 4) echo 'лайка';
                                else echo 'лайков';
                            @endphp</span>
                        </div>
                    </div>
                    @else
                    <div class="fav" style="cursor: not-allowed; opacity: 0.6;">
                        <img src="{{ asset('assets/images/oneProduct/Fav.svg') }}" alt="">
                        <div class="fav_count">
                            <span class="likes-number">{{ $likes_count }}</span>
                            <span class="likes-text">лайков</span>
                        </div>
                    </div>
                    @endif
                    @if($is_sold)
                    <div class="sold-status-btn">
                        <img src="{{ asset('assets/images/gallery/sold.svg') }}" alt="Продано">
                        ПРОДАНО
                    </div>
                    @else
                        @if(session()->has('user_id'))
                        <div class="cart" id="addToCartBtn" data-picture-id="{{ $picture->id }}" style="cursor: pointer;">
                            В корзину<img src="{{ asset('assets/images/oneProduct/Right.svg') }}" alt="">
                        </div>
                        @else
                        <a href="{{ url('/auth') }}">
                        <div class="cart">
                            В корзину<img src="{{ asset('assets/images/oneProduct/Right.svg') }}" alt="">
                        </div>
                        </a>
                        @endif
                    @endif
                @endif
            </div>
            @endif
        </div>
        @if($picture->listing_type === 'auction')
        <div class="auction_product_actions" data-auction-card data-picture-id="{{ $picture->id }}">
            @if($userStatus)
                <div class="auction_user_status {{ $userStatus === 'Вы лидируете' || $userStatus === 'Вы выиграли' ? 'is-leading' : 'is-outbid' }}" data-user-status>
                    {{ $userStatus }}
                </div>
            @else
                <div class="auction_user_status" data-user-status style="display: none;"></div>
            @endif

            <div class="auction_product_panels">
                <section class="auction_bid_panel">
                    <div class="auction_panel_heading">
                        <h3>Ставки</h3>
                        <span data-auction-timer data-ends-at="{{ $endsAt ? $endsAt->toIso8601String() : '' }}">{{ $isFinishedAuction ? 'Завершен' : '...' }}</span>
                    </div>

                    <div class="auction_prices">
                        <div>
                            <span class="auction_price_label">Текущая ставка</span>
                            <strong data-current-price>{{ number_format($currentPrice, 0, '.', ' ') }} ₽</strong>
                        </div>
                        <div>
                            <span class="auction_price_label">Минимальный шаг</span>
                            <strong>+{{ number_format($minStep, 0, '.', ' ') }} ₽</strong>
                        </div>
                        <div>
                            <span class="auction_price_label">Следующая ставка</span>
                            <strong data-next-bid-label>{{ number_format($minNextBid, 0, '.', ' ') }} ₽</strong>
                        </div>
                        <div>
                            <span class="auction_price_label">Ставок</span>
                            <strong data-bids-count>{{ $picture->auction_bids_count }}</strong>
                        </div>
                    </div>
                </section>
                <section class="auction_bid_panel">
                    <div class="auction_bid_info">
                        @if($picture->latestAuctionBid)
                            <span>Лидер: <strong data-leader-name>{{ $latestBidUserId == $currentUserId ? 'вы' : ($picture->latestAuctionBid->user->name ?? 'Пользователь') }}</strong></span>
                        @else
                            <span>Станьте первым участником торгов</span>
                        @endif
                    </div>

                    @if($isFinishedAuction)
                        <div class="auction_notice">Торги по этой картине завершены.</div>
                    @elseif(!session()->has('user_id'))
                        <a class="auction_login_link" href="{{ url('/auth') }}">Войдите, чтобы сделать ставку</a>
                    @elseif(session('user_id') == $picture->user_id)
                        <div class="auction_notice">Это ваша картина. Покупатели могут делать ставки здесь.</div>
                    @else
                        <form class="auction_bid_form" data-bid-form data-confirm-message="Вы собираетесь поставить {{ number_format($minNextBid, 0, '.', ' ') }} ₽. Отменить ставку будет нельзя.">
                            <input type="hidden" name="picture_id" value="{{ $picture->id }}">
                            <input type="hidden" name="amount" value="{{ $minNextBid }}" data-quick-bid-amount>
                            <button class="auction_action_btn" type="submit" data-quick-bid-button>
                                Поставить {{ number_format($minNextBid, 0, '.', ' ') }} ₽
                            </button>
                        </form>

                        <form class="auction_custom_bid_form" data-bid-form data-custom-bid-form>
                            <input type="hidden" name="picture_id" value="{{ $picture->id }}">
                            <label class="auction_price_label" for="customBid{{ $picture->id }}">Ввести свою сумму</label>
                            <div class="auction_custom_bid_row">
                                <input id="customBid{{ $picture->id }}" type="number" name="amount" min="{{ $minNextBid }}" placeholder="Минимум {{ number_format($minNextBid, 0, '.', ' ') }} ₽" class="auction_input" data-bid-input>
                                <button class="auction_action_btn auction_action_btn_secondary" type="submit">Поставить</button>
                            </div>
                        </form>
                    @endif
                </section>

                @if(!$isFinishedAuction && $picture->auction_buyout_price && $currentPrice <= $picture->auction_buyout_price)
                    <section class="auction_buyout_panel">
                        <div class="auction_panel_heading">
                            <h3>Купить сейчас</h3>
                            <span>Аукцион завершится немедленно</span>
                        </div>
                        <strong class="auction_buyout_price">{{ number_format($picture->auction_buyout_price, 0, '.', ' ') }} ₽</strong>

                        @if(!session()->has('user_id'))
                            <a class="auction_buyout_btn" href="{{ url('/auth') }}">Войти для покупки</a>
                        @elseif(session('user_id') == $picture->user_id)
                            <div class="auction_notice">Блиц-покупка доступна только покупателям.</div>
                        @else
                            <form class="auction_buyout_form" data-buyout-form data-confirm-message="Вы покупаете картину за {{ number_format($picture->auction_buyout_price, 0, '.', ' ') }} ₽. Аукцион завершится немедленно.">
                                <input type="hidden" name="picture_id" value="{{ $picture->id }}">
                                <button class="auction_buyout_btn" type="submit">Купить сейчас</button>
                            </form>
                        @endif
                    </section>
                @endif
            </div>

            <div class="auction_message" data-auction-message></div>
        </div>
        @endif
    </div>
</main>

<!-- Delete Modal -->
<div class="delete_modal_overlay" id="deleteModal" style="display: none;">
    <div class="delete_modal">
        <div class="delete_icon"><span>?</span></div>
        <h2 class="delete_title" id="deleteTitle">Удалить картину?</h2>
        <div class="delete_buttons">
            <button class="delete_btn delete_btn_back" onclick="closeDeleteModal()">
                <img src="{{ asset('assets/images/add/Left.svg') }}" alt="Back"> Назад
            </button>
            <button class="delete_btn delete_btn_confirm" id="deleteConfirmBtn">Подтвердить</button>
        </div>
    </div>
</div>

@if($picture->listing_type === 'auction')
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
@endif

@endsection

@section('scripts')
<style>
.edit_product_btn, .delete_product_btn { display: flex; align-items: center; gap: 10px; padding: 20px 50px; border-radius: 20px; font-family: 'InterTight', sans-serif; font-size: 16px; font-weight: 400; cursor: pointer; transition: all 0.3s; border: none; text-decoration: none; }
.edit_product_btn { background: #939393; color: #222222; border: 1px solid #939393; }
.edit_product_btn:hover { background-color: #0D0D0D; color: #e0e0e0; }
.edit_product_btn:hover img { filter: brightness(0) saturate(100%) invert(88%) sepia(0%) saturate(0%) hue-rotate(0deg) brightness(150%) contrast(88%); }
.delete_product_btn { background-color: #603A3A; color: #BA5D5D; border: 1px solid #603A3A; }
.delete_product_btn:hover { background-color: #0D0D0D; }
.edit_product_btn img, .delete_product_btn img { width: 20px; height: 20px; }
.delete_modal_overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 9999; }
.fav { transition: all 0.3s ease; }
.fav.active { background-color: rgba(251, 255, 131, 0.15); }
.fav.active img { filter: brightness(0) saturate(100%) invert(92%) sepia(93%) saturate(497%) hue-rotate(359deg) brightness(104%) contrast(103%); }
.fav:hover { opacity: 0.8; transform: scale(1.02); }
.fav.loading { pointer-events: none; opacity: 0.6; }
</style>
<script src="{{ asset('script.js') }}"></script>
<script>
const isAuctionPicture = @json($picture->listing_type === 'auction');

function updateProductAuctionTimer() {
    document.querySelectorAll('[data-auction-timer]').forEach((timer) => {
        const endsAt = timer.dataset.endsAt ? new Date(timer.dataset.endsAt) : null;
        if (!endsAt) {
            timer.textContent = 'Не указано';
            return;
        }

        const diff = endsAt.getTime() - Date.now();
        if (diff <= 0) {
            timer.textContent = 'Завершен';
            return;
        }

        const totalSeconds = Math.floor(diff / 1000);
        const days = Math.floor(totalSeconds / 86400);
        const hours = Math.floor((totalSeconds % 86400) / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;

        timer.textContent = days > 0
            ? `${days} д ${hours} ч ${minutes} мин`
            : `${hours} ч ${minutes} мин ${seconds} сек`;
    });
}

updateProductAuctionTimer();
window.setInterval(updateProductAuctionTimer, 1000);

if (isAuctionPicture) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const confirmModal = document.getElementById('auctionConfirmModal');
    const confirmText = document.getElementById('auctionConfirmText');
    const confirmSubmit = document.getElementById('auctionConfirmSubmit');
    const confirmCancel = document.getElementById('auctionConfirmCancel');
    let pendingAction = null;

    function formatPrice(value) {
        return new Intl.NumberFormat('ru-RU').format(Number(value) || 0) + ' ₽';
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

    function showMessage(card, message, isSuccess) {
        const messageNode = card.querySelector('[data-auction-message]');
        if (!messageNode) return;
        messageNode.textContent = message;
        messageNode.classList.toggle('success', isSuccess);
        messageNode.classList.toggle('error', !isSuccess);
    }

    function updateUserStatus(card, status, isLeading = true) {
        const statusNode = card.querySelector('[data-user-status]');
        if (!statusNode) return;
        statusNode.textContent = status;
        statusNode.style.display = '';
        statusNode.classList.toggle('is-leading', isLeading);
        statusNode.classList.toggle('is-outbid', !isLeading);
    }

    async function sendAuctionRequest(url, form) {
        const response = await fetch(url, {
            method: 'POST',
            body: new FormData(form),
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        });

        const result = await response.json();
        return { response, result };
    }

    async function submitBid(form) {
        const card = form.closest('[data-auction-card]');
        const buttons = card.querySelectorAll('[data-bid-form] button');
        buttons.forEach((button) => button.disabled = true);

        try {
            const { response, result } = await sendAuctionRequest('{{ url('/api/auction/bid') }}', form);
            showMessage(card, result.message || 'Готово', response.ok && result.success);

            if (response.ok && result.success) {
                card.querySelector('[data-current-price]').textContent = formatPrice(result.current_price);
                card.querySelector('[data-next-bid-label]').textContent = formatPrice(result.min_next_bid);
                card.querySelector('[data-bids-count]').textContent = result.bids_count;

                const quickAmount = card.querySelector('[data-quick-bid-amount]');
                const quickButton = card.querySelector('[data-quick-bid-button]');
                if (quickAmount) quickAmount.value = result.min_next_bid;
                if (quickButton) quickButton.textContent = 'Поставить ' + formatPrice(result.min_next_bid);

                const quickForm = card.querySelector('.auction_bid_form');
                if (quickForm) {
                    quickForm.dataset.confirmMessage = 'Вы собираетесь поставить ' + formatPrice(result.min_next_bid) + '. Отменить ставку будет нельзя.';
                }

                const input = card.querySelector('[data-bid-input]');
                if (input) {
                    input.min = result.min_next_bid;
                    input.placeholder = 'Минимум ' + formatPrice(result.min_next_bid);
                    input.value = '';
                }

                const leader = card.querySelector('[data-leader-name]');
                if (leader) leader.textContent = 'вы';
                updateUserStatus(card, result.user_status || 'Вы лидируете', true);
            }
        } catch (error) {
            showMessage(card, 'Не удалось отправить ставку. Попробуйте еще раз.', false);
        } finally {
            buttons.forEach((button) => button.disabled = false);
        }
    }

    async function submitBuyout(form) {
        const card = form.closest('[data-auction-card]');
        const button = form.querySelector('button');
        button.disabled = true;

        try {
            const { response, result } = await sendAuctionRequest('{{ url('/api/auction/buyout') }}', form);
            showMessage(card, result.message || 'Готово', response.ok && result.success);

            if (response.ok && result.success) {
                window.setTimeout(() => {
                    window.location.href = result.redirect_url || '{{ url('/checkout') }}';
                }, 700);
            }
        } catch (error) {
            showMessage(card, 'Не удалось оформить блиц-покупку. Попробуйте еще раз.', false);
        } finally {
            button.disabled = false;
        }
    }

    document.querySelectorAll('[data-bid-form]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            const amount = Number(new FormData(form).get('amount'));
            const min = Number(form.querySelector('[name="amount"]')?.min || 0);

            if (min && amount < min) {
                showMessage(form.closest('[data-auction-card]'), 'Минимальная ставка: ' + formatPrice(min), false);
                return;
            }

            const message = form.dataset.confirmMessage
                || 'Вы собираетесь поставить ' + formatPrice(amount) + '. Отменить ставку будет нельзя.';
            openConfirm(message, () => submitBid(form));
        });
    });

    document.querySelectorAll('[data-buyout-form]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            openConfirm(form.dataset.confirmMessage, () => submitBuyout(form), 'Купить');
        });
    });

    confirmSubmit?.addEventListener('click', async () => {
        if (!pendingAction) return;
        confirmSubmit.disabled = true;
        const action = pendingAction;
        closeConfirm();
        await action();
    });

    confirmCancel?.addEventListener('click', closeConfirm);
    confirmModal?.addEventListener('click', (event) => {
        if (event.target === confirmModal) {
            closeConfirm();
        }
    });
}

let pictureToDelete = null;

function getLikesWord(count) {
    const lastDigit = count % 10;
    const lastTwoDigits = count % 100;
    if (lastTwoDigits >= 11 && lastTwoDigits <= 19) return 'лайков';
    else if (lastDigit === 1) return 'лайк';
    else if (lastDigit >= 2 && lastDigit <= 4) return 'лайка';
    else return 'лайков';
}

document.addEventListener('DOMContentLoaded', function() {
    const addToCartBtn = document.getElementById('addToCartBtn');
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', async function() {
            if (this.classList.contains('loading')) return;
            const pictureId = this.getAttribute('data-picture-id');
            this.classList.add('loading');
            this.style.opacity = '0.6';
            this.style.pointerEvents = 'none';
            try {
                const formData = new FormData();
                formData.append('action', 'add');
                formData.append('picture_id', pictureId);
                const response = await fetch('/api/cart', { method: 'POST', body: formData, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
                const result = await response.json();
                if (result.success) { window.location.href = '/cart'; }
                else { this.classList.remove('loading'); this.style.opacity = '1'; this.style.pointerEvents = 'auto'; }
            } catch (error) { this.classList.remove('loading'); this.style.opacity = '1'; this.style.pointerEvents = 'auto'; }
        });
    }
    
    const favButton = document.querySelector('.fav[data-picture-id]');
    if (favButton) {
        favButton.addEventListener('click', async function() {
            if (this.classList.contains('loading')) return;
            const pictureId = this.getAttribute('data-picture-id');
            const isFavorite = this.getAttribute('data-is-favorite') === 'true';
            const action = isFavorite ? 'remove' : 'add';
            this.classList.add('loading');
            try {
                const formData = new FormData();
                formData.append('action', action);
                formData.append('picture_id', pictureId);
                const response = await fetch('/api/favorites', { method: 'POST', body: formData, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
                const result = await response.json();
                if (result.success) {
                    if (result.action === 'added') { this.classList.add('active'); this.setAttribute('data-is-favorite', 'true'); }
                    else { this.classList.remove('active'); this.setAttribute('data-is-favorite', 'false'); }
                    const likesNumber = this.querySelector('.likes-number');
                    const likesText = this.querySelector('.likes-text');
                    if (likesNumber && likesText) { likesNumber.textContent = result.likes_count; likesText.textContent = getLikesWord(result.likes_count); }
                }
            } catch (error) {}
            finally { this.classList.remove('loading'); }
        });
    }
});

function showDeleteModal(id, name) { pictureToDelete = id; document.getElementById('deleteTitle').textContent = `Удалить картину "${name}"?`; document.getElementById('deleteModal').style.display = 'flex'; }
function closeDeleteModal() { document.getElementById('deleteModal').style.display = 'none'; pictureToDelete = null; }

document.getElementById('deleteConfirmBtn')?.addEventListener('click', async function() {
    if (!pictureToDelete) return;
    this.disabled = true;
    this.textContent = 'Удаление...';
    try {
        const formData = new FormData();
        formData.append('picture_id', pictureToDelete);
        const response = await fetch('/api/picture/delete', { method: 'POST', body: formData, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
        const result = await response.json();
        if (result.success) { window.location.href = '/account'; }
        else { this.disabled = false; this.textContent = 'Подтвердить'; }
    } catch (error) { this.disabled = false; this.textContent = 'Подтвердить'; }
});
</script>
@endsection
