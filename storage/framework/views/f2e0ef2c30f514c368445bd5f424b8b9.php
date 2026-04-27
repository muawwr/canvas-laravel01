

<?php $__env->startSection('content'); ?>
<main>
    <div class="one_product container">
        <div>
            <a class="btn_back" href="<?php echo e(url('/gallery')); ?>"><img src="<?php echo e(asset('assets/images/oneProduct/back.svg')); ?>" alt=""></a>
        </div>
        <div class="product_image">
            <img src="<?php echo e(asset($picture->img)); ?>" alt="<?php echo e($picture->name); ?>">
        </div>
        <div class="product_info">
            <div class="product_card_author">
                <img src="<?php echo e(asset($picture->user->img ?? 'assets/images/account/mainUser.png')); ?>" alt="<?php echo e($picture->user->name); ?>" class="product_author_avatar">
                <a href="<?php echo e(url('/account?user_id=' . $picture->user_id)); ?>" style="color: inherit; text-decoration: none;">
                    <span><?php echo e($picture->user->name); ?></span>
                </a>
            </div>
            <h3 class="product_name"><?php echo e($picture->name); ?></h3>
            <div class="description_accordion">
                <?php
                    $description = $picture->description;
                    $description_length = mb_strlen($description);
                ?>
                <?php if($description_length > 70): ?>
                    <?php
                        $temp = mb_substr($description, 0, 70);
                        $last_space = mb_strrpos($temp, ' ');
                        $short = $last_space !== false ? mb_substr($description, 0, $last_space) : $temp;
                        $remaining = $last_space !== false ? mb_substr($description, $last_space + 1) : mb_substr($description, 70);
                    ?>
                    <p class="description"><?php echo nl2br(e($short)); ?></p>
                    <div class="description_hidden">
                        <p><?php echo nl2br(e($remaining)); ?></p>
                    </div>
                    <button class="description_toggle">Читать далее</button>
                <?php else: ?>
                    <p class="description"><?php echo nl2br(e($description)); ?></p>
                <?php endif; ?>
            </div>
            <div class="filters">
                <?php if($picture->genre): ?>
                <div class="filter_card">
                    <div class="dote"></div>
                    <p class="filter"><?php echo e($picture->genre->name); ?></p>
                </div>
                <?php endif; ?>
                <?php if($picture->style): ?>
                <div class="filter_card">
                    <div class="dote"></div>
                    <p class="filter"><?php echo e($picture->style->name); ?></p>
                </div>
                <?php endif; ?>
                <?php if($picture->era): ?>
                <div class="filter_card">
                    <div class="dote"></div>
                    <p class="filter"><?php echo e($picture->era->name); ?></p>
                </div>
                <?php endif; ?>
            </div>
            <div class="product_about">
                <div class="product_about_card">
                    <div class="info_card">
                        <p class="info_title">Техника</p>
                        <p class="info_text"><?php echo e($picture->technique); ?></p>
                    </div>
                    <div class="product_line"></div>
                </div>
                <div class="product_about_card">
                    <div class="info_card">
                        <p class="info_title">Размеры</p>
                        <p class="info_text"><?php echo e($picture->width); ?> ✕ <?php echo e($picture->height); ?> см</p>
                    </div>
                    <div class="product_line"></div>
                </div>
                <div class="product_about_card">
                    <div class="info_card">
                        <p class="info_title">Год</p>
                        <p class="info_text"><?php echo e($picture->year); ?></p>
                    </div>
                    <div class="product_line"></div>
                </div>
                <div class="product_about_card">
                    <div class="info_card">
                        <p class="info_title">Цена</p>
                        <p class="info_text price"><?php echo e(number_format($picture->price, 0, '.', ' ')); ?> ₽</p>
                    </div>
                    <div class="product_line"></div>
                </div>
            </div>
            <div class="product_buttons">
                <?php if(session()->has('user_id') && session('user_id') == $picture->user_id): ?>
                    <a href="<?php echo e(url('/edit/' . $picture->id)); ?>" class="edit_product_btn">
                        <img src="<?php echo e(asset('assets/images/oneProduct/edit.svg')); ?>" alt="Редактировать">
                        Редактировать
                    </a>
                    <button class="delete_product_btn" onclick="showDeleteModal(<?php echo e($picture->id); ?>, '<?php echo e(e($picture->name)); ?>')">
                        <img src="<?php echo e(asset('assets/images/oneProduct/Trash.svg')); ?>" alt="Удалить">
                        Удалить
                    </button>
                <?php else: ?>
                    <?php if(session()->has('user_id')): ?>
                    <div class="fav <?php echo e($is_in_favorites ? 'active' : ''); ?>" data-picture-id="<?php echo e($picture->id); ?>" data-is-favorite="<?php echo e($is_in_favorites ? 'true' : 'false'); ?>" style="cursor: pointer;">
                        <img src="<?php echo e(asset('assets/images/oneProduct/Fav.svg')); ?>" alt="">
                        <div class="fav_count">
                            <span class="likes-number"><?php echo e($likes_count); ?></span>
                            <span class="likes-text"><?php
                                $last_digit = $likes_count % 10;
                                $last_two_digits = $likes_count % 100;
                                if ($last_two_digits >= 11 && $last_two_digits <= 19) echo 'лайков';
                                elseif ($last_digit == 1) echo 'лайк';
                                elseif ($last_digit >= 2 && $last_digit <= 4) echo 'лайка';
                                else echo 'лайков';
                            ?></span>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="fav" style="cursor: not-allowed; opacity: 0.6;">
                        <img src="<?php echo e(asset('assets/images/oneProduct/Fav.svg')); ?>" alt="">
                        <div class="fav_count">
                            <span class="likes-number"><?php echo e($likes_count); ?></span>
                            <span class="likes-text">лайков</span>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if($is_sold): ?>
                    <div class="sold-status-btn">
                        <img src="<?php echo e(asset('assets/images/gallery/sold.svg')); ?>" alt="Продано">
                        ПРОДАНО
                    </div>
                    <?php else: ?>
                        <?php if(session()->has('user_id')): ?>
                        <div class="cart" id="addToCartBtn" data-picture-id="<?php echo e($picture->id); ?>" style="cursor: pointer;">
                            В корзину<img src="<?php echo e(asset('assets/images/oneProduct/Right.svg')); ?>" alt="">
                        </div>
                        <?php else: ?>
                        <a href="<?php echo e(url('/auth')); ?>">
                        <div class="cart">
                            В корзину<img src="<?php echo e(asset('assets/images/oneProduct/Right.svg')); ?>" alt="">
                        </div>
                        </a>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<!-- Delete Modal -->
<div class="delete_modal_overlay" id="deleteModal" style="display: none;">
    <div class="delete_modal">
        <div class="delete_icon"><span>?</span></div>
        <h2 class="delete_title" id="deleteTitle">Удалить картину?</h2>
        <div class="delete_buttons">
            <button class="delete_btn delete_btn_back" onclick="closeDeleteModal()">
                <img src="<?php echo e(asset('assets/images/add/Left.svg')); ?>" alt="Back"> Назад
            </button>
            <button class="delete_btn delete_btn_confirm" id="deleteConfirmBtn">Подтвердить</button>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<style>
.edit_product_btn, .delete_product_btn { display: flex; align-items: center; gap: 10px; padding: 20px 50px; border-radius: 20px; font-family: 'InterTight', sans-serif; font-size: 16px; font-weight: 400; cursor: pointer; transition: all 0.3s; border: none; text-decoration: none; }
.edit_product_btn { background: #939393; color: #222222; border: 1px solid #939393; }
.edit_product_btn:hover { background-color: #0D0D0D; color: #e0e0e0; }
.edit_product_btn:hover img { filter: brightness(0) saturate(100%) invert(88%) sepia(0%) saturate(0%) hue-rotate(0deg) brightness(150%) contrast(88%); }
.delete_product_btn { background-color: #603A3A; color: #BA5D5D; border: 1px solid #603A3A; }
.delete_product_btn:hover { background-color: #0D0D0D; }
.edit_product_btn img, .delete_product_btn img { width: 20px; height: 20px; }
.delete_modal_overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 9999; }
@keyframes slideIn { from { transform: translateX(400px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
.fav { transition: all 0.3s ease; }
.fav.active { background-color: rgba(251, 255, 131, 0.15); }
.fav.active img { filter: brightness(0) saturate(100%) invert(92%) sepia(93%) saturate(497%) hue-rotate(359deg) brightness(104%) contrast(103%); }
.fav:hover { opacity: 0.8; transform: scale(1.02); }
.fav.loading { pointer-events: none; opacity: 0.6; }
</style>
<script src="<?php echo e(asset('script.js')); ?>"></script>
<script>
let pictureToDelete = null;

function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    const icon = type === 'success' ? '✓' : '✗';
    toast.innerHTML = `<span style="font-size:20px">${icon}</span><span>${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => { toast.style.animation = 'slideOut 0.3s ease-out forwards'; setTimeout(() => toast.remove(), 300); }, 3000);
}

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
                if (result.success) { showToast('Добавлено в корзину'); setTimeout(() => { window.location.href = '/cart'; }, 1500); }
                else { showToast(result.message || 'Ошибка', 'error'); this.classList.remove('loading'); this.style.opacity = '1'; this.style.pointerEvents = 'auto'; }
            } catch (error) { showToast('Ошибка сервера', 'error'); this.classList.remove('loading'); this.style.opacity = '1'; this.style.pointerEvents = 'auto'; }
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
                    if (result.action === 'added') { this.classList.add('active'); this.setAttribute('data-is-favorite', 'true'); showToast('Добавлено в избранное'); }
                    else { this.classList.remove('active'); this.setAttribute('data-is-favorite', 'false'); showToast('Удалено из избранного'); }
                    const likesNumber = this.querySelector('.likes-number');
                    const likesText = this.querySelector('.likes-text');
                    if (likesNumber && likesText) { likesNumber.textContent = result.likes_count; likesText.textContent = getLikesWord(result.likes_count); }
                } else { showToast(result.message || 'Ошибка', 'error'); }
            } catch (error) { showToast('Ошибка сервера', 'error'); }
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
        if (result.success) { showToast('Картина удалена'); setTimeout(() => { window.location.href = '/account'; }, 1500); }
        else { showToast(result.message || 'Ошибка', 'error'); this.disabled = false; this.textContent = 'Подтвердить'; }
    } catch (error) { showToast('Ошибка сервера', 'error'); this.disabled = false; this.textContent = 'Подтвердить'; }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\canvas-laravel01\resources\views\picture\show.blade.php ENDPATH**/ ?>