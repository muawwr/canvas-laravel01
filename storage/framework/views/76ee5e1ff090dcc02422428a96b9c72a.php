<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo $__env->make('partials.theme-head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <title>Авторизация - Канвас</title>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.css')); ?>">
    <link rel="shortcut icon" href="<?php echo e(asset('assets/images/header/logo.svg')); ?>" type="image/x-icon">
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
    <?php ($authImage = old('auth_random_image', $randomImage ?? 'assets/images/auth/default.jpg')); ?>

    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="<?php echo e(url('/')); ?>" class="logo">
                    <img src="<?php echo e(asset('assets/images/header/logo.svg')); ?>" alt="Канвас" class="logo-icon">
                </a>

                <nav class="navigation">
                    <a href="<?php echo e(url('/')); ?>" class="nav-item">
                        <img src="<?php echo e(asset('assets/images/header/home.svg')); ?>" alt="Главная">
                    </a>
                    <a href="<?php echo e(url('/gallery')); ?>" class="nav-item">
                        <img src="<?php echo e(asset('assets/images/header/gallery.svg')); ?>" alt="Галерея">
                    </a>
                    <a href="<?php echo e(url('/auction')); ?>" class="nav-item">
                        <img src="<?php echo e(asset('assets/images/header/auction.svg')); ?>" alt="Аукцион">
                    </a>
                    <div class="nav-item profile-toggle" id="profileToggle">
                        <img src="<?php echo e(asset('assets/images/header/user.svg')); ?>" alt="Профиль">
                    </div>
                </nav>

                <div class="auth-buttons">
                    <a href="<?php echo e(url('/auth')); ?>" class="btn btn-login">Войти</a>
                    <a href="<?php echo e(url('/reg')); ?>" class="btn btn-register">Регистрация</a>
                </div>
            </div>
        </div>
    </header>

    <div class="reg">
        <div class="reg_form">
            <form action="<?php echo e(url('/auth')); ?>" method="POST" novalidate>
                <?php echo csrf_field(); ?>
                <input type="hidden" name="auth_random_image" value="<?php echo e($authImage); ?>">

                <div class="form_title">
                    <div class="form_logo">
                        <img src="<?php echo e(asset('assets/images/header/logo.svg')); ?>" alt="Канвас">
                    </div>
                    <h3>С возвращением!</h3>
                </div>

                <?php if(session('success')): ?>
                    <div class="general-error" style="background-color: #4caf50;">
                        <?php echo e(session('success')); ?>

                    </div>
                <?php endif; ?>

                <?php if($errors->has('general')): ?>
                    <div class="general-error">
                        <?php echo e($errors->first('general')); ?>

                    </div>
                <?php endif; ?>

                <div class="form_inputs">
                    <div class="input_group <?php echo e($errors->has('email') ? 'error' : ''); ?>">
                        <div class="input_icon">
                            <img src="<?php echo e(asset('assets/images/reg/Mail.svg')); ?>" alt="Email">
                        </div>
                        <input type="email" name="email" placeholder="Электронная почта" value="<?php echo e(old('email')); ?>" autocomplete="off">
                    </div>
                    <?php if($errors->has('email')): ?>
                        <span class="error-message"><?php echo e($errors->first('email')); ?></span>
                    <?php endif; ?>

                    <div class="input_group <?php echo e($errors->has('password') ? 'error' : ''); ?>">
                        <div class="input_icon">
                            <img src="<?php echo e(asset('assets/images/reg/Lock.svg')); ?>" alt="Пароль">
                        </div>
                        <input type="password" name="password" placeholder="Пароль" autocomplete="current-password">
                    </div>
                    <?php if($errors->has('password')): ?>
                        <span class="error-message"><?php echo e($errors->first('password')); ?></span>
                    <?php endif; ?>
                </div>

                <button type="submit" class="submit_btn">
                    <span>Авторизоваться</span>
                    <img src="<?php echo e(asset('assets/images/reg/Right.svg')); ?>" alt="Далее">
                </button>

                <div class="form_footer">
                    <p>Нет аккаунта? <a href="<?php echo e(url('/reg')); ?>">Регистрация</a></p>
                </div>
            </form>
        </div>

        <div class="reg_image">
            <img src="<?php echo e(asset($authImage)); ?>" alt="Арт">
        </div>
    </div>
    <?php echo $__env->make('partials.theme-toggle', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script src="<?php echo e(asset('script.js')); ?>"></script>
</body>
</html>
<?php /**PATH C:\OSPanel\domains\canvas-laravel01\resources\views\auth\login.blade.php ENDPATH**/ ?>