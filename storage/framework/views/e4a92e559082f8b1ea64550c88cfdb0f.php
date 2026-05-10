
<?php $__env->startSection('nav-profile-active', 'active'); ?>

<?php $__env->startSection('content'); ?>
<main class="account_main container">
    <div class="account_profile">
        <div class="account_avatar" id="accountAvatar">
            <img src="<?php echo e(asset($user_data->img)); ?>" alt="Profile" id="avatarImage">
            <?php if($is_own_profile): ?>
            <div class="avatar_upload_overlay" id="avatarUploadOverlay" style="display: none;">
                <img src="<?php echo e(asset('assets/images/account/update_img.svg')); ?>" alt="Изменить" style="width: 40px; height: 40px; filter: brightness(0) saturate(100%) invert(88%) sepia(0%) saturate(0%) hue-rotate(0deg) brightness(150%) contrast(88%);">
                <input type="file" id="avatarUploadInput" accept="image/*" style="display: none;">
            </div>
            <?php endif; ?>
        </div>
        <div class="account_badge <?php echo e($rank_class); ?>">
            <img src="<?php echo e(asset('assets/images/account/' . $rank_icon)); ?>" alt="<?php echo e($rank_label); ?>">
            <span><?php echo e(strtoupper($rank_label)); ?></span>
        </div>
        <h1 class="account_name" id="accountNameDisplay"><?php echo e($user_data->name); ?></h1>
        <?php if($is_own_profile): ?>
        <input type="text" class="account_name_edit" id="accountNameEdit" value="<?php echo e($user_data->name); ?>" style="display: none;" maxlength="50">
        <?php endif; ?>
    </div>
    <div class="account_stats">
        <?php if($is_own_profile): ?>
        <button class="account_edit_btn" id="editProfileBtn">
            <img src="<?php echo e(asset('assets/images/account/edit.svg')); ?>" alt="Редактировать" style="width: 15px; height: 15px; margin-right: 10px;">
            <span id="editBtnText">Редактировать</span>
        </button>
        <?php endif; ?>
        <div class="account_stat">
            <span class="stat_number"><?php echo e($user_data->pictures_count); ?></span>
            <span class="stat_label">картин</span>
        </div>
        <div class="account_stat">
            <span class="stat_number"><?php echo e($user_data->orders_count); ?></span>
            <span class="stat_label">заказов</span>
        </div>
        <div class="account_stat">
            <span class="stat_number"><?php echo e($user_data->profile_views ?? 0); ?></span>
            <span class="stat_label">просмотров</span>
        </div>
    </div>

    <div class="gallery-grid-masonry">
        <?php $__empty_1 = true; $__currentLoopData = $user_pictures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $picture): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="gallery-card account_picture_card">
            <a href="<?php echo e(url('/picture/' . $picture->id)); ?>">
                <img src="<?php echo e(asset($picture->img)); ?>" alt="<?php echo e($picture->name); ?>">
                <?php if($picture->is_sold > 0): ?>
                    <div class="sold-badge">
                        <img src="<?php echo e(asset('assets/images/gallery/sold.svg')); ?>" alt="Продано">
                        <span>ПРОДАНО</span>
                    </div>
                <?php endif; ?>
            </a>

            <?php if($is_own_profile && $picture->has_failed_auction): ?>
                <div class="account_auction_retry_box">
                    <div class="account_auction_retry_head">
                        <strong>Картина не была продана</strong>
                        <span class="account_retry_info">i<span class="account_retry_tooltip">&#1055;&#1086;&#1073;&#1077;&#1076;&#1080;&#1090;&#1077;&#1083;&#1100; &#1072;&#1091;&#1082;&#1094;&#1080;&#1086;&#1085;&#1072; &#1085;&#1077; &#1086;&#1087;&#1083;&#1072;&#1090;&#1080;&#1083; &#1082;&#1072;&#1088;&#1090;&#1080;&#1085;&#1091; &#1074; &#1090;&#1077;&#1095;&#1077;&#1085;&#1080;&#1077; 24 &#1095;&#1072;&#1089;&#1086;&#1074;</span></span>
                    </div>
                    <span class="account_auction_retry_text">Хотите опубликовать картину снова?</span>
                    <div class="account_auction_retry_actions">
                        <button class="account_retry_btn" type="button" data-relist-auction-picture-id="<?php echo e($picture->id); ?>">Да</button>
                        <button class="account_retry_btn account_retry_btn_secondary" type="button" data-delete-auction-picture-id="<?php echo e($picture->id); ?>">Нет, удалить</button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="gallery-card" style="text-align: center; color: #999; padding: 60px;">
            У вас пока нет добавленных картин
        </div>
        <?php endif; ?>
    </div>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<?php if($is_own_profile): ?>
<script>
(function() {
    'use strict';
    let isEditMode = false;
    let newAvatarFile = null;

    const editBtn = document.getElementById('editProfileBtn');
    const editBtnText = document.getElementById('editBtnText');
    const editBtnIcon = editBtn.querySelector('img');
    const nameDisplay = document.getElementById('accountNameDisplay');
    const nameEdit = document.getElementById('accountNameEdit');
    const avatarUploadOverlay = document.getElementById('avatarUploadOverlay');
    const avatarUploadInput = document.getElementById('avatarUploadInput');
    const avatarImage = document.getElementById('avatarImage');
    const accountAvatar = document.getElementById('accountAvatar');

    editBtn.addEventListener('click', function() {
        if (!isEditMode) { enterEditMode(); } else { saveProfile(); }
    });

    function enterEditMode() {
        isEditMode = true;
        nameDisplay.style.display = 'none';
        nameEdit.style.display = 'block';
        nameEdit.focus();
        avatarUploadOverlay.style.display = 'flex';
        accountAvatar.style.cursor = 'pointer';
        editBtnIcon.src = '<?php echo e(asset("assets/images/account/accept.svg")); ?>';
        editBtnText.textContent = 'Применить';
        editBtn.style.background = 'transparent';
        editBtn.style.color = '#E0E0E0';
        editBtn.style.border = '1px solid #E0E0E0';
    }

    function exitEditMode() {
        isEditMode = false;
        newAvatarFile = null;
        nameDisplay.style.display = 'block';
        nameEdit.style.display = 'none';
        avatarUploadOverlay.style.display = 'none';
        accountAvatar.style.cursor = 'default';
        editBtnIcon.src = '<?php echo e(asset("assets/images/account/edit.svg")); ?>';
        editBtnText.textContent = 'Редактировать';
        editBtn.style.background = '';
        editBtn.style.color = '';
        editBtn.style.border = '';
    }

    accountAvatar.addEventListener('click', function() { if (isEditMode) avatarUploadInput.click(); });

    avatarUploadInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (!file.type.startsWith('image/')) { return; }
            if (file.size > 5 * 1024 * 1024) { return; }
            newAvatarFile = file;
            const reader = new FileReader();
            reader.onload = function(e) { avatarImage.src = e.target.result; };
            reader.readAsDataURL(file);
        }
    });

    async function saveProfile() {
        const newName = nameEdit.value.trim();
        if (!newName || newName.length < 2) { return; }
        editBtn.disabled = true;
        editBtnText.textContent = 'Сохранение...';
        try {
            const formData = new FormData();
            formData.append('name', newName);
            if (newAvatarFile) formData.append('avatar', newAvatarFile);
            const response = await fetch('/api/profile/update', { method: 'POST', body: formData, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
            const result = await response.json();
            if (result.success) {
                nameDisplay.textContent = newName;
                if (result.avatar_url) { avatarImage.src = result.avatar_url + '?t=' + Date.now(); }
                exitEditMode();
            }
        } catch (error) {}
        finally { editBtn.disabled = false; if (isEditMode) editBtnText.textContent = 'Применить'; }
    }

    document.querySelectorAll('[data-relist-auction-picture-id]').forEach((button) => {
        button.addEventListener('click', async () => {
            button.disabled = true;
            const formData = new FormData();
            formData.append('picture_id', button.dataset.relistAuctionPictureId);

            try {
                const response = await fetch('/api/picture/relist-auction', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                const result = await response.json();
                if (result.success) {
                    window.location.reload();
                    return;
                }
                alert(result.message || 'Не удалось снова выставить картину на аукцион');
            } catch (error) {
                alert('Не удалось снова выставить картину на аукцион');
            } finally {
                button.disabled = false;
            }
        });
    });

    document.querySelectorAll('[data-delete-auction-picture-id]').forEach((button) => {
        button.addEventListener('click', async () => {
            button.disabled = true;
            const formData = new FormData();
            formData.append('picture_id', button.dataset.deleteAuctionPictureId);

            try {
                const response = await fetch('/api/picture/delete', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                const result = await response.json();
                if (result.success) {
                    window.location.reload();
                }
            } catch (error) {
            } finally {
                button.disabled = false;
            }
        });
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isEditMode) {
            nameEdit.value = nameDisplay.textContent;
            if (newAvatarFile) avatarImage.src = '<?php echo e(asset($user_data->img)); ?>';
            exitEditMode();
        }
    });
})();
</script>
<style>
.account_name_edit {     font-family: 'Neue Haas Grotesk', sans-serif;
    font-size: 32px;
    font-weight: 400;
    color: #E0E0E0;
    margin: 20px 0 20px 27px;
    border: none !important;
    outline: none !important;
    background: #0d0d0d;
    border-bottom: 2px solid #E0E0E0;
    max-width: 300px;
 }
    .account_name_edit:focus{ background-color: #0d0d0d;}
.avatar_upload_overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease; opacity: 0; }
.account_avatar:hover .avatar_upload_overlay { opacity: 1; }
.account_edit_btn { display: flex; align-items: center; justify-content: center; padding: 20px 50px; background-color: #e0e0e0; border: 1px solid #e0e0e0; border-radius: 25px; color: #1d1d1d; font-size: 16px; font-weight: 400; width: 235px; cursor: pointer; transition: all 0.3s; margin-bottom: 20px; }
.account_edit_btn:hover { background-color: transparent; border: 1px solid #e0e0e0; color: #e0e0e0; }
.account_edit_btn:hover img {
        filter: brightness(0) saturate(100%) invert(88%) sepia(0%) saturate(0%) hue-rotate(0deg) brightness(150%) contrast(88%);
    }
.account_picture_card {
    position: relative;
    overflow: hidden;
    z-index: 2;
}
.account_picture_card:hover {
    z-index: 12;
}
.account_auction_retry_box {
    position: absolute;
    left: 5px;
    right: 5px;
    bottom: 5px;
    padding: 18px 18px 16px;
    border-radius: 12px;
    background: rgba(18, 18, 18, 0.92);
    border: 1px solid rgba(255, 90, 90, 0.2);
    backdrop-filter: blur(10px);
    z-index: 3;
    overflow: visible;
}
.account_auction_retry_box strong {
    color: #F3F3F3;
    font-size: 18px;
}
.account_auction_retry_head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 12px;
}
.account_retry_info {
    position: relative;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    border: 1px solid rgba(255,255,255,0.24);
    color: #F3F3F3;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    line-height: 1;
    font-weight: 600;
    cursor: default;
    flex-shrink: 0;
    padding: 0;
    text-indent: 0;
    overflow: visible;
}
.account_retry_tooltip {
    position: absolute;
    right: -20px;
    bottom:45px;
    width: 220px;
    max-width: min(220px, calc(100vw - 32px));
    padding: 12px 14px;
    border-radius: 12px;
    background: rgba(10, 10, 10, 0.55);
    color: #EAEAEA;
    font-size: 13px;
    line-height: 1.45;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: 0.2s ease;
    text-align: left;
    z-index: 40;
    display: block;
}

.account_retry_info:hover .account_retry_tooltip {
    opacity: 1;
    visibility: visible;
}
.account_auction_retry_text {
    display: block;
    color: #BDBDBD;
    margin-bottom: 14px;
}
.account_auction_retry_actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}
.account_retry_btn {
    min-height: 42px;
    padding: 0 18px;
    border-radius: 12px;
    border: none;
    background: #FBFF83;
    color: #121212;
    cursor: pointer;
    font-weight: 600;
}
.account_retry_btn_secondary {
    background: transparent;
    color: #E0E0E0;
    border: 1px solid rgba(255,255,255,0.16);
}
</style>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\canvas-laravel01\resources\views/account.blade.php ENDPATH**/ ?>