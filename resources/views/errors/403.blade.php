<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Доступ запрещён - Канвас</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/header/logo.svg') }}" type="image/x-icon">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="{{ url('/') }}" class="logo">
                    <img src="{{ asset('assets/images/header/logo.svg') }}" alt="Канвас" class="logo-icon">
                </a>
            </div>
        </div>
    </header>

    <main style="min-height: 60vh; display: flex; align-items: center; justify-content: center;">
        <div style="text-align: center; padding: 60px 20px;">
            <h1 style="font-size: 120px; font-weight: 200; color: #FBFF83; margin-bottom: 10px;">403</h1>
            <h2 style="font-size: 28px; color: #E0E0E0; font-weight: 400; margin-bottom: 15px;">Доступ запрещён</h2>
            <p style="color: #939393; font-size: 16px; margin-bottom: 40px;">У вас нет прав для доступа к этой странице</p>
            <a href="{{ url('/') }}" class="hero_btn" style="display:inline-block; padding:15px 30px; background:#FBFF83; color:#1A1A1A; border-radius:12px; text-decoration:none;">На главную</a>
        </div>
    </main>
</body>
</html>
