@extends('layouts.app')

@section('content')
<main class="admin_main container">

    <div class="admin_tabs">
        <button class="admin_tab admin_tab_active" type="button" data-tab="processing">
            На обработку
            <span class="admin_tab_badge">{{ $pending_pictures->count() }}</span>
        </button>
        <button class="admin_tab" type="button" data-tab="deals">
            Сделки
            <span class="admin_tab_badge">{{ $deals->count() }}</span>
        </button>
        <button class="admin_tab" type="button" data-tab="users">
            Пользователи
            <span class="admin_tab_badge">{{ $users->count() }}</span>
        </button>
        <button class="admin_tab" type="button" data-tab="categories">Категории</button>
    </div>

    <div class="admin_table_wrapper" data-table="processing">
        <div class="admin_panel">
            <div class="admin_panel_header">
                <h2 class="admin_panel_title">Картины на модерации</h2>
            </div>

            <div class="admin_table_scroll">
                <table class="admin_table">
                    <thead>
                        <tr>
                            <th style="width: 100px;">ID</th>
                            <th style="width: 180px;">Дата</th>
                            <th style="width: 220px;">Автор</th>
                            <th style="width: 220px;">Название</th>
                            <th style="width: 180px;">Стоимость</th>
                            <th style="width: 120px;">Просмотр</th>
                            <th style="width: 220px;">Действия</th>
                        </tr>
                    </thead>
                    <tbody id="processing_table_body">
                        @forelse ($pending_pictures as $picture)
                            <tr data-picture-id="{{ $picture->id }}">
                                <td>{{ $picture->id }}</td>
                                <td>{{ optional($picture->created_at)->format('d.m.Y') }}</td>
                                <td>{{ $picture->user->name ?? 'Неизвестно' }}</td>
                                <td>
                                    <div class="admin_cell_stack">
                                        <span>{{ $picture->name }}</span>
                                        <small>{{ \Illuminate\Support\Str::limit($picture->description, 56) }}</small>
                                    </div>
                                </td>
                                <td>{{ number_format($picture->price, 0, '.', ' ') }} ₽</td>
                                <td class="admin_action_watch">
                                    <button
                                        class="admin_btn_watch js-view-picture"
                                        type="button"
                                        data-image="{{ asset($picture->img) }}"
                                        data-name="{{ $picture->name }}"
                                        aria-label="Открыть изображение {{ $picture->name }}"
                                    >
                                        <img src="{{ asset('assets/images/admin/watch.svg') }}" alt="Просмотр">
                                    </button>
                                </td>
                                <td class="admin_actions">
                                    <button class="admin_btn admin_btn_accept js-moderate-picture" type="button" data-picture-id="{{ $picture->id }}" data-action="approve">
                                        <img src="{{ asset('assets/images/admin/accept.svg') }}" alt="Одобрить">
                                    </button>
                                    <button class="admin_btn admin_btn_decline js-moderate-picture" type="button" data-picture-id="{{ $picture->id }}" data-action="reject">
                                        <img src="{{ asset('assets/images/admin/decline.svg') }}" alt="Отклонить">
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr class="admin_empty_row" id="processing_empty_row">
                                <td colspan="7">Нет картин на модерации</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="admin_table_wrapper" data-table="deals">
        <div class="admin_panel">
            <div class="admin_panel_header">
                <h2 class="admin_panel_title">Успешные сделки</h2>
            </div>

            <div class="admin_table_scroll">
                <table class="admin_table">
                    <thead>
                        <tr>
                            <th style="width: 100px;">ID</th>
                            <th style="width: 180px;">Дата</th>
                            <th style="width: 220px;">Продавец</th>
                            <th style="width: 220px;">Покупатель</th>
                            <th style="width: 220px;">Картина</th>
                            <th style="width: 180px;">Стоимость</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($deals as $deal)
                            <tr>
                                <td>{{ $deal->id }}</td>
                                <td>{{ optional($deal->created_at)->format('d.m.Y') }}</td>
                                <td>{{ $deal->seller->name ?? 'Неизвестно' }}</td>
                                <td>{{ $deal->buyer->name ?? 'Неизвестно' }}</td>
                                <td>{{ $deal->picture->name ?? 'Картина удалена' }}</td>
                                <td>{{ number_format($deal->price, 0, '.', ' ') }} ₽</td>
                            </tr>
                        @empty
                            <tr class="admin_empty_row">
                                <td colspan="6">Сделок пока нет</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="admin_table_wrapper" data-table="users">
        <div class="admin_panel">
            <div class="admin_panel_header">
                <h2 class="admin_panel_title">Пользователи</h2>
            </div>

            <div class="admin_table_scroll">
                <table class="admin_table admin_table_users">
                    <thead>
                        <tr>
                            <th style="width: 100px;">ID</th>
                            <th style="width: 220px;">Дата регистрации</th>
                            <th style="width: 260px;">Пользователь</th>
                            <th style="width: 180px;">Роль</th>
                            <th style="width: 180px;">Ранг</th>
                            <th style="width: 180px;">Картин</th>
                            <th style="width: 180px;">Сделок</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr
                                class="admin_user_row"
                                data-user-id="{{ $user->id }}"
                                role="button"
                                tabindex="0"
                                aria-label="Открыть профиль пользователя {{ $user->name }}"
                            >
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->date_of_reg ? \Carbon\Carbon::parse($user->date_of_reg)->format('d.m.Y') : '-' }}</td>
                                <td>
                                    <div class="admin_user_identity">
                                        <img
                                            src="{{ asset($user->img ?: 'assets/images/account/mainUser.png') }}"
                                            alt="{{ $user->name }}"
                                            class="admin_user_avatar"
                                        >
                                        <div class="admin_cell_stack">
                                            <span>{{ $user->name }}</span>
                                            <small>{{ $user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->role == 2 ? 'Администратор' : 'Пользователь' }}</td>
                                <td>{{ $user->rank ?: 'Не указан' }}</td>
                                <td>{{ $user->pictures_count }}</td>
                                <td>{{ $user->orders_count }}</td>
                            </tr>
                        @empty
                            <tr class="admin_empty_row">
                                <td colspan="7">Пользователи не найдены</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="admin_table_wrapper admin_categories" data-table="categories">
        <div class="categories_container">
            <section class="categories_add">
                <h2 class="categories_add_title">Добавление категорий</h2>

                <div class="category_form_section">
                    <label class="category_label" for="genre_input">Жанр</label>
                    <div class="category_input_wrapper">
                        <input type="text" class="category_input" id="genre_input" placeholder="Введите жанр картины">
                        <button class="category_add_btn" type="button" onclick="addCategory('genre')">+</button>
                    </div>
                </div>

                <div class="category_form_section">
                    <label class="category_label" for="style_input">Стиль</label>
                    <div class="category_input_wrapper">
                        <input type="text" class="category_input" id="style_input" placeholder="Введите стиль написания">
                        <button class="category_add_btn" type="button" onclick="addCategory('style')">+</button>
                    </div>
                </div>

                <div class="category_form_section">
                    <label class="category_label" for="era_input">Эпоха</label>
                    <div class="category_input_wrapper">
                        <input type="text" class="category_input" id="era_input" placeholder="Введите эпоху картины">
                        <button class="category_add_btn" type="button" onclick="addCategory('era')">+</button>
                    </div>
                </div>
            </section>

            <section class="categories_list">
                <div class="category_section">
                    <h3 class="category_section_title"><span>I</span> Жанры</h3>
                    <div class="category_items" id="genres_list">
                        @forelse ($genres as $genre)
                            <div class="category_item" data-id="{{ $genre->id }}">
                                <span>{{ $genre->name }}</span>
                                <button class="category_delete_btn js-delete-category" type="button" data-type="genre" data-id="{{ $genre->id }}">
                                    <img src="{{ asset('assets/images/admin/delete.svg') }}" alt="Удалить">
                                </button>
                            </div>
                        @empty
                            <p class="no_categories">Жанры не добавлены</p>
                        @endforelse
                    </div>
                </div>

                <div class="category_section">
                    <h3 class="category_section_title"><span>II</span> Стили</h3>
                    <div class="category_items category_items_columns" id="styles_list">
                        @php
                            $stylesHalf = (int) ceil($styles->count() / 2);
                            $stylesFirstColumn = $styles->slice(0, $stylesHalf);
                            $stylesSecondColumn = $styles->slice($stylesHalf);
                        @endphp

                        <div class="category_column">
                            @foreach ($stylesFirstColumn as $style)
                                <div class="category_item" data-id="{{ $style->id }}">
                                    <span>{{ $style->name }}</span>
                                    <button class="category_delete_btn js-delete-category" type="button" data-type="style" data-id="{{ $style->id }}">
                                        <img src="{{ asset('assets/images/admin/delete.svg') }}" alt="Удалить">
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        <div class="category_column">
                            @foreach ($stylesSecondColumn as $style)
                                <div class="category_item" data-id="{{ $style->id }}">
                                    <span>{{ $style->name }}</span>
                                    <button class="category_delete_btn js-delete-category" type="button" data-type="style" data-id="{{ $style->id }}">
                                        <img src="{{ asset('assets/images/admin/delete.svg') }}" alt="Удалить">
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        @if ($styles->isEmpty())
                            <p class="no_categories">Стили не добавлены</p>
                        @endif
                    </div>
                </div>

                <div class="category_section">
                    <h3 class="category_section_title"><span>III</span> Эпохи</h3>
                    <div class="category_items" id="eras_list">
                        @forelse ($eras as $era)
                            <div class="category_item" data-id="{{ $era->id }}">
                                <span>{{ $era->name }}</span>
                                <button class="category_delete_btn js-delete-category" type="button" data-type="era" data-id="{{ $era->id }}">
                                    <img src="{{ asset('assets/images/admin/delete.svg') }}" alt="Удалить">
                                </button>
                            </div>
                        @empty
                            <p class="no_categories">Эпохи не добавлены</p>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>
    </div>
</main>

<div id="toast-container" class="toast-container"></div>
@endsection

@section('scripts')
<style>
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
    }

    .toast {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 280px;
        margin-bottom: 10px;
        padding: 16px 20px;
        border-radius: 12px;
        background: #202020;
        color: #e0e0e0;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.28);
        animation: slideIn 0.3s ease-out;
    }

    .toast.success {
        border-left: 4px solid #fbff83;
    }

    .toast.error {
        border-left: 4px solid #c76060;
    }

    .toast-icon {
        font-size: 18px;
        line-height: 1;
    }

    @keyframes slideIn {
        from {
            transform: translateX(24px);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }

        to {
            transform: translateX(24px);
            opacity: 0;
        }
    }
</style>

<script>
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');

        if (!container) {
            return;
        }

        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `
            <span class="toast-icon">${type === 'success' ? '✓' : '✕'}</span>
            <span>${escapeHtml(message)}</span>
        `;

        container.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease-out forwards';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function addCategoryToDom(type, id, name) {
        let container;

        if (type === 'genre') {
            container = document.getElementById('genres_list');
        } else if (type === 'style') {
            container = document.getElementById('styles_list');
        } else {
            container = document.getElementById('eras_list');
        }

        if (!container) {
            return;
        }

        const placeholder = container.querySelector('.no_categories');
        if (placeholder) {
            placeholder.remove();
        }

        const item = document.createElement('div');
        item.className = 'category_item';
        item.dataset.id = id;
        item.innerHTML = `
            <span>${escapeHtml(name)}</span>
            <button class="category_delete_btn js-delete-category" type="button" data-type="${type}" data-id="${id}">
                <img src="{{ asset('assets/images/admin/delete.svg') }}" alt="Удалить">
            </button>
        `;

        if (type === 'style') {
            let columns = container.querySelectorAll('.category_column');

            if (!columns.length) {
                container.innerHTML = '<div class="category_column"></div><div class="category_column"></div>';
                columns = container.querySelectorAll('.category_column');
            }

            const firstColumnCount = columns[0].querySelectorAll('.category_item').length;
            const secondColumnCount = columns[1].querySelectorAll('.category_item').length;
            const targetColumn = firstColumnCount <= secondColumnCount ? columns[0] : columns[1];
            targetColumn.appendChild(item);
            return;
        }

        container.appendChild(item);
    }

    function checkEmptyCategories(type) {
        const map = {
            genre: { id: 'genres_list', text: 'Жанры не добавлены' },
            style: { id: 'styles_list', text: 'Стили не добавлены' },
            era: { id: 'eras_list', text: 'Эпохи не добавлены' }
        };

        const config = map[type];
        const container = document.getElementById(config.id);

        if (!container) {
            return;
        }

        const items = container.querySelectorAll('.category_item');
        if (items.length > 0 || container.querySelector('.no_categories')) {
            return;
        }

        const emptyMessage = document.createElement('p');
        emptyMessage.className = 'no_categories';
        emptyMessage.textContent = config.text;
        container.appendChild(emptyMessage);
    }

    async function addCategory(type) {
        const input = document.getElementById(`${type}_input`);
        const name = input ? input.value.trim() : '';

        if (!name) {
            showToast('Введите название категории', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('action', 'add');
        formData.append('type', type);
        formData.append('name', name);

        try {
            const response = await fetch('/api/categories', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const result = await response.json();

            if (!result.success) {
                showToast(result.message || 'Не удалось добавить категорию', 'error');
                return;
            }

            showToast(result.message || 'Категория добавлена');
            input.value = '';
            addCategoryToDom(type, result.id, name);
        } catch (error) {
            showToast('Ошибка при добавлении категории', 'error');
            console.error(error);
        }
    }

    async function deleteCategory(type, id) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('type', type);
        formData.append('id', id);

        try {
            const response = await fetch('/api/categories', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const result = await response.json();

            if (!result.success) {
                showToast(result.message || 'Не удалось удалить категорию', 'error');
                return;
            }

            const item = document.querySelector(`.category_item[data-id="${id}"]`);
            if (item) {
                item.remove();
            }

            checkEmptyCategories(type);
            showToast(result.message || 'Категория удалена');
        } catch (error) {
            showToast('Ошибка при удалении категории', 'error');
            console.error(error);
        }
    }

    function ensureProcessingEmptyState() {
        const tbody = document.getElementById('processing_table_body');

        if (!tbody || tbody.querySelector('tr[data-picture-id]') || document.getElementById('processing_empty_row')) {
            return;
        }

        const row = document.createElement('tr');
        row.className = 'admin_empty_row';
        row.id = 'processing_empty_row';
        row.innerHTML = '<td colspan="7">Нет картин на модерации</td>';
        tbody.appendChild(row);
    }

    async function moderatePicture(pictureId, action) {
        const formData = new FormData();
        formData.append('picture_id', pictureId);
        formData.append('action', action);

        try {
            const response = await fetch('/api/picture/moderate', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const result = await response.json();

            if (!result.success) {
                showToast(result.message || 'Не удалось обновить статус', 'error');
                return;
            }

            const row = document.querySelector(`tr[data-picture-id="${pictureId}"]`);
            if (row) {
                row.remove();
            }

            ensureProcessingEmptyState();
            showToast(result.message || 'Статус картины обновлён');
        } catch (error) {
            showToast('Ошибка при модерации картины', 'error');
            console.error(error);
        }
    }

    function viewPicture(imagePath, pictureName) {
        const overlay = document.createElement('div');
        overlay.className = 'admin_preview_overlay';
        overlay.innerHTML = `
            <div class="admin_preview_modal" role="dialog" aria-modal="true" aria-label="${escapeHtml(pictureName)}">
                <button class="admin_preview_close" type="button" aria-label="Закрыть просмотр">✕</button>
                <img src="${imagePath}" alt="${escapeHtml(pictureName)}">
                <div class="admin_preview_caption">${escapeHtml(pictureName)}</div>
            </div>
        `;

        document.body.appendChild(overlay);

        overlay.addEventListener('click', (event) => {
            if (event.target === overlay || event.target.classList.contains('admin_preview_close')) {
                overlay.remove();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        ['genre', 'style', 'era'].forEach((type) => {
            const input = document.getElementById(`${type}_input`);

            if (!input) {
                return;
            }

            input.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    addCategory(type);
                }
            });
        });

        document.querySelectorAll('.admin_user_row').forEach((row) => {
            const openProfile = () => {
                const userId = row.dataset.userId;
                if (userId) {
                    window.location.href = `/account?user_id=${userId}`;
                }
            };

            row.addEventListener('click', openProfile);
            row.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    openProfile();
                }
            });
        });

        document.addEventListener('click', (event) => {
            const deleteButton = event.target.closest('.js-delete-category');
            if (deleteButton) {
                deleteCategory(deleteButton.dataset.type, Number(deleteButton.dataset.id));
                return;
            }

            const moderateButton = event.target.closest('.js-moderate-picture');
            if (moderateButton) {
                moderatePicture(Number(moderateButton.dataset.pictureId), moderateButton.dataset.action);
                return;
            }

            const viewButton = event.target.closest('.js-view-picture');
            if (viewButton) {
                viewPicture(viewButton.dataset.image, viewButton.dataset.name);
            }
        });
    });
</script>
@endsection
