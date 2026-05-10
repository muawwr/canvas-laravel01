<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Канвас - Платформа для искусства</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/header/logo.svg') }}" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('head')
</head>

<body>
    @php
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
    @endphp

    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="{{ url('/') }}" class="logo">
                    <img src="{{ asset('assets/images/header/logo.svg') }}" alt="Канвас" class="logo-icon">
                </a>

                <nav class="navigation">
                    <a href="{{ url('/') }}" class="nav-item {{ $isHomeActive ? 'active' : $manualHomeActive }}">
                        <img src="{{ asset('assets/images/header/home.svg') }}" alt="Главная">
                    </a>
                    <a href="{{ url('/gallery') }}" class="nav-item {{ $isGalleryActive ? 'active' : $manualGalleryActive }}">
                        <img src="{{ asset('assets/images/header/gallery.svg') }}" alt="Галерея">
                    </a>
                    <a href="{{ url('/auction') }}" class="nav-item {{ $isAuctionActive ? 'active' : $manualAuctionActive }}">
                        <img src="{{ asset('assets/images/header/auction.svg') }}" alt="Аукцион">
                    </a>
                    <div class="nav-item profile-toggle {{ $isProfileActive ? 'active' : '' }}" id="profileToggle">
                        @if(session()->has('user_id'))
                            <img width="40" height="40" src="{{ asset(session('user_img', 'assets/images/account/mainUser.png')) }}"
                                 alt="{{ session('user_name') }}"
                                 class="profile-avatar">
                        @else
                            <img src="{{ asset('assets/images/header/user.svg') }}" alt="Профиль">
                        @endif
                    </div>
                </nav>

                <div class="header-right-tools">
                    @if(session()->has('user_id'))
                        <a href="{{ url('/notifications') }}" class="header-notification-link {{ $isNotificationsActive ? 'active' : '' }}">
                            <img src="{{ asset('assets/images/header/notifications.svg') }}" alt="Уведомления">
                            <span class="notification-dot" data-notification-dot style="{{ $notificationCount > 0 ? '' : 'display:none;' }}"></span>
                        </a>
                    @endif

                    @if(session()->has('user_id'))
                        <div class="profile-dropdown" id="profileDropdown">
                            @if(session('user_role') == 2)
                                <a href="{{ url('/admin') }}" class="profile-dropdown-item">
                                    <img class="p_d_a" src="{{ asset('assets/images/admin/admin.svg') }}" alt="Админ-панель">
                                </a>
                                <a href="{{ url('/logout') }}" class="profile-dropdown-item">
                                    <img src="{{ asset('assets/images/header/Logout.svg') }}" alt="Выход">
                                </a>
                            @else
                                <a href="{{ url('/cart') }}" class="profile-dropdown-item">
                                    <img src="{{ asset('assets/images/header/Cart.svg') }}" alt="Корзина">
                                </a>
                                <a href="{{ url('/fav') }}" class="profile-dropdown-item">
                                    <img src="{{ asset('assets/images/header/fav.svg') }}" alt="Избранное">
                                </a>
                                <a href="{{ url('/account') }}" class="profile-dropdown-item">
                                    <img src="{{ asset('assets/images/header/account.svg') }}" alt="Настройки">
                                </a>
                                <a href="{{ url('/add') }}" class="profile-dropdown-item">
                                    <img src="{{ asset('assets/images/header/add.svg') }}" alt="Добавить">
                                </a>
                                <a href="{{ url('/orders') }}" class="profile-dropdown-item">
                                    <img src="{{ asset('assets/images/header/orders.svg') }}" alt="Заказы">
                                </a>
                                <a href="{{ url('/logout') }}" class="profile-dropdown-item">
                                    <img src="{{ asset('assets/images/header/Logout.svg') }}" alt="Выход">
                                </a>
                            @endif
                        </div>
                    @endif

                    @if(!session()->has('user_id'))
                        <div class="auth-buttons">
                        <a href="{{ url('/auth') }}" class="btn btn-login">Войти</a>
                        <a href="{{ url('/reg') }}" class="btn btn-register">Регистрация</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <header class="mobile-header">
        <div class="mobile-header-content">
            <a href="{{ url('/') }}" class="mobile-logo">
                <img src="{{ asset('assets/images/header/logo.svg') }}" alt="Канвас">
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
            <a href="{{ url('/') }}" class="mobile-menu-item {{ $isHomeActive ? 'active' : $manualHomeActive }}">
                <img src="{{ asset('assets/images/header/home.svg') }}" alt="Главная">
            </a>
            <a href="{{ url('/gallery') }}" class="mobile-menu-item {{ $isGalleryActive ? 'active' : $manualGalleryActive }}">
                <img src="{{ asset('assets/images/header/gallery.svg') }}" alt="Галерея">
            </a>
            <a href="{{ url('/auction') }}" class="mobile-menu-item {{ $isAuctionActive ? 'active' : $manualAuctionActive }}">
                <img src="{{ asset('assets/images/header/auction.svg') }}" alt="Аукцион">
            </a>
            @if(session()->has('user_id'))
                <a href="{{ url('/notifications') }}" class="mobile-menu-item notification-nav-item {{ $isNotificationsActive ? 'active' : '' }}">
                    <img src="{{ asset('assets/images/header/notifications.svg') }}" alt="Уведомления">
                    <span class="notification-dot" data-notification-dot style="{{ $notificationCount > 0 ? '' : 'display:none;' }}"></span>
                </a>
            @endif
            <div class="mobile-menu-item mobile-profile-toggle {{ $isProfileActive ? 'active' : '' }}" id="mobileProfileToggle">
                @if(session()->has('user_id'))
                    <img src="{{ asset(session('user_img', 'assets/images/account/mainUser.png')) }}"
                         alt="{{ session('user_name') }}"
                         class="profile-avatar">
                @else
                    <img src="{{ asset('assets/images/header/user.svg') }}" alt="Профиль">
                @endif
            </div>
        </div>
    </div>

    @if(session()->has('user_id'))
        <div class="mobile-profile-dropdown" id="mobileProfileDropdown">
            @if(session('user_role') == 2)
                <a href="{{ url('/admin') }}" class="mobile-profile-dropdown-item">
                    <img class="p_d_a" src="{{ asset('assets/images/admin/admin.svg') }}" alt="Админ-панель">
                </a>
                <a href="{{ url('/logout') }}" class="mobile-profile-dropdown-item">
                    <img src="{{ asset('assets/images/header/Logout.svg') }}" alt="Выход">
                </a>
            @else
                <a href="{{ url('/cart') }}" class="mobile-profile-dropdown-item">
                    <img src="{{ asset('assets/images/header/Cart.svg') }}" alt="Корзина">
                </a>
                <a href="{{ url('/fav') }}" class="mobile-profile-dropdown-item">
                    <img src="{{ asset('assets/images/header/fav.svg') }}" alt="Избранное">
                </a>
                <a href="{{ url('/account') }}" class="mobile-profile-dropdown-item">
                    <img src="{{ asset('assets/images/header/account.svg') }}" alt="Настройки">
                </a>
                <a href="{{ url('/add') }}" class="mobile-profile-dropdown-item">
                    <img src="{{ asset('assets/images/header/add.svg') }}" alt="Добавить">
                </a>
                <a href="{{ url('/orders') }}" class="mobile-profile-dropdown-item">
                    <img src="{{ asset('assets/images/header/orders.svg') }}" alt="Заказы">
                </a>
                <a href="{{ url('/logout') }}" class="mobile-profile-dropdown-item">
                    <img src="{{ asset('assets/images/header/Logout.svg') }}" alt="Выход">
                </a>
            @endif
        </div>
    @endif

    @yield('content')

    <footer class="footer">
        <div class="container">
          <div class="footer-logo">
              <img src="{{ asset('assets/images/footer/logo.svg') }}" alt="Канвас" class="footer-logo-icon">
              <span class="footer-logo-text">Канвас</span>
          </div>
            <div class="footer-content">
                <div class="footer-column">
                    <h4 class="footer-title">Пользователь</h4>
                    <ul class="footer-links">
                        <li><a href="{{ url('/auth') }}">Авторизация</a></li>
                        <li><a href="{{ url('/account') }}">Личный кабинет</a></li>
                        <li><a href="{{ url('/cart') }}">Корзина</a></li>
                        <li><a href="{{ url('/fav') }}">Избранное</a></li>
                        <li><a href="{{ url('/account') }}">Настройки</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h4 class="footer-title">Галерея</h4>
                    <ul class="footer-links">
                        <li><a href="{{ url('/gallery') }}">Галерея</a></li>
                        <li><a href="{{ url('/auction') }}">Аукцион</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <p class="footer-year">2025</p>
                    <p class="footer-email">info@kanvas.ru</p>
                    <a href="#" class="footer-link">Политика конфиденциальности</a>
                    <div class="social-links">
                        <a href="#" class="social-link">
                            <img src="{{ asset('assets/images/footer/tg.svg') }}" alt="Telegram">
                        </a>
                        <a href="#" class="social-link">
                            <img src="{{ asset('assets/images/footer/vk.svg') }}" alt="VKontakte">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="{{ asset('script.js') }}"></script>
    @yield('scripts')
</body>
</html>
