
<?php $__env->startSection('nav-home-active', 'active'); ?>

<?php $__env->startSection('content'); ?>
<main>
    <!-- Hero Section -->
    <section class="hero section-spacing">
        <div class="hero-banner">
            <div class="hero-content container">
                <div class="hero-text">
                    <p class="hero-subtitle">Безграничное творческое <br> пространство для покупки и <br> продажи искусства</p>
                </div>
                <div class="hero-title">
                    <img src="<?php echo e(asset('assets/images/banner/canvas.svg')); ?>" alt="">
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <div class="gallery container">
        <div class="title">
            <p class="section-subtitle">веб-платформа</p>
            <h2 class="section-title">ГАЛЕРЕЯ</h2>
            <div class="line"></div>
        </div>
        
        <div class="wrap">
            <!-- верхний текстовый блок -->
            <div class="lead">
                <div>
                    <p>
                        пространство, где коллекционирование искусства <br> становится личной историей.
                        Мы объединяем мастеров <br> и тех, кто ценит изысканность, качество и смысл <br> в каждой детали.
                    </p>
                </div>
            </div>
        

                <!-- Fallback на статические изображения если нет данных в БД -->
                <figure class="tile i1"><img src="<?php echo e(asset('assets/images/mainGallery/1.png')); ?>" alt=""></figure>
                <figure class="tile i2"><img src="<?php echo e(asset('assets/images/mainGallery/2.png')); ?>" alt=""></figure>
                <figure class="tile i3"><img src="<?php echo e(asset('assets/images/mainGallery/3.png')); ?>" alt=""></figure>
                <figure class="tile i4"><img src="<?php echo e(asset('assets/images/mainGallery/5.png')); ?>" alt=""></figure>
                <figure class="tile i5"><img src="<?php echo e(asset('assets/images/mainGallery/4.png')); ?>" alt=""></figure>
                <figure class="tile i6"><img src="<?php echo e(asset('assets/images/mainGallery/6.png')); ?>" alt=""></figure>
                <figure class="tile i7"><img src="<?php echo e(asset('assets/images/mainGallery/7.png')); ?>" alt=""></figure>

        </div>
    </div>

    <!-- Artists Section -->
    <section class="artists container section-spacing">
        <div class="artists-content">
            <div class="title">
                <p class="section-subtitle">популярные</p>
                <h2 class="section-title">ХУДОЖНИКИ</h2>
                <div class="line"></div>
            </div>
            
            <section class="artists container">
                <div class="artists-carousel">
                    <div class="artists-track">
                        <?php if($topArtists->count() > 0): ?>
                            <?php $__currentLoopData = $topArtists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $artist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(url('/account?user_id=' . $artist->id)); ?>" class="artist-card">
                                    <div class="artist-avatar">
                                        <img src="<?php echo e(asset($artist->img ?? 'assets/images/account/mainUser.png')); ?>" alt="<?php echo e($artist->name); ?>">
                                    </div>
                                    <h3 class="artist-name"><?php echo e($artist->name); ?></h3>
                                    <p class="artist-sales"><?php echo e($artist->sales_count); ?> продаж</p>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <div class="artist-card" style="text-align: center; color: #999;">
                                <p>Пока нет художников с продажами</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>
    </section>
</main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\canvas-laravel01\resources\views/main.blade.php ENDPATH**/ ?>