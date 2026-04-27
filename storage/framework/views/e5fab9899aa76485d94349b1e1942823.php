<?php $__env->startSection('content'); ?>
<main>
    <div class="cart_main container">
        <div class="main_page_title">
            <h3 class="main_title">В корзине:</h3>
            <div class="count_page" id="cartTotalCount"><?php echo e($cartItems->count()); ?></div>
        </div>

        <?php if($cartItems->isEmpty()): ?>
            <div class="empty-cart" style="text-align: center; padding: 60px 20px; color: #939393;">
                <img src="<?php echo e(asset('assets/images/header/Cart.svg')); ?>" alt="Корзина" style="width: 80px; height: 80px; opacity: 0.3; margin-bottom: 20px;">
                <h3 style="font-size: 24px; margin-bottom: 10px;">Корзина пуста</h3>
                <p style="font-size: 16px; margin-bottom: 30px;">Добавьте картины в корзину, чтобы они появились здесь</p>
                <a href="<?php echo e(url('/gallery')); ?>" class="btn" style="display: inline-block; padding: 15px 40px; background: #FBFF83; color: #0D0D0D; text-decoration: none; border-radius: 15px; font-weight: 500;">
                    Перейти в галерею
                </a>
            </div>
        <?php else: ?>
            <div class="cart_content">
                <div class="cart_items" id="cartItemsList">
                    <?php $__currentLoopData = $cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="cart_item" data-picture-id="<?php echo e($item->picture_id); ?>" data-price="<?php echo e($item->picture->price); ?>">
                            <label class="cart_checkbox">
                                <input type="checkbox" class="cart_item_checkbox" data-price="<?php echo e($item->picture->price); ?>">
                                <span class="checkmark"></span>
                            </label>

                            <a href="<?php echo e(url('/picture/' . $item->picture_id)); ?>">
                                <div class="cart_item_image">
                                    <img src="<?php echo e(asset($item->picture->img)); ?>" alt="<?php echo e($item->picture->name); ?>">
                                </div>
                            </a>

                            <div class="cart_item_info">
                                <h4 class="cart_item_name"><?php echo e($item->picture->name); ?></h4>
                                <p class="cart_item_author"><?php echo e($item->picture->user->name); ?></p>
                            </div>

                            <p class="cart_item_price"><?php echo e(number_format($item->picture->price, 0, '.', ' ')); ?> <span>₽</span></p>

                            <button class="cart_item_delete" type="button" data-picture-id="<?php echo e($item->picture_id); ?>">
                                <img src="<?php echo e(asset('assets/images/cart/Trashcan.svg')); ?>" alt="Удалить">
                            </button>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="cart_all_info">
                    <form id="checkoutForm" class="cart_checkout">
                        <div class="cart_count">
                            <label class="checkout_count">Кол-во картин</label>
                            <div class="checkout_count_value" id="selectedCount">0</div>
                        </div>
                        <div class="checkout_line"></div>

                        <div class="cart_checkout_field">
                            <label class="checkout_label" for="pickupPoint">Заполните пункт выдачи</label>
                            <div class="checkout_input_wrapper">
                                <input type="text" placeholder="г. Москва, ул. Ленина, д. 1" class="checkout_input" id="pickupPoint" name="pickup_point" required>
                            </div>
                        </div>

                        <div class="cart_checkout_field">
                            <label class="checkout_label" for="recipientName">Фамилия и имя получателя</label>
                            <input type="text" placeholder="Иванов Иван" class="checkout_input" id="recipientName" name="recipient_name" required>
                        </div>

                        <div class="cart_total">
                            <span class="total_label">ИТОГО</span>
                            <span class="total_price" id="totalPrice">0 <span>₽</span></span>
                        </div>

                        <button type="submit" class="checkout_button" id="checkoutBtn">
                            <img src="<?php echo e(asset('assets/images/cart/Subtract.svg')); ?>" alt="">
                            <span>Перейти к оформлению</span>
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 10000;"></div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<style>
.cart_item {
    transition: opacity 0.3s ease;
}

.cart_item.removing {
    opacity: 0.3;
    pointer-events: none;
}

.toast {
    background: #2D2D2D;
    color: #E0E0E0;
    padding: 16px 24px;
    border-radius: 12px;
    margin-bottom: 10px;
    min-width: 300px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    animation: slideIn 0.3s ease-out;
    display: flex;
    align-items: center;
    gap: 12px;
}

.toast.success {
    border-left: 4px solid #FBFF83;
}

.toast.error {
    border-left: 4px solid #C76060;
}

@keyframes slideIn {
    from {
        transform: translateX(400px);
        opacity: 0;
    }

    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }

    to {
        transform: translateX(400px);
        opacity: 0;
    }
}
</style>

<script>
function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    const icon = type === 'success' ? '✓' : '✗';
    toast.innerHTML = `<span style="font-size:20px">${icon}</span><span>${message}</span>`;
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease-out forwards';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

function updateCartSummary() {
    const checkboxes = document.querySelectorAll('.cart_item_checkbox:checked');
    let count = checkboxes.length;
    let total = 0;

    checkboxes.forEach((checkbox) => {
        total += parseFloat(checkbox.getAttribute('data-price'));
    });

    const selectedCount = document.getElementById('selectedCount');
    const totalPrice = document.getElementById('totalPrice');
    const checkoutBtn = document.getElementById('checkoutBtn');

    if (selectedCount) {
        selectedCount.textContent = count;
    }

    if (totalPrice) {
        totalPrice.innerHTML = `${total.toLocaleString('ru-RU')} <span>₽</span>`;
    }

    if (checkoutBtn) {
        checkoutBtn.style.opacity = count > 0 ? '1' : '0.5';
        checkoutBtn.style.pointerEvents = count > 0 ? 'auto' : 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.cart_item_checkbox');
    const deleteButtons = document.querySelectorAll('.cart_item_delete');
    const checkoutForm = document.getElementById('checkoutForm');

    checkboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', updateCartSummary);
    });

    deleteButtons.forEach((button) => {
        button.addEventListener('click', async function() {
            const pictureId = this.getAttribute('data-picture-id');
            const cartItem = this.closest('.cart_item');

            cartItem.classList.add('removing');

            try {
                const formData = new FormData();
                formData.append('action', 'remove');
                formData.append('picture_id', pictureId);

                const response = await fetch('/api/cart', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();

                if (result.success) {
                    showToast('Удалено из корзины', 'success');

                    setTimeout(() => {
                        cartItem.remove();

                        document.getElementById('cartTotalCount').textContent = result.cart_count;
                        updateCartSummary();

                        if (result.cart_count === 0) {
                            setTimeout(() => window.location.reload(), 500);
                        }
                    }, 300);
                } else {
                    showToast(result.message || 'Ошибка при удалении', 'error');
                    cartItem.classList.remove('removing');
                }
            } catch (error) {
                showToast('Ошибка подключения к серверу', 'error');
                cartItem.classList.remove('removing');
            }
        });
    });

    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const selectedCheckboxes = document.querySelectorAll('.cart_item_checkbox:checked');
            if (selectedCheckboxes.length === 0) {
                showToast('Выберите хотя бы один товар', 'error');
                return;
            }

            const pickupPoint = document.getElementById('pickupPoint').value.trim();
            const recipientName = document.getElementById('recipientName').value.trim();

            if (!pickupPoint || !recipientName) {
                showToast('Заполните все поля', 'error');
                return;
            }

            const selectedPictures = [];
            selectedCheckboxes.forEach((checkbox) => {
                const cartItem = checkbox.closest('.cart_item');
                selectedPictures.push(cartItem.getAttribute('data-picture-id'));
            });

            localStorage.setItem('checkout_data', JSON.stringify({
                pictures: selectedPictures,
                pickup_point: pickupPoint,
                recipient_name: recipientName
            }));

            window.location.href = '/checkout';
        });
    }

    updateCartSummary();
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\canvas-laravel01\resources\views/cart.blade.php ENDPATH**/ ?>