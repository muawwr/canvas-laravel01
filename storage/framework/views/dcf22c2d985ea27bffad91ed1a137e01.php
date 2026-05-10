

<?php $__env->startSection('content'); ?>
<main class="admin_main container">

    <div class="admin_tabs">
        <button class="admin_tab admin_tab_active" type="button" data-tab="processing">
            На обработку
            <span class="admin_tab_badge"><?php echo e($pending_pictures->count()); ?></span>
        </button>
        <button class="admin_tab" type="button" data-tab="deals">
            Сделки
            <span class="admin_tab_badge"><?php echo e($deals->count()); ?></span>
        </button>
        <button class="admin_tab" type="button" data-tab="users">
            Пользователи
            <span class="admin_tab_badge"><?php echo e($users->count()); ?></span>
        </button>
        <button class="admin_tab" type="button" data-tab="categories">Категории</button>
    </div>

    <div class="admin_table_wrapper" data-table="processing">
        <div class="admin_panel">

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
                        <?php $__empty_1 = true; $__currentLoopData = $pending_pictures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $picture): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr data-picture-id="<?php echo e($picture->id); ?>">
                                <td><?php echo e($picture->id); ?></td>
                                <td><?php echo e(optional($picture->created_at)->format('d.m.Y')); ?></td>
                                <td><?php echo e($picture->user->name ?? 'Неизвестно'); ?></td>
                                <td>
                                    <div class="admin_cell_stack">
                                        <span><?php echo e($picture->name); ?></span>
                                        <small><?php echo e(\Illuminate\Support\Str::limit($picture->description, 56)); ?></small>
                                    </div>
                                </td>
                                <td><?php echo e(number_format($picture->price, 0, '.', ' ')); ?> ₽</td>
                                <td class="admin_action_watch">
                                    <button
                                        class="admin_btn_watch js-view-picture"
                                        type="button"
                                        data-image="<?php echo e(asset($picture->img)); ?>"
                                        data-name="<?php echo e($picture->name); ?>"
                                        aria-label="Открыть изображение <?php echo e($picture->name); ?>"
                                    >
                                        <img src="<?php echo e(asset('assets/images/admin/watch.svg')); ?>" alt="Просмотр">
                                    </button>
                                </td>
                                <td class="admin_actions">
                                    <button class="admin_btn admin_btn_accept js-moderate-picture" type="button" data-picture-id="<?php echo e($picture->id); ?>" data-action="approve">
                                        <img src="<?php echo e(asset('assets/images/admin/accept.svg')); ?>" alt="Одобрить">
                                    </button>
                                    <button class="admin_btn admin_btn_decline js-moderate-picture" type="button" data-picture-id="<?php echo e($picture->id); ?>" data-action="reject">
                                        <img src="<?php echo e(asset('assets/images/admin/decline.svg')); ?>" alt="Отклонить">
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr class="admin_empty_row" id="processing_empty_row">
                                <td colspan="7">Нет картин на модерации</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="admin_table_wrapper" data-table="deals">
        <div class="admin_panel">


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
                        <?php $__empty_1 = true; $__currentLoopData = $deals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($deal->id); ?></td>
                                <td><?php echo e(optional($deal->created_at)->format('d.m.Y')); ?></td>
                                <td><?php echo e($deal->seller->name ?? 'Неизвестно'); ?></td>
                                <td><?php echo e($deal->buyer->name ?? 'Неизвестно'); ?></td>
                                <td><?php echo e($deal->picture->name ?? 'Картина удалена'); ?></td>
                                <td><?php echo e(number_format($deal->price, 0, '.', ' ')); ?> ₽</td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr class="admin_empty_row">
                                <td colspan="6">Сделок пока нет</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="admin_table_wrapper" data-table="users">
        <div class="admin_panel">


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
                        <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr
                                class="admin_user_row"
                                data-user-id="<?php echo e($user->id); ?>"
                                role="button"
                                tabindex="0"
                                aria-label="Открыть профиль пользователя <?php echo e($user->name); ?>"
                            >
                                <td><?php echo e($user->id); ?></td>
                                <td><?php echo e($user->date_of_reg ? \Carbon\Carbon::parse($user->date_of_reg)->format('d.m.Y') : '-'); ?></td>
                                <td>
                                    <div class="admin_user_identity">
                                        <img
                                            src="<?php echo e(asset($user->img ?: 'assets/images/account/mainUser.png')); ?>"
                                            alt="<?php echo e($user->name); ?>"
                                            class="admin_user_avatar"
                                        >
                                        <div class="admin_cell_stack">
                                            <span><?php echo e($user->name); ?></span>
                                            <small><?php echo e($user->email); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo e($user->role == 2 ? 'Администратор' : 'Пользователь'); ?></td>
                                <td><?php echo e($user->rank ?: 'Не указан'); ?></td>
                                <td><?php echo e($user->pictures_count); ?></td>
                                <td><?php echo e($user->orders_count); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr class="admin_empty_row">
                                <td colspan="7">Пользователи не найдены</td>
                            </tr>
                        <?php endif; ?>
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
                        <?php $__empty_1 = true; $__currentLoopData = $genres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $genre): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="category_item" data-id="<?php echo e($genre->id); ?>">
                                <span><?php echo e($genre->name); ?></span>
                                <button class="category_delete_btn js-delete-category" type="button" data-type="genre" data-id="<?php echo e($genre->id); ?>">
                                    <img src="<?php echo e(asset('assets/images/admin/delete.svg')); ?>" alt="Удалить">
                                </button>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="no_categories">Жанры не добавлены</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="category_section">
                    <h3 class="category_section_title"><span>II</span> Стили</h3>
                    <div class="category_items category_items_columns" id="styles_list">
                        <?php
                            $stylesHalf = (int) ceil($styles->count() / 2);
                            $stylesFirstColumn = $styles->slice(0, $stylesHalf);
                            $stylesSecondColumn = $styles->slice($stylesHalf);
                        ?>

                        <div class="category_column">
                            <?php $__currentLoopData = $stylesFirstColumn; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $style): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="category_item" data-id="<?php echo e($style->id); ?>">
                                    <span><?php echo e($style->name); ?></span>
                                    <button class="category_delete_btn js-delete-category" type="button" data-type="style" data-id="<?php echo e($style->id); ?>">
                                        <img src="<?php echo e(asset('assets/images/admin/delete.svg')); ?>" alt="Удалить">
                                    </button>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                        <div class="category_column">
                            <?php $__currentLoopData = $stylesSecondColumn; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $style): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="category_item" data-id="<?php echo e($style->id); ?>">
                                    <span><?php echo e($style->name); ?></span>
                                    <button class="category_delete_btn js-delete-category" type="button" data-type="style" data-id="<?php echo e($style->id); ?>">
                                        <img src="<?php echo e(asset('assets/images/admin/delete.svg')); ?>" alt="Удалить">
                                    </button>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                        <?php if($styles->isEmpty()): ?>
                            <p class="no_categories">Стили не добавлены</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="category_section">
                    <h3 class="category_section_title"><span>III</span> Эпохи</h3>
                    <div class="category_items" id="eras_list">
                        <?php $__empty_1 = true; $__currentLoopData = $eras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $era): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="category_item" data-id="<?php echo e($era->id); ?>">
                                <span><?php echo e($era->name); ?></span>
                                <button class="category_delete_btn js-delete-category" type="button" data-type="era" data-id="<?php echo e($era->id); ?>">
                                    <img src="<?php echo e(asset('assets/images/admin/delete.svg')); ?>" alt="Удалить">
                                </button>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="no_categories">Эпохи не добавлены</p>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div class="admin_reject_modal" id="reject_reason_modal" hidden>
        <div class="admin_reject_dialog" role="dialog" aria-modal="true" aria-labelledby="reject_reason_title">
            <h2 id="reject_reason_title">Причина отказа</h2>
            <form id="reject_reason_form">
                <textarea
                    id="reject_reason_text"
                    class="admin_reject_textarea"
                    rows="5"
                    maxlength="500"
                    placeholder="Например: изображение плохого качества или описание заполнено некорректно"
                    required
                ></textarea>
                <span class="admin_reject_error" id="reject_reason_error"></span>

                <div class="admin_reject_actions">
                    <button class="admin_reject_cancel" type="button" data-reject-close>Отмена</button>
                    <button class="admin_reject_submit" type="submit">Отклонить</button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<style>
.admin_reject_modal {
    position: fixed;
    inset: 0;
    z-index: 10002;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
    background: rgba(0, 0, 0, 0.82);
}

.admin_reject_modal[hidden] {
    display: none;
}

.admin_reject_dialog {
    position: relative;
    width: min(100%, 520px);
    padding: 28px;
    border-radius: 18px;
    background: #151515;

    box-shadow: 0 24px 70px rgba(0, 0, 0, 0.45);
}

.admin_reject_dialog h2 {
    margin: 0 0 10px;
    color: #F3F3F3;
    font-size: 24px;
}

.admin_reject_dialog p {
    margin: 0 0 18px;
    color: #AFAFAF;
    line-height: 1.45;
}

.admin_reject_close {
    position: absolute;
    top: 18px;
    right: 18px;
    width: 32px;
    height: 32px;
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 50%;
    background: transparent;
    color: #E0E0E0;
    cursor: pointer;
}

.admin_reject_textarea {
    width: 100%;
    min-height: 130px;
    resize: vertical;
    padding: 14px 16px;
    border-radius: 14px;
    border: none;
    background: #0E0E0E;
    color: #EAEAEA;
    font: inherit;
    outline: none;
}

.admin_reject_error {
    display: block;
    min-height: 20px;
    margin-top: 8px;
    color: #FF6B6B;
    font-size: 14px;
}

.admin_reject_actions {
    display: flex;
    gap: 12px;
    margin-top: 5px;
}

.admin_reject_cancel,
.admin_reject_submit {
    min-height: 44px;
    padding: 0 20px;
    border-radius: 12px;
    cursor: pointer;
    font-weight: 600;
}

.admin_reject_cancel {
    background: transparent;
    color: #E0E0E0;
    border: 1px solid rgba(255, 255, 255, 0.16);
}

.admin_reject_submit {
    background: #FBFF83;
    color: #111;
    border: 1px solid #FBFF83;
}
</style>

<script>
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
                <img src="<?php echo e(asset('assets/images/admin/delete.svg')); ?>" alt="Удалить">
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
                return;
            }

            input.value = '';
            addCategoryToDom(type, result.id, name);
        } catch (error) {
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
                return;
            }

            const item = document.querySelector(`.category_item[data-id="${id}"]`);
            if (item) {
                item.remove();
            }

            checkEmptyCategories(type);
        } catch (error) {
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

    async function moderatePicture(pictureId, action, rejectionReason = '') {
        const formData = new FormData();
        formData.append('picture_id', pictureId);
        formData.append('action', action);
        if (action === 'reject') {
            formData.append('rejection_reason', rejectionReason);
        }

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
                alert(result.message || 'Не удалось выполнить действие');
                return false;
            }

            const row = document.querySelector(`tr[data-picture-id="${pictureId}"]`);
            if (row) {
                row.remove();
            }

            ensureProcessingEmptyState();
            return true;
        } catch (error) {
            console.error(error);
            return false;
        }
    }
    function viewPicture(imagePath, pictureName) {
        const overlay = document.createElement('div');
        overlay.className = 'admin_preview_overlay';
        overlay.innerHTML = `
            <div class="admin_preview_modal" role="dialog" aria-modal="true" aria-label="${escapeHtml(pictureName)}">
                <button class="admin_preview_close" type="button" aria-label="Закрыть просмотр">x</button>
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
        const rejectModal = document.getElementById('reject_reason_modal');
        const rejectForm = document.getElementById('reject_reason_form');
        const rejectTextarea = document.getElementById('reject_reason_text');
        const rejectError = document.getElementById('reject_reason_error');
        let pendingRejectPictureId = null;

        function openRejectModal(pictureId) {
            pendingRejectPictureId = pictureId;
            rejectTextarea.value = '';
            rejectError.textContent = '';
            rejectModal.hidden = false;
            rejectTextarea.focus();
        }

        function closeRejectModal() {
            rejectModal.hidden = true;
            pendingRejectPictureId = null;
            rejectTextarea.value = '';
            rejectError.textContent = '';
        }

        document.querySelectorAll('[data-reject-close]').forEach((button) => {
            button.addEventListener('click', closeRejectModal);
        });

        rejectModal.addEventListener('click', (event) => {
            if (event.target === rejectModal) {
                closeRejectModal();
            }
        });

        rejectForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const reason = rejectTextarea.value.trim();
            if (!reason) {
                rejectError.textContent = 'Причина отклонения обязательна';
                rejectTextarea.focus();
                return;
            }

            const success = await moderatePicture(pendingRejectPictureId, 'reject', reason);
            if (success) {
                closeRejectModal();
            }
        });

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
                const action = moderateButton.dataset.action;

                if (action === 'reject') {
                    openRejectModal(Number(moderateButton.dataset.pictureId));
                    return;
                }

                moderatePicture(Number(moderateButton.dataset.pictureId), action);
                return;
            }
            const viewButton = event.target.closest('.js-view-picture');
            if (viewButton) {
                viewPicture(viewButton.dataset.image, viewButton.dataset.name);
            }
        });
    });
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\canvas-laravel01\resources\views/admin.blade.php ENDPATH**/ ?>