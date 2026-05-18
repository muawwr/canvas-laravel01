<button
    class="theme-toggle"
    type="button"
    data-theme-toggle
    aria-label="Toggle theme"
    aria-pressed="false"
>
    <span class="theme-toggle__icon theme-toggle__icon--sun" aria-hidden="true"><img
                        src="<?php echo e(asset('assets/images/theme/moon.svg')); ?>"
                        data-theme-image
                        data-dark-src="<?php echo e(asset('assets/images/theme/moon.svg')); ?>"
                        data-light-src="<?php echo e(asset('assets/images/theme/moon-light.svg')); ?>"
                        alt=""
                    ></span>
    <span class="theme-toggle__track" aria-hidden="true">
        <span class="theme-toggle__thumb"></span>
    </span>
    <span class="theme-toggle__icon theme-toggle__icon--moon" aria-hidden="true"><img
                        src="<?php echo e(asset('assets/images/theme/sun.svg')); ?>"
                        data-theme-image
                        data-dark-src="<?php echo e(asset('assets/images/theme/sun.svg')); ?>"
                        data-light-src="<?php echo e(asset('assets/images/theme/sun-light.svg')); ?>"
                        alt=""
                    ></span>
    <span class="theme-toggle__label" data-theme-toggle-label>Light theme</span>
</button><?php /**PATH C:\OSPanel\domains\canvas-laravel01\resources\views/partials/theme-toggle.blade.php ENDPATH**/ ?>