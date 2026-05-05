<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оформление заказа - Канвас</title>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.css')); ?>">
    <link rel="shortcut icon" href="<?php echo e(asset('assets/images/header/logo.svg')); ?>" type="image/x-icon">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
</head>
<body>
    <div class="checkout-page" style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #0D0D0D;">
        <div class="checkout-container" style="max-width: 500px; width: 100%; padding: 40px; background: #1A1A1A; border-radius: 20px; margin: 20px;">
            <h2 style="font-size: 32px; color: #E0E0E0; text-align: center; font-weight:400; margin-bottom: 30px; font-family: 'Neue Haas Grotesk', sans-serif;">Оформление заказа</h2>

            <?php if($auctionPicture): ?>
                <form method="POST" action="<?php echo e(url('/checkout')); ?>" id="checkoutFormFinal">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="auction_picture_id" value="<?php echo e($auctionPicture->id); ?>">

                    <div style="background: #2D2D2D; padding: 20px; border-radius: 15px; margin-bottom: 20px;">
                        <h3 style="font-size: 20px; color: #E0E0E0; font-weight:400; margin-bottom: 15px;">Товар:</h3>
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <img src="<?php echo e(asset($auctionPicture->img)); ?>" alt="<?php echo e($auctionPicture->name); ?>" style="width: 70px; height: 70px; object-fit: cover; border-radius: 10px;">
                            <div style="flex: 1;">
                                <div style="color: #E0E0E0; font-size: 16px; margin-bottom: 3px;"><?php echo e($auctionPicture->name); ?></div>
                                <div style="color: #939393; font-size: 14px;"><?php echo e($auctionPicture->user->name ?? 'Автор'); ?></div>
                            </div>
                            <div style="color: #FBFF83; font-size: 18px; font-weight: 600;"><?php echo e(number_format($auctionPicture->auction_buyout_price, 0, '.', ' ')); ?> ₽</div>
                        </div>
                    </div>

                    <div style="background: #2D2D2D; padding: 20px; border-radius: 15px; margin-bottom: 20px;">
                        <h3 style="font-size: 20px; color: #E0E0E0; font-weight:400; margin-bottom: 15px;">Данные доставки:</h3>
                        <label style="color: #939393; font-size: 14px; display: block; margin-bottom: 5px;">Пункт выдачи:</label>
                        <input type="text" name="pickup_point" required style="width: 100%; margin-bottom: 15px; padding: 16px; border-radius: 12px; border: 1px solid #3D3D3D; background: #1A1A1A; color: #E0E0E0;">

                        <label style="color: #939393; font-size: 14px; display: block; margin-bottom: 5px;">Получатель:</label>
                        <input type="text" name="recipient_name" required style="width: 100%; padding: 16px; border-radius: 12px; border: 1px solid #3D3D3D; background: #1A1A1A; color: #E0E0E0;">
                    </div>

                    <button type="submit" style="width: 100%; padding: 20px; background: #FBFF83; color: #0D0D0D; border: none; border-radius: 15px; font-size: 18px; font-weight: 400; cursor: pointer; font-family: 'InterTight', sans-serif; transition: all 0.3s;">
                        Оформить заказ
                    </button>

                    <a href="<?php echo e(url('/auction')); ?>" style="display: block; text-align: center; margin-top: 15px; color: #939393; text-decoration: none; transition: color 0.3s;">
                        Вернуться к аукциону
                    </a>
                </form>
            <?php else: ?>
                <div id="loadingMessage" style="text-align: center; color: #939393;">
                    <p>Загрузка данных...</p>
                </div>

                <form method="POST" action="<?php echo e(url('/checkout')); ?>" id="checkoutFormFinal" style="display: none;">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="picture_ids" id="pictureIds">
                    <input type="hidden" name="pickup_point" id="pickupPointHidden">
                    <input type="hidden" name="recipient_name" id="recipientNameHidden">

                    <div style="background: #2D2D2D; padding: 20px; border-radius: 15px; margin-bottom: 20px;">
                        <h3 style="font-size: 20px; color: #E0E0E0; font-weight:400; margin-bottom: 15px;">Товары:</h3>
                        <div id="orderItems"></div>
                        <div style="padding-top: 15px;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: #939393; font-size: 16px;">Итого:</span>
                                <span id="totalAmount" style="color: #FBFF83; font-size: 24px; font-weight: 600;"></span>
                            </div>
                        </div>
                    </div>

                    <div style="background: #2D2D2D; padding: 20px; border-radius: 15px; margin-bottom: 20px;">
                        <h3 style="font-size: 20px; color: #E0E0E0; font-weight:400; margin-bottom: 15px;">Данные доставки:</h3>
                        <div style="margin-bottom: 10px;">
                            <label style="color: #939393; font-size: 14px; display: block; margin-bottom: 5px;">Пункт выдачи:</label>
                            <div id="pickupPointDisplay" style="color: #E0E0E0; font-size: 16px;"></div>
                        </div>
                        <div>
                            <label style="color: #939393; font-size: 14px; display: block; margin-bottom: 5px;">Получатель:</label>
                            <div id="recipientNameDisplay" style="color: #E0E0E0; font-size: 16px;"></div>
                        </div>
                    </div>

                    <button type="submit" style="width: 100%; padding: 20px; background: #FBFF83; color: #0D0D0D; border: none; border-radius: 15px; font-size: 18px; font-weight: 400; cursor: pointer; font-family: 'InterTight', sans-serif; transition: all 0.3s;">
                        Оформить заказ
                    </button>

                    <a href="<?php echo e(url('/cart')); ?>" style="display: block; text-align: center; margin-top: 15px; color: #939393; text-decoration: none; transition: color 0.3s;">
                        Вернуться в корзину
                    </a>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <?php if(!$auctionPicture): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkoutData = localStorage.getItem('checkout_data');
        if (!checkoutData) { window.location.href = '/cart'; return; }
        const data = JSON.parse(checkoutData);

        document.getElementById('pictureIds').value = data.pictures.join(',');
        document.getElementById('pickupPointHidden').value = data.pickup_point;
        document.getElementById('recipientNameHidden').value = data.recipient_name;
        document.getElementById('pickupPointDisplay').textContent = data.pickup_point;
        document.getElementById('recipientNameDisplay').textContent = data.recipient_name;

        const formData = new FormData();
        formData.append('action', 'get');
        fetch('/api/cart', { method: 'POST', body: formData, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
        .then(r => r.json())
        .then(result => {
            if (result.success) {
                const selectedItems = result.items.filter(item => data.pictures.includes(String(item.picture_id)));
                let itemsHtml = '';
                let total = 0;
                selectedItems.forEach(item => {
                    total += parseFloat(item.price);
                    itemsHtml += `<div style="display: flex; align-items: center; gap: 15px; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #3D3D3D;">
                        <img src="${item.img}" alt="${item.name}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 10px;">
                        <div style="flex: 1;">
                            <div style="color: #E0E0E0; font-size: 16px; margin-bottom: 3px;">${item.name}</div>
                            <div style="color: #939393; font-size: 14px;">${item.author_name}</div>
                        </div>
                        <div style="color: #FBFF83; font-size: 18px; font-weight: 600;">${item.price.toLocaleString('ru-RU')} ₽</div>
                    </div>`;
                });
                document.getElementById('orderItems').innerHTML = itemsHtml;
                document.getElementById('totalAmount').textContent = `${total.toLocaleString('ru-RU')} ₽`;
                document.getElementById('loadingMessage').style.display = 'none';
                document.getElementById('checkoutFormFinal').style.display = 'block';
            }
        });
    });
    </script>
    <?php endif; ?>
</body>
</html>
<?php /**PATH C:\OSPanel\domains\canvas-laravel01\resources\views\checkout.blade.php ENDPATH**/ ?>