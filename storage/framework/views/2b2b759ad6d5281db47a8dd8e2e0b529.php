<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo $__env->make('partials.theme-head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <title>Оплата успешна - Канвас</title>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.css')); ?>">
    <link rel="shortcut icon" href="<?php echo e(asset('assets/images/header/logo.svg')); ?>" type="image/x-icon">
</head>
<body>
    <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: var(--theme-bg);">
        <div style="max-width: 500px; text-align: center; padding: 30px; background: var(--theme-surface-strong); border-radius: 20px; box-shadow: var(--theme-shadow); margin: 20px;">
            <div style="width: 60px; height: 60px; background: var(--theme-accent); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                <img src="<?php echo e(asset('assets/images/add/Success.svg')); ?>" alt="Success">
            </div>
            <h2 style="font-weight: 400; font-size: 32px; color: var(--theme-text); margin-bottom: 15px; font-family: 'Neue Haas Grotesk', sans-serif;">Оплата успешна!</h2>
            <p style="color: var(--theme-muted); margin-bottom: 30px; line-height: 1.6;">
                Ваш заказ успешно оформлен. Информация о заказах доступна в разделе «Заказы».
            </p>
            <a href="<?php echo e(url('/orders')); ?>" style="display: inline-block; padding: 15px 40px; background: var(--theme-accent); color: var(--theme-accent-contrast); text-decoration: none; border-radius: 15px; font-weight: 400;">
                Перейти к заказам
            </a>
            <br>
            <a href="<?php echo e(url('/gallery')); ?>" style="display: inline-block; margin-top: 15px; color: var(--theme-muted); text-decoration: none;">
                Вернуться в галерею
            </a>
        </div>
    </div>

    <script>
    localStorage.removeItem('checkout_data');
    </script>
    <?php echo $__env->make('partials.theme-toggle', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script src="<?php echo e(asset('script.js')); ?>"></script>
</body>
</html>
<?php /**PATH C:\OSPanel\domains\canvas-laravel01\resources\views\checkout-success.blade.php ENDPATH**/ ?>