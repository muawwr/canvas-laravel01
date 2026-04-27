<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - Канвас</title>
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
            <form action="<?php echo e(url('/reg')); ?>" method="POST" novalidate>
                <?php echo csrf_field(); ?>
                <input type="hidden" name="auth_random_image" value="<?php echo e($authImage); ?>">

                <div class="form_title">
                    <div class="form_logo">
                        <img src="<?php echo e(asset('assets/images/header/logo.svg')); ?>" alt="Канвас">
                    </div>
                    <h3>Добро пожаловать <br> в Канвас!</h3>
                </div>

                <?php if($errors->has('general')): ?>
                    <div class="general-error">
                        <?php echo e($errors->first('general')); ?>

                    </div>
                <?php endif; ?>

                <div class="form_inputs">
                    <div class="input_group <?php echo e($errors->has('name') ? 'error' : ''); ?>">
                        <div class="input_icon">
                            <img src="<?php echo e(asset('assets/images/reg/User.svg')); ?>" alt="Пользователь">
                        </div>
                        <input type="text" name="name" placeholder="Отображаемое имя" value="<?php echo e(old('name')); ?>" autocomplete="off">
                    </div>
                    <?php if($errors->has('name')): ?>
                        <span class="error-message"><?php echo e($errors->first('name')); ?></span>
                    <?php endif; ?>

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
                        <input type="password" name="password" placeholder="Пароль" autocomplete="new-password">
                    </div>
                    <?php if($errors->has('password')): ?>
                        <span class="error-message"><?php echo e($errors->first('password')); ?></span>
                    <?php endif; ?>

                    <div class="input_group <?php echo e($errors->has('password_confirmation') ? 'error' : ''); ?>">
                        <div class="input_icon">
                            <img src="<?php echo e(asset('assets/images/reg/Lock.svg')); ?>" alt="Подтверждение пароля">
                        </div>
                        <input type="password" name="password_confirmation" placeholder="Повтор пароля" autocomplete="new-password">
                    </div>
                    <?php if($errors->has('password_confirmation')): ?>
                        <span class="error-message"><?php echo e($errors->first('password_confirmation')); ?></span>
                    <?php endif; ?>
                </div>

                <button type="submit" class="submit_btn">
                    <span>Зарегистрироваться</span>
                    <img src="<?php echo e(asset('assets/images/reg/Right.svg')); ?>" alt="Далее">
                </button>

                <div class="form_footer">
                    <p>Есть аккаунт? <a href="<?php echo e(url('/auth')); ?>">Авторизация</a></p>
                </div>
            </form>
        </div>

        <div class="reg_image">
            <img src="<?php echo e(asset($authImage)); ?>" alt="Арт">
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\OSPanel\domains\canvas-laravel01\resources\views\auth\register.blade.php ENDPATH**/ ?>