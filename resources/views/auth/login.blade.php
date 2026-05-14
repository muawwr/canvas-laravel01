<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.theme-head')
    <title>Авторизация - Канвас</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/header/logo.svg') }}" type="image/x-icon">
    <style>
        .error-message {
            color: #c76060;
            font-size: 13px;
            display: block;
        }

        .general-error {
            background-color: #c76060;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .input_group.error input {
            border-color: #c76060;
        }
    </style>
</head>
<body>
    @php($authImage = old('auth_random_image', $randomImage ?? 'assets/images/auth/default.jpg'))

    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="{{ url('/') }}" class="logo">
                    <img src="{{ asset('assets/images/header/logo.svg') }}" alt="Канвас" class="logo-icon">
                </a>

                <nav class="navigation">
                    <a href="{{ url('/') }}" class="nav-item">
                        <img src="{{ asset('assets/images/header/home.svg') }}" alt="Главная">
                    </a>
                    <a href="{{ url('/gallery') }}" class="nav-item">
                        <img src="{{ asset('assets/images/header/gallery.svg') }}" alt="Галерея">
                    </a>
                    <a href="{{ url('/auction') }}" class="nav-item">
                        <img src="{{ asset('assets/images/header/auction.svg') }}" alt="Аукцион">
                    </a>
                    <div class="nav-item profile-toggle" id="profileToggle">
                        <img src="{{ asset('assets/images/header/user.svg') }}" alt="Профиль">
                    </div>
                </nav>

                <div class="auth-buttons">
                    <a href="{{ url('/auth') }}" class="btn btn-login">Войти</a>
                    <a href="{{ url('/reg') }}" class="btn btn-register">Регистрация</a>
                </div>
            </div>
        </div>
    </header>

    <div class="reg">
        <div class="reg_form">
            <form action="{{ url('/auth') }}" method="POST" novalidate>
                @csrf
                <input type="hidden" name="auth_random_image" value="{{ $authImage }}">

                <div class="form_title">
                    <div class="form_logo">
                        <img src="{{ asset('assets/images/header/logo.svg') }}" alt="Канвас">
                    </div>
                    <h3>С возвращением!</h3>
                </div>

                @if(session('success'))
                    <div class="general-error" style="background-color: #4caf50;">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->has('general'))
                    <div class="general-error">
                        {{ $errors->first('general') }}
                    </div>
                @endif

                <div class="form_inputs">
                    <div class="input_group {{ $errors->has('email') ? 'error' : '' }}">
                        <div class="input_icon">
                            <img src="{{ asset('assets/images/reg/Mail.svg') }}" alt="Email">
                        </div>
                        <input type="email" name="email" placeholder="Электронная почта" value="{{ old('email') }}" autocomplete="off">
                    </div>
                    @if($errors->has('email'))
                        <span class="error-message">{{ $errors->first('email') }}</span>
                    @endif

                    <div class="input_group {{ $errors->has('password') ? 'error' : '' }}">
                        <div class="input_icon">
                            <img src="{{ asset('assets/images/reg/Lock.svg') }}" alt="Пароль">
                        </div>
                        <input type="password" name="password" placeholder="Пароль" autocomplete="current-password">
                    </div>
                    @if($errors->has('password'))
                        <span class="error-message">{{ $errors->first('password') }}</span>
                    @endif
                </div>

                <button type="submit" class="submit_btn">
                    <span>Авторизоваться</span>
                    <img src="{{ asset('assets/images/reg/Right.svg') }}" alt="Далее">
                </button>

                <div class="form_footer">
                    <p>Нет аккаунта? <a href="{{ url('/reg') }}">Регистрация</a></p>
                </div>
            </form>
        </div>

        <div class="reg_image">
            <img src="{{ asset($authImage) }}" alt="Арт">
        </div>
    </div>
    @include('partials.theme-toggle')
    <script src="{{ asset('script.js') }}"></script>
</body>
</html>
