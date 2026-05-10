

<?php $__env->startSection('content'); ?>
<main class="fav_main container">
     <div class="fav_header">
    <h1 class="fav_title">Избранное: <span class="fav_badge"><?php echo e($favorites->count()); ?></span></h1>
     </div>
    <?php if($favorites->isEmpty()): ?>
    <div class="empty-fav" style="text-align: center; padding: 60px 20px; color: #939393;">
        <img src="<?php echo e(asset('assets/images/header/fav.svg')); ?>" alt="Избранное" style="width: 80px; height: 80px; opacity: 0.3; margin-bottom: 20px;">
        <h3 style="font-size: 24px; margin-bottom: 10px;">Избранное пусто</h3>
        <p style="font-size: 16px; margin-bottom: 30px;">Добавляйте понравившиеся картины</p>
        <a href="<?php echo e(url('/gallery')); ?>" class="btn" style="display: inline-block; padding: 15px 40px; background: #FBFF83; color: #0D0D0D; text-decoration: none; border-radius: 15px; font-weight: 500;">
                    Перейти в галерею
                </a>
    </div>
    <?php else: ?>
    <div class="gallery-grid-masonry">
        <?php $__currentLoopData = $favorites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fav): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="gallery-card" data-picture-id="<?php echo e($fav->picture_id); ?>">
            <a href="<?php echo e(url('/picture/' . $fav->picture_id)); ?>">
                <img src="<?php echo e(asset($fav->picture->img)); ?>" alt="<?php echo e($fav->picture->name); ?>">
            </a>
            <button class="fav_remove_btn" onclick="removeFav(<?php echo e($fav->picture_id); ?>)">
                <img src="<?php echo e(asset('assets/images/cart/delete.svg')); ?>" alt="Удалить">
            </button>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>
</main>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<style>
.fav_remove_btn { position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.6); border: none; border-radius: 50%; width: 36px; height: 36px; cursor: pointer; display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s; }
.gallery-card:hover .fav_remove_btn { opacity: 1; }
.fav_remove_btn img { width: 16px; height: 16px; }
.gallery-card { position: relative; }
</style>
<script>
function removeFav(pictureId) {
    const formData = new FormData();
    formData.append('action', 'remove');
    formData.append('picture_id', pictureId);
    
    fetch('/api/favorites', { method: 'POST', body: formData, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const card = document.querySelector(`[data-picture-id="${pictureId}"]`);
            if (card) { card.style.opacity = '0'; setTimeout(() => card.remove(), 300); }
        }
    });
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\canvas-laravel01\resources\views/favorites.blade.php ENDPATH**/ ?>