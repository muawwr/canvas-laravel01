<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo $__env->make('partials.theme-head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <title>Галерея - Канвас</title>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.css')); ?>">
    <link rel="shortcut icon" href="<?php echo e(asset('assets/images/header/logo.svg')); ?>" type="image/x-icon">
</head>
<body>
    <?php
        $galleryHasNotificationsTable = false;
        try {
            $galleryHasNotificationsTable = \Illuminate\Support\Facades\Schema::hasTable('user_notifications');
        } catch (\Throwable $e) {
            $galleryHasNotificationsTable = false;
        }

        $galleryNotificationCount = session()->has('user_id') && $galleryHasNotificationsTable
            ? \App\Models\UserNotification::where('user_id', session('user_id'))->whereNull('read_at')->count()
            : 0;
    ?>
    <header class="header-gallery">
        <div class="container">
            <div class="header-gallery-content">
                <a href="<?php echo e(url('/')); ?>" class="logo-gallery">
                    <img src="<?php echo e(asset('assets/images/header/logo.svg')); ?>" alt="Канвас" class="logo-icon">
                </a>
                <div class="header-gallery-nav">
                    <a href="<?php echo e(url('/')); ?>" class="link_main">
                        <img src="<?php echo e(asset('assets/images/headerNew/home.svg')); ?>" alt="Главная">
                    </a>
                    <a href="<?php echo e(url('/auction')); ?>" class="link_main link_auction">
                        <img src="<?php echo e(asset('assets/images/headerNew/auction.svg')); ?>" alt="Аукцион">
                    </a>
                    <?php if(session()->has('user_id')): ?>
                        <a href="<?php echo e(url('/notifications')); ?>" class="link_notification_gallery">
                            <img src="<?php echo e(asset('assets/images/header/notifications.svg')); ?>" alt="Уведомления">
                            <span class="notification-dot" style="<?php echo e($galleryNotificationCount > 0 ? '' : 'display:none;'); ?>"></span>
                        </a>
                    <?php endif; ?>

                    <div class="search-bar search-bar-collapsed" id="gallerySearchBar">
                        <button type="button" class="search-toggle-btn" id="gallerySearchToggle" aria-label="Открыть поиск" aria-expanded="false">
                            <img src="<?php echo e(asset('assets/images/headerNew/Search.svg')); ?>" alt="Поиск" class="search-icon-right">
                        </button>
                        <input type="text" id="searchInput" placeholder="Поиск">
                    </div>
                </div>
                
                <div class="header-actions">
                    <a href="<?php echo e(url('/cart')); ?>" class="icon-btn">
                        <img src="<?php echo e(asset('assets/images/headerNew/Cart.svg')); ?>" alt="Корзина">
                    </a>
                    <a href="<?php echo e(url('/fav')); ?>" class="icon-btn">
                        <img src="<?php echo e(asset('assets/images/headerNew/Fav.svg')); ?>" alt="Избранное">
                    </a>
                </div>
                
                <!-- Profile Button with Dropdown -->
                <div class="profile-wrapper-gallery">
                    <div class="icon-btn profile-btn profile-toggle-gallery" id="profileToggleGallery">
                        <?php if(session()->has('user_id')): ?>
                            <img width="40" height="40" src="<?php echo e(asset(session('user_img', 'assets/images/account/mainUser.png'))); ?>" 
                                 alt="<?php echo e(session('user_name')); ?>" 
                                 class="profile-avatar">
                        <?php else: ?>
                            <img src="<?php echo e(asset('assets/images/header/user.svg')); ?>" alt="Профиль">
                        <?php endif; ?>
                    </div>
                    <?php if(session()->has('user_id')): ?>
                    <!-- Profile Dropdown Panel for Gallery -->
                    <div class="profile-dropdown-gallery" id="profileDropdownGallery">
                        <?php if(session('user_role') == 2): ?>
                            <a href="<?php echo e(url('/admin')); ?>" class="profile-dropdown-item">
                                <img src="<?php echo e(asset('assets/images/admin/admin.svg')); ?>" alt="Админ-панель">
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
                </div>
            </div>
            <div class="h_line"></div>
        </div>
    </header>

    <main class="gallery-main">
        <div class="container">
            <div class="gallery-controls">
                <button class="filter-btn" id="filterToggle">
                    <img src="<?php echo e(asset('assets/images/gallery/Filter.svg')); ?>" alt="Фильтр">
                    <span>Параметры отображения</span>
                </button>
            </div>

            <!-- Filter Panel -->
            <div class="filter-panel" id="filterPanel">
                <div class="filter-sections">
                    <div class="filter-section">
                        <h3 class="filter-title"><span>I</span> Жанр</h3>
                        <div class="filter-title_line"></div>
                        <div class="filter-options">
                            <label class="filter-option" data-filter-reset="genre">
                                <span>Все</span>
                            </label>
                            <?php $__currentLoopData = $genres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $genre): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="filter-option">
                                <input type="checkbox" class="filter-checkbox" data-filter-type="genre" data-filter-id="<?php echo e($genre->id); ?>" style="display: none;">
                                <span><?php echo e($genre->name); ?></span>
                            </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    <div class="filter-section">
                        <h3 class="filter-title"><span>II</span> Стиль</h3>
                        <div class="filter-title_line"></div>
                        <div class="filter-options filter-columns">
                            <?php 
                            $half = ceil($styles->count() / 2);
                            $column1 = $styles->slice(0, $half);
                            $column2 = $styles->slice($half);
                            ?>
                            <div class="filter-column">
                                <?php $__currentLoopData = $column1; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $style): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="filter-option">
                                    <input type="checkbox" class="filter-checkbox" data-filter-type="style" data-filter-id="<?php echo e($style->id); ?>" style="display: none;">
                                    <span><?php echo e($style->name); ?></span>
                                </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <div class="filter-column">
                                <?php $__currentLoopData = $column2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $style): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="filter-option">
                                    <input type="checkbox" class="filter-checkbox" data-filter-type="style" data-filter-id="<?php echo e($style->id); ?>" style="display: none;">
                                    <span><?php echo e($style->name); ?></span>
                                </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>

                    <div class="filter-section">
                        <h3 class="filter-title"><span>III</span> Эпоха</h3>
                        <div class="filter-title_line"></div>
                        <div class="filter-options">
                            <?php $__currentLoopData = $eras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $era): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="filter-option">
                                <input type="checkbox" class="filter-checkbox" data-filter-type="era" data-filter-id="<?php echo e($era->id); ?>" style="display: none;">
                                <span><?php echo e($era->name); ?></span>
                            </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    <div class="filter-section">
                        <h3 class="filter-title"><img src="<?php echo e(asset('assets/images/gallery/Burger.svg')); ?>" alt="Сортировать по"> Сортировать по</h3>
                        <div class="filter-title_line"></div>
                        <div class="filter-options back">
                            <label class="filter-option radio-option">
                                <input type="radio" name="price-sort" value="price-asc">
                                <span>Цена ↗</span>
                            </label>
                            <label class="filter-option radio-option">
                                <input type="radio" name="price-sort" value="price-desc">
                                <span>Цена ↘</span>
                            </label>
                            <label class="filter-option radio-option">
                                <input type="radio" name="additional-sort" value="popularity">
                                <span>Популярность</span>
                            </label>
                            <label class="filter-option radio-option">
                                <input type="radio" name="additional-sort" value="newest">
                                <span>Новизна</span>
                            </label>
                        </div>
                    </div>
                </div>
                <!-- Кнопка сброса всех фильтров -->
                <div class="filter-section">
                    <button class="filter-reset-all-btn" id="resetAllFiltersBtn">
                        <img src="<?php echo e(asset('assets/images/gallery/Filter.svg')); ?>" alt="Сбросить" style="transform: rotate(180deg);">
                        <span>Сбросить все</span>
                    </button>
                </div>

                <button class="expand-btn" id="expandBtn">
                    <img src="<?php echo e(asset('assets/images/gallery/Collapse.svg')); ?>" alt="">
                </button>
            </div>

            <div class="gallery-grid-masonry" id="galleryGrid">
                <?php if(count($pictures) > 0): ?>
                    <?php $__currentLoopData = $pictures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $picture): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="gallery-card" 
                         data-genre-id="<?php echo e($picture->genre_id); ?>"
                         data-style-id="<?php echo e($picture->style_id); ?>"
                         data-era-id="<?php echo e($picture->era_id); ?>"
                         data-name="<?php echo e(mb_strtolower($picture->name)); ?>"
                         data-author="<?php echo e(mb_strtolower($picture->user->name ?? '')); ?>"
                         data-price="<?php echo e($picture->price); ?>"
                         data-likes="<?php echo e($picture->favorite_entries_count ?? 0); ?>"
                         data-created="<?php echo e(strtotime($picture->created_at)); ?>">
                        <a href="<?php echo e(url('/picture/' . $picture->id)); ?>">
                            <img src="<?php echo e(asset($picture->img)); ?>" alt="<?php echo e($picture->name); ?>">
                            <?php if($picture->is_sold > 0): ?>
                                <div class="sold-badge">
                                    <img src="<?php echo e(asset('assets/images/gallery/sold.svg')); ?>" alt="Продано">
                                    <span>ПРОДАНО</span>
                                </div>
                            <?php endif; ?>
                        </a>
                        <div class="card-author">
                            <img src="<?php echo e(asset($picture->user->img ?? 'assets/images/account/mainUser.png')); ?>" 
                                 alt="<?php echo e($picture->user->name ?? 'Автор'); ?>" 
                                 class="author-avatar">
                            <a href="<?php echo e(url('/account?user_id=' . $picture->user_id)); ?>" style="color: inherit; text-decoration: none;">
                                <span><?php echo e($picture->user->name ?? 'Неизвестный автор'); ?></span>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="no-results" style="text-align: center; color: #999; padding: 60px;">
                        Нет одобренных картин
                    </div>
                <?php endif; ?>
            </div>

            <?php if($pictures->hasPages()): ?>
                <?php
                    $currentPage = $pictures->currentPage();
                    $lastPage = $pictures->lastPage();
                    $pageStart = max(1, $currentPage - 2);
                    $pageEnd = min($lastPage, $currentPage + 2);
                ?>

                <nav class="gallery_pagination" aria-label="Пагинация каталога">
                    <?php if($pictures->onFirstPage()): ?>
                        <span class="gallery_page_link gallery_page_link_disabled"><img src="<?php echo e(asset('assets/images/gallery/left.svg')); ?>" alt=""></span>
                    <?php else: ?>
                        <a class="gallery_page_link" href="<?php echo e($pictures->previousPageUrl()); ?>"><img src="<?php echo e(asset('assets/images/gallery/left.svg')); ?>" alt=""></a>
                    <?php endif; ?>

                    <?php if($pageStart > 1): ?>
                        <a class="gallery_page_number" href="<?php echo e($pictures->url(1)); ?>">1</a>
                        <?php if($pageStart > 2): ?>
                            <span class="gallery_page_gap">...</span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for($page = $pageStart; $page <= $pageEnd; $page++): ?>
                        <?php if($page === $currentPage): ?>
                            <span class="gallery_page_number gallery_page_number_active"><?php echo e($page); ?></span>
                        <?php else: ?>
                            <a class="gallery_page_number" href="<?php echo e($pictures->url($page)); ?>"><?php echo e($page); ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if($pageEnd < $lastPage): ?>
                        <?php if($pageEnd < $lastPage - 1): ?>
                            <span class="gallery_page_gap">...</span>
                        <?php endif; ?>
                        <a class="gallery_page_number" href="<?php echo e($pictures->url($lastPage)); ?>"><?php echo e($lastPage); ?></a>
                    <?php endif; ?>

                    <?php if($pictures->hasMorePages()): ?>
                        <a class="gallery_page_link" href="<?php echo e($pictures->nextPageUrl()); ?>"><img src="<?php echo e(asset('assets/images/gallery/right.svg')); ?>" alt=""></a>
                    <?php else: ?>
                        <span class="gallery_page_link gallery_page_link_disabled"><img src="<?php echo e(asset('assets/images/gallery/right.svg')); ?>" alt=""></span>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
            
            <div id="noResultsMessage" style="display: none; text-align: center; color: #999; padding: 60px; width: 100%;">
                Ничего не найдено. Попробуйте изменить параметры поиска или фильтры.
            </div>

            <a href="<?php echo e(session()->has('user_id') ? url('/add') : url('/auth')); ?>" class="share-btn">
                <div class="plus"><img src="<?php echo e(asset('assets/images/gallery/plus.svg')); ?>" alt=""></div>
                <span>Поделиться творчеством</span>
            </a>
        </div>
    </main>

    <!-- Footer -->
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
                
                <div class="footer-column footer-column-mobile">
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
    
    <script>
    // ======== ФИЛЬТРАЦИЯ И ПОИСК В ГАЛЕРЕЕ ========
    (function() {
        'use strict';
        
        const filterState = {
            genres: new Set(),
            styles: new Set(),
            eras: new Set(),
            searchQuery: '',
            priceSort: null,
            additionalSort: null
        };
        
        const searchInput = document.getElementById('searchInput');
        const searchBar = document.getElementById('gallerySearchBar');
        const searchToggle = document.getElementById('gallerySearchToggle');
        const galleryGrid = document.getElementById('galleryGrid');
        const noResultsMessage = document.getElementById('noResultsMessage');
        const filterCheckboxes = document.querySelectorAll('.filter-checkbox');
        const priceSortRadios = document.querySelectorAll('input[name="price-sort"]');
        const additionalSortRadios = document.querySelectorAll('input[name="additional-sort"]');
        const sortOptionLabels = document.querySelectorAll('.radio-option');
        const resetAllBtn = document.getElementById('resetAllFiltersBtn');
        const initialCards = galleryGrid ? Array.from(galleryGrid.querySelectorAll('.gallery-card')) : [];

        initialCards.forEach((card, index) => {
            card.dataset.initialOrder = index;
        });
        
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                filterState.searchQuery = this.value.toLowerCase().trim();
                applyFilters();
            });
        }

        if (searchToggle && searchBar && searchInput) {
            const setSearchOpen = (isOpen) => {
                searchBar.classList.toggle('search-bar-open', isOpen);
                searchBar.classList.toggle('search-bar-collapsed', !isOpen);
                searchToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

                if (isOpen) {
                    window.requestAnimationFrame(() => searchInput.focus());
                }
            };

            searchToggle.addEventListener('click', function() {
                const isOpen = !searchBar.classList.contains('search-bar-open');
                setSearchOpen(isOpen);
            });

            searchInput.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    setSearchOpen(false);
                    searchInput.blur();
                }
            });

            document.addEventListener('click', function(event) {
                if (!searchBar.contains(event.target)) {
                    setSearchOpen(false);
                }
            });
        }
        
        filterCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const filterType = this.dataset.filterType;
                const filterId = this.dataset.filterId;
                
                if (this.checked) filterState[filterType + 's'].add(filterId);
                else filterState[filterType + 's'].delete(filterId);
                
                applyFilters();
            });
        });
        
        document.querySelectorAll('[data-filter-reset="genre"]').forEach(label => {
            label.addEventListener('click', function(e) {
                e.preventDefault();
                resetGenreFilters();
            });
        });
        
        function syncSortState() {
            const checkedPriceSort = document.querySelector('input[name="price-sort"]:checked');
            const checkedAdditionalSort = document.querySelector('input[name="additional-sort"]:checked');

            filterState.priceSort = checkedPriceSort
                ? (checkedPriceSort.value === 'price-asc' ? 'asc' : 'desc')
                : null;
            filterState.additionalSort = checkedAdditionalSort ? checkedAdditionalSort.value : null;

            applyFilters();
        }

        function updateSortOptionStyles() {
            sortOptionLabels.forEach(label => {
                const input = label.querySelector('input[type="radio"]');
                if (!input) {
                    return;
                }

                label.classList.toggle('active', input.checked);
            });
        }

        priceSortRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                syncSortState();
                updateSortOptionStyles();
            });
        });

        additionalSortRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                syncSortState();
                updateSortOptionStyles();
            });
        });

        sortOptionLabels.forEach(label => {
            label.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopPropagation();

                const input = this.querySelector('input[type="radio"]');
                if (!input) {
                    return;
                }

                if (input.checked) {
                    input.checked = false;
                    syncSortState();
                    updateSortOptionStyles();
                    return;
                }

                document.querySelectorAll(`input[name="${input.name}"]`).forEach(radio => {
                    radio.checked = false;
                });

                input.checked = true;
                syncSortState();
                updateSortOptionStyles();
            }, true);
        });
        
        if (resetAllBtn) {
            resetAllBtn.addEventListener('click', function(e) {
                e.preventDefault();
                resetAllFilters();
            });
        }
        
        function resetAllFilters() {
            filterState.genres.clear();
            filterState.styles.clear();
            filterState.eras.clear();
            filterState.searchQuery = '';
            filterState.priceSort = null;
            filterState.additionalSort = null;
            
            filterCheckboxes.forEach(cb => cb.checked = false);
            priceSortRadios.forEach(radio => radio.checked = false);
            additionalSortRadios.forEach(radio => radio.checked = false);
            
            document.querySelectorAll('.filter-option').forEach(option => option.classList.remove('active'));
            if (searchInput) searchInput.value = '';

            updateSortOptionStyles();
            
            applyFilters();
        }
        
        function resetGenreFilters() {
            filterState.genres.clear();
            document.querySelectorAll('[data-filter-type="genre"]').forEach(cb => {
                cb.checked = false;
                const parentLabel = cb.closest('.filter-option');
                if (parentLabel) parentLabel.classList.remove('active');
            });
            applyFilters();
        }
        
        function applyFilters() {
            if(!galleryGrid) return;
            const cards = Array.from(galleryGrid.querySelectorAll('.gallery-card'));
            let visibleCount = 0;
            
            cards.forEach(card => {
                const genreId = card.dataset.genreId;
                const styleId = card.dataset.styleId;
                const eraId = card.dataset.eraId;
                const name = card.dataset.name || '';
                const author = card.dataset.author || '';
                
                let shouldShow = true;
                if (filterState.genres.size > 0 && !filterState.genres.has(genreId)) shouldShow = false;
                if (filterState.styles.size > 0 && !filterState.styles.has(styleId)) shouldShow = false;
                if (filterState.eras.size > 0 && !filterState.eras.has(eraId)) shouldShow = false;
                if (filterState.searchQuery) {
                    const matchesSearch = name.includes(filterState.searchQuery) || author.includes(filterState.searchQuery);
                    if (!matchesSearch) shouldShow = false;
                }
                
                if (shouldShow) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            if (filterState.priceSort || filterState.additionalSort) {
                sortCards(cards.filter(card => card.style.display !== 'none'));
            } else {
                restoreDefaultOrder(cards);
            }
            
            if (visibleCount === 0) {
                if(noResultsMessage) noResultsMessage.style.display = 'block';
                galleryGrid.style.display = 'none';
            } else {
                if(noResultsMessage) noResultsMessage.style.display = 'none';
                galleryGrid.style.display = '';
            }
        }
        
        function sortCards(visibleCards) {
            visibleCards.sort((a, b) => {
                let result = 0;
                if (filterState.additionalSort === 'popularity') {
                    result = (parseInt(b.dataset.likes) || 0) - (parseInt(a.dataset.likes) || 0);
                } else if (filterState.additionalSort === 'newest') {
                    result = (parseInt(b.dataset.created) || 0) - (parseInt(a.dataset.created) || 0);
                }
                if (result === 0 && filterState.priceSort) {
                    const priceA = parseInt(a.dataset.price) || 0;
                    const priceB = parseInt(b.dataset.price) || 0;
                    result = filterState.priceSort === 'asc' ? priceA - priceB : priceB - priceA;
                }
                return result;
            });
            visibleCards.forEach(card => galleryGrid.appendChild(card));
        }

        function restoreDefaultOrder(cards) {
            cards
                .slice()
                .sort((a, b) => (parseInt(a.dataset.initialOrder) || 0) - (parseInt(b.dataset.initialOrder) || 0))
                .forEach(card => galleryGrid.appendChild(card));
        }

        updateSortOptionStyles();
    })();
    </script>
    <?php echo $__env->make('partials.theme-toggle', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>
</html>

<?php /**PATH C:\OSPanel\domains\canvas-laravel01\resources\views/gallery.blade.php ENDPATH**/ ?>