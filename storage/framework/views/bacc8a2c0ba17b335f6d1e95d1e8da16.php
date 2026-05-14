<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo $__env->make('partials.theme-head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <title>Канвас - Платформа для искусства</title>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.css')); ?>">
    <link rel="shortcut icon" href="<?php echo e(asset('assets/images/header/logo.svg')); ?>" type="image/x-icon">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <?php echo $__env->yieldContent('head'); ?>
</head>

<body>
    <?php
        $hasNotificationsTable = false;
        try {
            $hasNotificationsTable = \Illuminate\Support\Facades\Schema::hasTable('user_notifications');
        } catch (\Throwable $e) {
            $hasNotificationsTable = false;
        }

        $notificationCount = session()->has('user_id') && $hasNotificationsTable
            ? \App\Models\UserNotification::where('user_id', session('user_id'))->whereNull('read_at')->count()
            : 0;

        $manualHomeActive = trim((string) $__env->yieldContent('nav-home-active'));
        $manualGalleryActive = trim((string) $__env->yieldContent('nav-gallery-active'));
        $manualAuctionActive = trim((string) $__env->yieldContent('nav-auction-active'));
        $manualProfileActive = trim((string) $__env->yieldContent('nav-profile-active'));

        $isHomeActive = trim(request()->path(), '/') === '' || request()->is('main');
        $isGalleryActive = request()->is('gallery');
        $isAuctionActive = request()->is('auction');
        $isNotificationsActive = request()->is('notifications');
        $isProfileActive = request()->is(
            'admin',
            'account',
            'cart',
            'fav',
            'orders',
            'add',
            'edit/*',
            'checkout'
        ) || $manualProfileActive === 'active';
    ?>

    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="<?php echo e(url('/')); ?>" class="logo">
                    <img src="<?php echo e(asset('assets/images/header/logo.svg')); ?>" alt="Канвас" class="logo-icon">
                </a>

                <nav class="navigation">
                    <a href="<?php echo e(url('/')); ?>" class="nav-item <?php echo e($isHomeActive ? 'active' : $manualHomeActive); ?>">
                        <img src="<?php echo e(asset('assets/images/header/home.svg')); ?>" alt="Главная">
                    </a>
                    <a href="<?php echo e(url('/gallery')); ?>" class="nav-item <?php echo e($isGalleryActive ? 'active' : $manualGalleryActive); ?>">
                        <img src="<?php echo e(asset('assets/images/header/gallery.svg')); ?>" alt="Галерея">
                    </a>
                    <a href="<?php echo e(url('/auction')); ?>" class="nav-item <?php echo e($isAuctionActive ? 'active' : $manualAuctionActive); ?>">
                        <img src="<?php echo e(asset('assets/images/header/auction.svg')); ?>" alt="Аукцион">
                    </a>
                    <div class="nav-item profile-toggle <?php echo e($isProfileActive ? 'active' : ''); ?>" id="profileToggle">
                        <?php if(session()->has('user_id')): ?>
                            <img width="40" height="40" src="<?php echo e(asset(session('user_img', 'assets/images/account/mainUser.png'))); ?>"
                                 alt="<?php echo e(session('user_name')); ?>"
                                 class="profile-avatar">
                        <?php else: ?>
                            <img src="<?php echo e(asset('assets/images/header/user.svg')); ?>" alt="Профиль">
                        <?php endif; ?>
                    </div>
                </nav>

                <div class="header-right-tools">
                    <?php if(session()->has('user_id')): ?>
                        <a href="<?php echo e(url('/notifications')); ?>" class="header-notification-link <?php echo e($isNotificationsActive ? 'active' : ''); ?>">
                            <img src="<?php echo e(asset('assets/images/header/notifications.svg')); ?>" alt="Уведомления">
                            <span class="notification-dot" data-notification-dot style="<?php echo e($notificationCount > 0 ? '' : 'display:none;'); ?>"></span>
                        </a>
                    <?php endif; ?>

                    <?php if(session()->has('user_id')): ?>
                        <div class="profile-dropdown" id="profileDropdown">
                            <?php if(session('user_role') == 2): ?>
                                <a href="<?php echo e(url('/admin')); ?>" class="profile-dropdown-item">
                                    <img class="p_d_a" src="<?php echo e(asset('assets/images/admin/admin.svg')); ?>" alt="Админ-панель">
                                </a>
                                <a href="<?php echo e(url('/logout')); ?>" class="profile-dropdown-item">
                                    <img src="<?php echo e(asset('assets/images/header/Logout.svg')); ?>" alt="Выход">
                                </a>
                            <?php else: ?>
                                <a href="<?php echo e(url('/cart')); ?>" class="profile-dropdown-item">
                                    <img src="<?php echo e(asset('assets/images/header/Cart.svg')); ?>" alt="Корзина">
                                </a>
                                <a href="<?php echo e(url('/fav')); ?>" class="profile-dropdown-item">
                                    <img src="<?php echo e(asset('assets/images/header/fav.svg')); ?>" alt="Избранное">
                                </a>
                                <a href="<?php echo e(url('/account')); ?>" class="profile-dropdown-item">
                                    <img src="<?php echo e(asset('assets/images/header/account.svg')); ?>" alt="Настройки">
                                </a>
                                <a href="<?php echo e(url('/add')); ?>" class="profile-dropdown-item">
                                    <img src="<?php echo e(asset('assets/images/header/add.svg')); ?>" alt="Добавить">
                                </a>
                                <a href="<?php echo e(url('/orders')); ?>" class="profile-dropdown-item">
                                    <img src="<?php echo e(asset('assets/images/header/orders.svg')); ?>" alt="Заказы">
                                </a>
                                <a href="<?php echo e(url('/logout')); ?>" class="profile-dropdown-item">
                                    <img src="<?php echo e(asset('assets/images/header/Logout.svg')); ?>" alt="Выход">
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if(!session()->has('user_id')): ?>
                        <div class="auth-buttons">
                        <a href="<?php echo e(url('/auth')); ?>" class="btn btn-login">Войти</a>
                        <a href="<?php echo e(url('/reg')); ?>" class="btn btn-register">Регистрация</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <header class="mobile-header">
        <div class="mobile-header-content">
            <a href="<?php echo e(url('/')); ?>" class="mobile-logo">
                <img src="<?php echo e(asset('assets/images/header/logo.svg')); ?>" alt="Канвас">
            </a>
            <button class="burger-menu" id="burgerMenu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </header>

    <div class="mobile-overlay" id="mobileOverlay"></div>

    <div class="mobile-sidebar" id="mobileSidebar">
        <div class="mobile-sidebar-content">
            <a href="<?php echo e(url('/')); ?>" class="mobile-menu-item <?php echo e($isHomeActive ? 'active' : $manualHomeActive); ?>">
                <img src="<?php echo e(asset('assets/images/header/home.svg')); ?>" alt="Главная">
            </a>
            <a href="<?php echo e(url('/gallery')); ?>" class="mobile-menu-item <?php echo e($isGalleryActive ? 'active' : $manualGalleryActive); ?>">
                <img src="<?php echo e(asset('assets/images/header/gallery.svg')); ?>" alt="Галерея">
            </a>
            <a href="<?php echo e(url('/auction')); ?>" class="mobile-menu-item <?php echo e($isAuctionActive ? 'active' : $manualAuctionActive); ?>">
                <img src="<?php echo e(asset('assets/images/header/auction.svg')); ?>" alt="Аукцион">
            </a>
            <?php if(session()->has('user_id')): ?>
                <a href="<?php echo e(url('/notifications')); ?>" class="mobile-menu-item notification-nav-item <?php echo e($isNotificationsActive ? 'active' : ''); ?>">
                    <img src="<?php echo e(asset('assets/images/header/notifications.svg')); ?>" alt="Уведомления">
                    <span class="notification-dot" data-notification-dot style="<?php echo e($notificationCount > 0 ? '' : 'display:none;'); ?>"></span>
                </a>
            <?php endif; ?>
            <div class="mobile-menu-item mobile-profile-toggle <?php echo e($isProfileActive ? 'active' : ''); ?>" id="mobileProfileToggle">
                <?php if(session()->has('user_id')): ?>
                    <img src="<?php echo e(asset(session('user_img', 'assets/images/account/mainUser.png'))); ?>"
                         alt="<?php echo e(session('user_name')); ?>"
                         class="profile-avatar">
                <?php else: ?>
                    <img src="<?php echo e(asset('assets/images/header/user.svg')); ?>" alt="Профиль">
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if(session()->has('user_id')): ?>
        <div class="mobile-profile-dropdown" id="mobileProfileDropdown">
            <?php if(session('user_role') == 2): ?>
                <a href="<?php echo e(url('/admin')); ?>" class="mobile-profile-dropdown-item">
                    <img class="p_d_a" src="<?php echo e(asset('assets/images/admin/admin.svg')); ?>" alt="Админ-панель">
                </a>
                <a href="<?php echo e(url('/logout')); ?>" class="mobile-profile-dropdown-item">
                    <img src="<?php echo e(asset('assets/images/header/Logout.svg')); ?>" alt="Выход">
                </a>
            <?php else: ?>
                <a href="<?php echo e(url('/cart')); ?>" class="mobile-profile-dropdown-item">
                    <img src="<?php echo e(asset('assets/images/header/Cart.svg')); ?>" alt="Корзина">
                </a>
                <a href="<?php echo e(url('/fav')); ?>" class="mobile-profile-dropdown-item">
                    <img src="<?php echo e(asset('assets/images/header/fav.svg')); ?>" alt="Избранное">
                </a>
                <a href="<?php echo e(url('/account')); ?>" class="mobile-profile-dropdown-item">
                    <img src="<?php echo e(asset('assets/images/header/account.svg')); ?>" alt="Настройки">
                </a>
                <a href="<?php echo e(url('/add')); ?>" class="mobile-profile-dropdown-item">
                    <img src="<?php echo e(asset('assets/images/header/add.svg')); ?>" alt="Добавить">
                </a>
                <a href="<?php echo e(url('/orders')); ?>" class="mobile-profile-dropdown-item">
                    <img src="<?php echo e(asset('assets/images/header/orders.svg')); ?>" alt="Заказы">
                </a>
                <a href="<?php echo e(url('/logout')); ?>" class="mobile-profile-dropdown-item">
                    <img src="<?php echo e(asset('assets/images/header/Logout.svg')); ?>" alt="Выход">
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php echo $__env->yieldContent('content'); ?>
    <?php echo $__env->make('partials.theme-toggle', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <footer class="footer">
        <div class="container">
          <div class="footer-logo">
              <img src="<?php echo e(asset('assets/images/footer/logo.svg')); ?>" alt="Канвас" class="footer-logo-icon">
              <span class="footer-logo-text">Канвас</span>
          </div>
            <div class="footer-content">
                <div class="footer-column">
                    <h4 class="footer-title">Пользователь</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo e(url('/auth')); ?>">Авторизация</a></li>
                        <li><a href="<?php echo e(url('/account')); ?>">Личный кабинет</a></li>
                        <li><a href="<?php echo e(url('/cart')); ?>">Корзина</a></li>
                        <li><a href="<?php echo e(url('/fav')); ?>">Избранное</a></li>
                        <li><a href="<?php echo e(url('/account')); ?>">Настройки</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h4 class="footer-title">Галерея</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo e(url('/gallery')); ?>">Галерея</a></li>
                        <li><a href="<?php echo e(url('/auction')); ?>">Аукцион</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <p class="footer-year">2025</p>
                    <p class="footer-email">info@kanvas.ru</p>
                    <a href="#" class="footer-link">Политика конфиденциальности</a>
                    <div class="social-links">
                        <a href="#" class="social-link">
                            <img src="<?php echo e(asset('assets/images/footer/tg.svg')); ?>" alt="Telegram">
                        </a>
                        <a href="#" class="social-link">
                            <img src="<?php echo e(asset('assets/images/footer/vk.svg')); ?>" alt="VKontakte">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="<?php echo e(asset('script.js')); ?>"></script>
    <?php echo $__env->yieldContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\OSPanel\domains\canvas-laravel01\resources\views/layouts/app.blade.php ENDPATH**/ ?>