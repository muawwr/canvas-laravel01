<?php $__env->startSection('content'); ?>
<main class="auction_workspace container notifications_screen" data-auction-win-fireworks="<?php echo e($hasFreshAuctionWin ?? false ? 'true' : 'false'); ?>">
    <div class="auction_workspace_title">
        <h1>Уведомления:</h1>
        <span><?php echo e($notifications->count()); ?></span>
    </div>

    <?php if($notifications->isEmpty()): ?>
        <div class="auction_empty">
            <h2>Уведомлений пока нет</h2>
            <p>Когда появятся новые события по картинам, заказам и аукционам, они отобразятся здесь.</p>
        </div>
    <?php else: ?>
        <div class="notifications_list">
            <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <article
                    class="notification_card <?php echo e($notification->type === 'auction_winner' ? 'notification_card_winner' : ''); ?>"
                    data-notification-card
                    <?php if($notification->url): ?> data-notification-url="<?php echo e($notification->url); ?>" <?php endif; ?>
                >
                    <div class="notification_card_head">
                        <div class="notification_card_title_wrap">
                            <h2><?php echo e($notification->title); ?></h2>
                        </div>
                        <time datetime="<?php echo e(optional($notification->created_at)->toIso8601String()); ?>">
                            <?php echo e(optional($notification->created_at)->format('d.m.Y H:i')); ?>

                        </time>
                    </div>
                    <p class="notification_card_text"><?php echo e($notification->message); ?></p>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
(() => {
    const screen = document.querySelector('.notifications_screen');

    function launchFireworks() {
        const layer = document.createElement('div');
        layer.className = 'auction-fireworks';
        document.body.appendChild(layer);

        const bursts = 11;

        for (let burst = 0; burst < bursts; burst += 1) {
            const x = 8 + Math.random() * 84;
            const y = 10 + Math.random() * 48;
            const particles = 30;

            window.setTimeout(() => {
                for (let i = 0; i < particles; i += 1) {
                    const particle = document.createElement('span');
                    const angle = (Math.PI * 2 * i) / particles;
                    const distance = 110 + Math.random() * 155;
                    particle.style.left = `${x}vw`;
                    particle.style.top = `${y}vh`;
                    particle.style.setProperty('--x', `${Math.cos(angle) * distance}px`);
                    particle.style.setProperty('--y', `${Math.sin(angle) * distance}px`);
                    particle.style.background = '#FBFF83';
                    particle.style.color = '#FBFF83';
                    layer.appendChild(particle);
                }
            }, burst * 220);
        }

        window.setTimeout(() => layer.remove(), 5200);
    }

    if (screen?.dataset.auctionWinFireworks === 'true') {
        window.setTimeout(launchFireworks, 250);
    }

    document.querySelectorAll('[data-notification-card]').forEach((card) => {
        card.addEventListener('click', () => {
            const targetUrl = card.dataset.notificationUrl || '';
            if (targetUrl) {
                window.location.href = targetUrl;
            }
        });
    });
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\canvas-laravel01\resources\views/notifications/index.blade.php ENDPATH**/ ?>