<?php $__env->startSection('nav-auction-active', 'active'); ?>

<?php $__env->startSection('content'); ?>
<main class="auction_main container">
    <div class="auction_header">
        <div>
            <h1 class="auction_title">Аукцион</h1>
            <p class="auction_subtitle">Картины, которые можно приобрести по ставке или сразу по блиц-цене.</p>
        </div>
        <a href="<?php echo e(session()->has('user_id') ? url('/add') : url('/auth')); ?>" class="auction_add_link">Выставить работу</a>
    </div>

    <div class="auction_grid">
        <?php $__empty_1 = true; $__currentLoopData = $auctions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $picture): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
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
            ?>

            <article class="auction_card <?php echo e($isFinished ? 'auction_card_finished' : ''); ?>" data-auction-card data-picture-id="<?php echo e($picture->id); ?>">
                <a href="<?php echo e(url('/picture/' . $picture->id)); ?>" class="auction_card_image">
                    <img src="<?php echo e(asset($picture->img)); ?>" alt="<?php echo e($picture->name); ?>">
                    <span class="auction_status"><?php echo e($isFinished ? 'Завершен' : 'Идет торг'); ?></span>
                </a>

                <div class="auction_card_body">
                    <div class="auction_author">
                        <img src="<?php echo e(asset($picture->user->img ?? 'assets/images/account/mainUser.png')); ?>" alt="<?php echo e($picture->user->name ?? 'Автор'); ?>">
                        <a href="<?php echo e(url('/account?user_id=' . $picture->user_id)); ?>"><?php echo e($picture->user->name ?? 'Неизвестный автор'); ?></a>
                    </div>

                    <h2 class="auction_card_title"><?php echo e($picture->name); ?></h2>

                    <div class="auction_meta">
                        <span><?php echo e($picture->technique); ?></span>
                        <span><?php echo e($picture->width); ?> x <?php echo e($picture->height); ?> см</span>
                        <?php if($picture->genre): ?>
                            <span><?php echo e($picture->genre->name); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="auction_prices">
                        <div>
                            <span class="auction_price_label">Текущая ставка</span>
                            <strong><?php echo e(number_format($currentPrice, 0, '.', ' ')); ?> ₽</strong>
                        </div>
                        <div>
                            <span class="auction_price_label">До завершения</span>
                            <strong data-auction-timer data-ends-at="<?php echo e($endsAt ? $endsAt->toIso8601String() : ''); ?>"><?php echo e($isFinished ? 'Завершен' : '...'); ?></strong>
                        </div>
                        <div>
                            <span class="auction_price_label">Ставок</span>
                            <strong><?php echo e($picture->auction_bids_count); ?></strong>
                        </div>
                        <?php if($picture->auction_buyout_price): ?>
                            <div>
                                <span class="auction_price_label">Блиц-цена</span>
                                <strong><?php echo e(number_format($picture->auction_buyout_price, 0, '.', ' ')); ?> ₽</strong>
                            </div>
                        <?php endif; ?>
                    </div>

                    <a class="auction_login_link" href="<?php echo e(url('/picture/' . $picture->id)); ?>">Открыть картину</a>
                </div>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="auction_empty">
                <h2>Активных аукционов пока нет</h2>
                <p>Когда продавцы выставят картины на торги и модератор их одобрит, они появятся здесь.</p>
            </div>
        <?php endif; ?>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\canvas-laravel01\resources\views/auction/index.blade.php ENDPATH**/ ?>