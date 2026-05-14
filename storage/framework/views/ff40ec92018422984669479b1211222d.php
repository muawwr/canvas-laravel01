<?php $__env->startSection('content'); ?>
<main class="auction_workspace container notifications_screen">
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
                    class="notification_card"
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

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\canvas-laravel01\resources\views\notifications\index.blade.php ENDPATH**/ ?>