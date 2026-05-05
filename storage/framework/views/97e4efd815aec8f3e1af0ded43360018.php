
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
        <div class="gallery-card">
            <a href="<?php echo e(url('/picture/' . $picture->id)); ?>">
                <img src="<?php echo e(asset($picture->img)); ?>" alt="<?php echo e($picture->name); ?>">
                <?php if($picture->is_sold > 0): ?>
                    <div class="sold-badge">
                        <img src="<?php echo e(asset('assets/images/gallery/sold.svg')); ?>" alt="Продано">
                        <span>ПРОДАНО</span>
                    </div>
                <?php endif; ?>
            </a>
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
</style>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\canvas-laravel01\resources\views\account.blade.php ENDPATH**/ ?>