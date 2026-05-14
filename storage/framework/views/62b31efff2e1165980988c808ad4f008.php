<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo $__env->make('partials.theme-head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <title>Аккаунт заблокирован</title>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.css')); ?>">
</head>
<body>
    <main class="blocked_page">
        <section class="blocked_card">
            <span class="blocked_badge">Доступ временно ограничен</span>
            <h1>Ваш аккаунт заблокирован на 7 дней</h1>
            <p>
                Вы выиграли картину на аукционе, но не оплатили заказ в течение 24 часов.
                Доступ к сайту восстановится автоматически после окончания блокировки.
            </p>
            <div class="blocked_timer_box">
                <span>До разблокировки осталось</span>
                <strong id="blockedCountdown" data-banned-until="<?php echo e($user->banned_until->toIso8601String()); ?>">
                    <?php echo e($user->banned_until->format('d.m.Y H:i')); ?>

                </strong>
            </div>
            <a href="<?php echo e(url('/logout')); ?>" class="blocked_logout_link">Выйти</a>
        </section>
    </main>

    <script>
    (() => {
        const countdownNode = document.getElementById('blockedCountdown');
        if (!countdownNode) return;

        function renderCountdown() {
            const endsAt = new Date(countdownNode.dataset.bannedUntil);
            const diff = endsAt.getTime() - Date.now();

            if (diff <= 0) {
                window.location.href = "<?php echo e(url('/auth')); ?>";
                return;
            }

            const totalSeconds = Math.floor(diff / 1000);
            const days = Math.floor(totalSeconds / 86400);
            const hours = Math.floor((totalSeconds % 86400) / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;

            countdownNode.textContent = days > 0
                ? `${days} д ${hours} ч ${minutes} мин`
                : `${hours} ч ${minutes} мин ${seconds} сек`;
        }

        renderCountdown();
        window.setInterval(renderCountdown, 1000);
    })();
    </script>
    <?php echo $__env->make('partials.theme-toggle', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script src="<?php echo e(asset('script.js')); ?>"></script>
</body>
</html>
<?php /**PATH C:\OSPanel\domains\canvas-laravel01\resources\views\blocked.blade.php ENDPATH**/ ?>