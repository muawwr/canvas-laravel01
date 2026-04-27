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
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="{{ url('/') }}" class="logo">
                    <img src="{{ asset('assets/images/header/logo.svg') }}" alt="Канвас" class="logo-icon">
                </a>
                
                <nav class="navigation">
                    <a href="{{ url('/') }}" class="nav-item @yield('nav-home-active')">
                        <img src="{{ asset('assets/images/header/home.svg') }}" alt="Главная">
                    </a>
                    <a href="{{ url('/gallery') }}" class="nav-item @yield('nav-gallery-active')">
                        <img src="{{ asset('assets/images/header/gallery.svg') }}" alt="Галерея">
                    </a>
                    <div class="nav-item profile-toggle @yield('nav-profile-active')" id="profileToggle">
                        @if(session()->has('user_id'))
                            <img width="40" height="40" src="{{ asset(session('user_img', 'assets/images/account/mainUser.png')) }}" 
                                 alt="{{ session('user_name') }}" 
                                 class="profile-avatar">
                        @else
                            <img src="{{ asset('assets/images/header/user.svg') }}" alt="Профиль">
                        @endif
                    </div>
                </nav>
                @if(session()->has('user_id'))
                <!-- Profile Dropdown Panel -->
                <div class="profile-dropdown" id="profileDropdown">
                    @if(session('user_role') == 2)
                        <!-- Меню для администратора -->
                        <a href="{{ url('/admin') }}" class="profile-dropdown-item">
                            <img class="p_d_a" src="{{ asset('assets/images/admin/admin.svg') }}" alt="Админ-панель">
                        </a>
                        <a href="{{ url('/logout') }}" class="profile-dropdown-item">
                            <img src="{{ asset('assets/images/header/Logout.svg') }}" alt="Выход">
                        </a>
                    @else
                        <!-- Меню для обычного пользователя -->
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
    </header>

    <!-- Mobile Header -->
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

    <!-- Mobile Sidebar Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <!-- Mobile Sidebar Menu -->
    <div class="mobile-sidebar" id="mobileSidebar">
        <div class="mobile-sidebar-content">
            <a href="{{ url('/') }}" class="mobile-menu-item @yield('nav-home-active')">
                <img src="{{ asset('assets/images/header/home.svg') }}" alt="Главная">
            </a>
            <a href="{{ url('/gallery') }}" class="mobile-menu-item @yield('nav-gallery-active')">
                <img src="{{ asset('assets/images/header/gallery.svg') }}" alt="Галерея">
            </a>
            <div class="mobile-menu-item mobile-profile-toggle" id="mobileProfileToggle">
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
    <!-- Mobile Profile Dropdown Panel -->
    <div class="mobile-profile-dropdown" id="mobileProfileDropdown">
        @if(session('user_role') == 2)
            <!-- Меню для администратора -->
            <a href="{{ url('/admin') }}" class="mobile-profile-dropdown-item">
                <img class="p_d_a" src="{{ asset('assets/images/admin/admin.svg') }}" alt="Админ-панель">
            </a>
            <a href="{{ url('/logout') }}" class="mobile-profile-dropdown-item">
                <img src="{{ asset('assets/images/header/Logout.svg') }}" alt="Выход">
            </a>
        @else
            <!-- Меню для обычного пользователя -->
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
