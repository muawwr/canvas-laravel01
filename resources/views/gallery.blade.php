<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Галерея - Канвас</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/header/logo.svg') }}" type="image/x-icon">
</head>
<body>
    @php
        $galleryHasNotificationsTable = false;
        try {
            $galleryHasNotificationsTable = \Illuminate\Support\Facades\Schema::hasTable('user_notifications');
        } catch (\Throwable $e) {
            $galleryHasNotificationsTable = false;
        }

        $galleryNotificationCount = session()->has('user_id') && $galleryHasNotificationsTable
            ? \App\Models\UserNotification::where('user_id', session('user_id'))->whereNull('read_at')->count()
            : 0;
    @endphp
    <header class="header-gallery">
        <div class="container">
            <div class="header-gallery-content">
                <a href="{{ url('/') }}" class="logo-gallery">
                    <img src="{{ asset('assets/images/header/logo.svg') }}" alt="Канвас" class="logo-icon">
                </a>
                <div class="header-gallery-nav">
                    <a href="{{ url('/') }}" class="link_main">
                        <img src="{{ asset('assets/images/headerNew/home.svg') }}" alt="Главная">
                    </a>
                    <a href="{{ url('/auction') }}" class="link_main link_auction">
                        <img src="{{ asset('assets/images/headerNew/auction.svg') }}" alt="Аукцион">
                    </a>
                    @if(session()->has('user_id'))
                        <a href="{{ url('/notifications') }}" class="link_notification_gallery">
                            <img src="{{ asset('assets/images/header/notifications.svg') }}" alt="Уведомления">
                            <span class="notification-dot" style="{{ $galleryNotificationCount > 0 ? '' : 'display:none;' }}"></span>
                        </a>
                    @endif

                    <div class="search-bar search-bar-collapsed" id="gallerySearchBar">
                        <button type="button" class="search-toggle-btn" id="gallerySearchToggle" aria-label="Открыть поиск" aria-expanded="false">
                            <img src="{{ asset('assets/images/headerNew/Search.svg') }}" alt="Поиск" class="search-icon-right">
                        </button>
                        <input type="text" id="searchInput" placeholder="Поиск">
                    </div>
                </div>
                
                <div class="header-actions">
                    <a href="{{ url('/cart') }}" class="icon-btn">
                        <img src="{{ asset('assets/images/headerNew/Cart.svg') }}" alt="Корзина">
                    </a>
                    <a href="{{ url('/fav') }}" class="icon-btn">
                        <img src="{{ asset('assets/images/headerNew/Fav.svg') }}" alt="Избранное">
                    </a>
                </div>
                
                <!-- Profile Button with Dropdown -->
                <div class="profile-wrapper-gallery">
                    <div class="icon-btn profile-btn profile-toggle-gallery" id="profileToggleGallery">
                        @if(session()->has('user_id'))
                            <img width="40" height="40" src="{{ asset(session('user_img', 'assets/images/account/mainUser.png')) }}" 
                                 alt="{{ session('user_name') }}" 
                                 class="profile-avatar">
                        @else
                            <img src="{{ asset('assets/images/header/user.svg') }}" alt="Профиль">
                        @endif
                    </div>
                    @if(session()->has('user_id'))
                    <!-- Profile Dropdown Panel for Gallery -->
                    <div class="profile-dropdown-gallery" id="profileDropdownGallery">
                        @if(session('user_role') == 2)
                            <a href="{{ url('/admin') }}" class="profile-dropdown-item">
                                <img src="{{ asset('assets/images/admin/admin.svg') }}" alt="Админ-панель">
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
                </div>
            </div>
            <div class="h_line"></div>
        </div>
    </header>

    <main class="gallery-main">
        <div class="container">
            <div class="gallery-controls">
                <button class="filter-btn" id="filterToggle">
                    <img src="{{ asset('assets/images/gallery/Filter.svg') }}" alt="Фильтр">
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
                            @foreach($genres as $genre)
                            <label class="filter-option">
                                <input type="checkbox" class="filter-checkbox" data-filter-type="genre" data-filter-id="{{ $genre->id }}" style="display: none;">
                                <span>{{ $genre->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="filter-section">
                        <h3 class="filter-title"><span>II</span> Стиль</h3>
                        <div class="filter-title_line"></div>
                        <div class="filter-options filter-columns">
                            @php 
                            $half = ceil($styles->count() / 2);
                            $column1 = $styles->slice(0, $half);
                            $column2 = $styles->slice($half);
                            @endphp
                            <div class="filter-column">
                                @foreach($column1 as $style)
                                <label class="filter-option">
                                    <input type="checkbox" class="filter-checkbox" data-filter-type="style" data-filter-id="{{ $style->id }}" style="display: none;">
                                    <span>{{ $style->name }}</span>
                                </label>
                                @endforeach
                            </div>
                            <div class="filter-column">
                                @foreach($column2 as $style)
                                <label class="filter-option">
                                    <input type="checkbox" class="filter-checkbox" data-filter-type="style" data-filter-id="{{ $style->id }}" style="display: none;">
                                    <span>{{ $style->name }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="filter-section">
                        <h3 class="filter-title"><span>III</span> Эпоха</h3>
                        <div class="filter-title_line"></div>
                        <div class="filter-options">
                            @foreach($eras as $era)
                            <label class="filter-option">
                                <input type="checkbox" class="filter-checkbox" data-filter-type="era" data-filter-id="{{ $era->id }}" style="display: none;">
                                <span>{{ $era->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="filter-section">
                        <h3 class="filter-title"><img src="{{ asset('assets/images/gallery/Burger.svg') }}" alt="Сортировать по"> Сортировать по</h3>
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
                        <img src="{{ asset('assets/images/gallery/Filter.svg') }}" alt="Сбросить" style="transform: rotate(180deg);">
                        <span>Сбросить все</span>
                    </button>
                </div>

                <button class="expand-btn" id="expandBtn">
                    <img src="{{ asset('assets/images/gallery/Collapse.svg') }}" alt="">
                </button>
            </div>

            <div class="gallery-grid-masonry" id="galleryGrid">
                @if(count($pictures) > 0)
                    @foreach($pictures as $picture)
                    <div class="gallery-card" 
                         data-genre-id="{{ $picture->genre_id }}"
                         data-style-id="{{ $picture->style_id }}"
                         data-era-id="{{ $picture->era_id }}"
                         data-name="{{ mb_strtolower($picture->name) }}"
                         data-author="{{ mb_strtolower($picture->user->name ?? '') }}"
                         data-price="{{ $picture->price }}"
                         data-likes="{{ $picture->favorite_entries_count ?? 0 }}"
                         data-created="{{ strtotime($picture->created_at) }}">
                        <a href="{{ url('/picture/' . $picture->id) }}">
                            <img src="{{ asset($picture->img) }}" alt="{{ $picture->name }}">
                            @if($picture->is_sold > 0)
                                <div class="sold-badge">
                                    <img src="{{ asset('assets/images/gallery/sold.svg') }}" alt="Продано">
                                    <span>ПРОДАНО</span>
                                </div>
                            @endif
                        </a>
                        <div class="card-author">
                            <img src="{{ asset($picture->user->img ?? 'assets/images/account/mainUser.png') }}" 
                                 alt="{{ $picture->user->name ?? 'Автор' }}" 
                                 class="author-avatar">
                            <a href="{{ url('/account?user_id=' . $picture->user_id) }}" style="color: inherit; text-decoration: none;">
                                <span>{{ $picture->user->name ?? 'Неизвестный автор' }}</span>
                            </a>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="no-results" style="text-align: center; color: #999; padding: 60px;">
                        Нет одобренных картин
                    </div>
                @endif
            </div>

            @if($pictures->hasPages())
                @php
                    $currentPage = $pictures->currentPage();
                    $lastPage = $pictures->lastPage();
                    $pageStart = max(1, $currentPage - 2);
                    $pageEnd = min($lastPage, $currentPage + 2);
                @endphp

                <nav class="gallery_pagination" aria-label="Пагинация каталога">
                    @if($pictures->onFirstPage())
                        <span class="gallery_page_link gallery_page_link_disabled"><img src="{{ asset('assets/images/gallery/left.svg') }}" alt=""></span>
                    @else
                        <a class="gallery_page_link" href="{{ $pictures->previousPageUrl() }}"><img src="{{ asset('assets/images/gallery/left.svg') }}" alt=""></a>
                    @endif

                    @if($pageStart > 1)
                        <a class="gallery_page_number" href="{{ $pictures->url(1) }}">1</a>
                        @if($pageStart > 2)
                            <span class="gallery_page_gap">...</span>
                        @endif
                    @endif

                    @for($page = $pageStart; $page <= $pageEnd; $page++)
                        @if($page === $currentPage)
                            <span class="gallery_page_number gallery_page_number_active">{{ $page }}</span>
                        @else
                            <a class="gallery_page_number" href="{{ $pictures->url($page) }}">{{ $page }}</a>
                        @endif
                    @endfor

                    @if($pageEnd < $lastPage)
                        @if($pageEnd < $lastPage - 1)
                            <span class="gallery_page_gap">...</span>
                        @endif
                        <a class="gallery_page_number" href="{{ $pictures->url($lastPage) }}">{{ $lastPage }}</a>
                    @endif

                    @if($pictures->hasMorePages())
                        <a class="gallery_page_link" href="{{ $pictures->nextPageUrl() }}"><img src="{{ asset('assets/images/gallery/right.svg') }}" alt=""></a>
                    @else
                        <span class="gallery_page_link gallery_page_link_disabled"><img src="{{ asset('assets/images/gallery/right.svg') }}" alt=""></span>
                    @endif
                </nav>
            @endif
            
            <div id="noResultsMessage" style="display: none; text-align: center; color: #999; padding: 60px; width: 100%;">
                Ничего не найдено. Попробуйте изменить параметры поиска или фильтры.
            </div>

            <a href="{{ session()->has('user_id') ? url('/add') : url('/auth') }}" class="share-btn">
                <div class="plus"><img src="{{ asset('assets/images/gallery/plus.svg') }}" alt=""></div>
                <span>Поделиться творчеством</span>
            </a>
        </div>
    </main>

    <!-- Footer -->
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
                
                <div class="footer-column footer-column-mobile">
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
</body>
</html>

