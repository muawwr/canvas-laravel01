@extends('layouts.app')

@section('nav-profile-active', 'active')

@section('content')
<main class="edit_main container" data-managed-edit-page="custom">
    <div class="edit_container">
        <div class="edit_header">
            <h1 class="edit_title">Редактирование картины</h1>
            <p class="edit_subtitle">Обновите изображение, описание и параметры работы. После сохранения изменения отправятся на повторную модерацию.</p>
        </div>

        <div class="add_line"></div>

        <div class="edit_content">
            <section class="edit_left">
                <div class="edit_section">
                    <label class="edit_label">Изображение</label>
                    <button class="edit_upload_area" id="uploadArea" type="button">
                        <input type="file" id="imageInput" class="upload_input" accept="image/jpeg,image/png,image/jpg,image/webp">
                        <img id="previewImage" class="edit_upload_preview" src="{{ asset($picture->img) }}" alt="{{ $picture->name }}">
                        <div class="edit_upload_overlay">
                            <div class="upload_icon">
                                <img src="{{ asset('assets/images/add/Upload.svg') }}" alt="Загрузить">
                            </div>
                            <p class="upload_text" id="uploadText">Заменить изображение</p>
                            <span class="edit_upload_hint">PNG, JPG или WEBP</span>
                        </div>
                    </button>
                </div>

                <div class="edit_section">
                    <label class="edit_label">Размер картины</label>
                    <div class="edit_size_inputs">
                        <input type="number" id="widthInput" class="edit_input" placeholder="Ширина" min="1" value="{{ $picture->width }}">
                        <span class="size_separator">×</span>
                        <input type="number" id="heightInput" class="edit_input" placeholder="Высота" min="1" value="{{ $picture->height }}">
                    </div>
                </div>

                <div class="edit_section">
                    <label class="edit_label" for="nameInput">Название картины</label>
                    <input type="text" class="edit_input edit_input_full" id="nameInput" placeholder="Введите название картины" maxlength="255" value="{{ $picture->name }}">
                </div>

                <div class="edit_row">
                    <div class="edit_section edit_section_half">
                        <label class="edit_label" for="techniqueInput">Техника написания</label>
                        <input type="text" class="edit_input" id="techniqueInput" placeholder="Гуашь, пастель" maxlength="255" value="{{ $picture->technique }}">
                    </div>

                    <div class="edit_section edit_section_half">
                        <label class="edit_label" for="yearInput">Год написания</label>
                        <input type="number" class="edit_input" id="yearInput" placeholder="ГГГГ" min="1000" max="{{ date('Y') }}" value="{{ $picture->year }}">
                    </div>
                </div>

                <div class="edit_section">
                    <label class="edit_label" for="descriptionInput">Описание картины</label>
                    <textarea class="edit_textarea" id="descriptionInput" placeholder="Введите описание картины" maxlength="5000">{{ $picture->description }}</textarea>
                </div>
            </section>

            <aside class="edit_right">
                <div class="edit_section">
                    <label class="edit_label">Жанр</label>
                    <div class="custom_select has-value" data-select="genre_id" id="genreSelect">
                        <div class="custom_select_trigger">
                            <span class="custom_select_text">{{ optional($genres->firstWhere('id', $picture->genre_id))->name ?? 'Выберите жанр' }}</span>
                            <img src="{{ asset('assets/images/add/Arrow.svg') }}" alt="Открыть список" class="select_arrow">
                        </div>
                        <div class="custom_select_options">
                            @forelse ($genres as $genre)
                                <button class="custom_select_option {{ $picture->genre_id == $genre->id ? 'selected' : '' }}" type="button" data-value="{{ $genre->id }}">{{ $genre->name }}</button>
                            @empty
                                <button class="custom_select_option" type="button" disabled>Жанры не добавлены</button>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="edit_section">
                    <label class="edit_label">Стиль</label>
                    <div class="custom_select has-value" data-select="style_id" id="styleSelect">
                        <div class="custom_select_trigger">
                            <span class="custom_select_text">{{ optional($styles->firstWhere('id', $picture->style_id))->name ?? 'Выберите стиль' }}</span>
                            <img src="{{ asset('assets/images/add/Arrow.svg') }}" alt="Открыть список" class="select_arrow">
                        </div>
                        <div class="custom_select_options">
                            @forelse ($styles as $style)
                                <button class="custom_select_option {{ $picture->style_id == $style->id ? 'selected' : '' }}" type="button" data-value="{{ $style->id }}">{{ $style->name }}</button>
                            @empty
                                <button class="custom_select_option" type="button" disabled>Стили не добавлены</button>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="edit_section">
                    <label class="edit_label">Эпоха</label>
                    <div class="custom_select has-value" data-select="era_id" id="eraSelect">
                        <div class="custom_select_trigger">
                            <span class="custom_select_text">{{ optional($eras->firstWhere('id', $picture->era_id))->name ?? 'Выберите эпоху' }}</span>
                            <img src="{{ asset('assets/images/add/Arrow.svg') }}" alt="Открыть список" class="select_arrow">
                        </div>
                        <div class="custom_select_options">
                            @forelse ($eras as $era)
                                <button class="custom_select_option {{ $picture->era_id == $era->id ? 'selected' : '' }}" type="button" data-value="{{ $era->id }}">{{ $era->name }}</button>
                            @empty
                                <button class="custom_select_option" type="button" disabled>Эпохи не добавлены</button>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="edit_section">
                    <div class="edit_price_header">
                        <label class="edit_label" for="editPriceYouGet">Цена, ₽</label>
                        <span class="edit_price_hint">Сколько вы получите</span>
                    </div>
                    <input type="number" class="edit_input edit_input_full" id="editPriceYouGet" placeholder="Введите цену" min="100" value="{{ $picture->price }}">
                </div>

                <div class="edit_section">
                    <div class="edit_price_header">
                        <label class="edit_label" for="editPriceBuyerPays">Цена, ₽</label>
                        <span class="edit_price_hint">Сколько заплатит покупатель с комиссией</span>
                    </div>
                    <input type="number" class="edit_input edit_input_full" id="editPriceBuyerPays" value="{{ (int) round($picture->price / (1 - 0.10)) }}" readonly>
                </div>

                <div class="edit_info_card">
                    <p class="edit_info_title">Что произойдёт после сохранения</p>
                    <p class="edit_info_text">Картина обновится и снова попадёт на модерацию, чтобы новые данные прошли проверку.</p>
                </div>

                <div class="edit_buttons">
                    <a href="{{ url('/picture/' . $picture->id) }}" class="edit_btn edit_btn_cancel">Отмена</a>
                    <button class="edit_btn edit_btn_submit" id="saveBtn" type="button">
                        Сохранить
                        <img src="{{ asset('assets/images/add/Right.svg') }}" alt="Сохранить">
                    </button>
                </div>
            </aside>
        </div>
    </div>
</main>

<div class="success_modal_overlay" id="editSuccessModal" style="display: none;">
    <div class="success_modal">
        <div class="success_icon">
            <img src="{{ asset('assets/images/add/Success.svg') }}" alt="Успешно">
        </div>
        <h2 class="success_title">Изменения отправлены на модерацию</h2>
        <button class="success_btn" id="editSuccessOkBtn" type="button">OK!</button>
    </div>
</div>

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

    .error-message {
        display: block;
        margin-top: 8px;
        color: #c76060;
        font-family: 'InterTight', sans-serif;
        font-size: 13px;
    }

    .edit_input.input-error,
    .edit_textarea.input-error,
    .custom_select.input-error .custom_select_trigger,
    .edit_upload_area.input-error {
        border-color: #c76060 !important;
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
    const pictureId = {{ $picture->id }};
    let newImageFile = null;

    const formState = {
        genre_id: {{ $picture->genre_id }},
        style_id: {{ $picture->style_id }},
        era_id: {{ $picture->era_id }}
    };

    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        if (!container) {
            return;
        }

        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `<span style="font-size:20px">${type === 'success' ? '✓' : '✕'}</span><span>${escapeHtml(message)}</span>`;
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

    function clearFieldError(element) {
        element.classList.remove('input-error');
        const container = element.closest('.edit_section') || element.parentElement;
        const error = container ? container.querySelector('.error-message') : null;
        if (error) {
            error.remove();
        }
    }

    function showFieldError(element, message) {
        clearFieldError(element);
        element.classList.add('input-error');

        const error = document.createElement('span');
        error.className = 'error-message';
        error.textContent = message;

        const container = element.closest('.edit_section') || element.parentElement;
        if (container) {
            container.appendChild(error);
        }
    }

    function clearAllErrors() {
        document.querySelectorAll('.error-message').forEach((item) => item.remove());
        document.querySelectorAll('.input-error').forEach((item) => item.classList.remove('input-error'));
    }

    function setupCustomSelects() {
        const selects = document.querySelectorAll('.custom_select');

        selects.forEach((select) => {
            const trigger = select.querySelector('.custom_select_trigger');
            const options = select.querySelectorAll('.custom_select_option:not([disabled])');
            const textElement = select.querySelector('.custom_select_text');
            const selectKey = select.dataset.select;

            trigger.addEventListener('click', (event) => {
                event.stopPropagation();

                selects.forEach((item) => {
                    if (item !== select) {
                        item.classList.remove('open');
                    }
                });

                select.classList.toggle('open');
            });

            options.forEach((option) => {
                option.addEventListener('click', (event) => {
                    event.stopPropagation();

                    options.forEach((item) => item.classList.remove('selected'));
                    option.classList.add('selected');

                    textElement.textContent = option.textContent;
                    select.classList.add('has-value');
                    select.classList.remove('open');

                    formState[selectKey] = Number(option.dataset.value);
                    clearFieldError(select);
                });
            });
        });

        document.addEventListener('click', () => {
            selects.forEach((select) => select.classList.remove('open'));
        });
    }

    function updateBuyerPrice() {
        const priceInput = document.getElementById('editPriceYouGet');
        const buyerPriceInput = document.getElementById('editPriceBuyerPays');
        const value = parseFloat(priceInput.value);

        if (!value || value <= 0) {
            buyerPriceInput.value = '';
            return;
        }

        buyerPriceInput.value = Math.round(value / (1 - 0.10));
    }

    document.addEventListener('DOMContentLoaded', () => {
        const uploadArea = document.getElementById('uploadArea');
        const imageInput = document.getElementById('imageInput');
        const previewImage = document.getElementById('previewImage');
        const uploadText = document.getElementById('uploadText');
        const saveButton = document.getElementById('saveBtn');

        setupCustomSelects();
        updateBuyerPrice();

        uploadArea.addEventListener('click', () => imageInput.click());

        imageInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (!file) {
                return;
            }

            newImageFile = file;
            uploadText.textContent = file.name;
            clearFieldError(uploadArea);

            const reader = new FileReader();
            reader.onload = (loadEvent) => {
                previewImage.src = loadEvent.target.result;
            };
            reader.readAsDataURL(file);
        });

        ['widthInput', 'heightInput', 'nameInput', 'techniqueInput', 'yearInput', 'descriptionInput', 'editPriceYouGet'].forEach((id) => {
            const element = document.getElementById(id);
            element.addEventListener('input', () => {
                clearFieldError(element);
            });
        });

        document.getElementById('editPriceYouGet').addEventListener('input', updateBuyerPrice);

        saveButton.addEventListener('click', async function () {
            clearAllErrors();

            const widthInput = document.getElementById('widthInput');
            const heightInput = document.getElementById('heightInput');
            const nameInput = document.getElementById('nameInput');
            const techniqueInput = document.getElementById('techniqueInput');
            const yearInput = document.getElementById('yearInput');
            const descriptionInput = document.getElementById('descriptionInput');
            const priceInput = document.getElementById('editPriceYouGet');

            const payload = {
                width: widthInput.value.trim(),
                height: heightInput.value.trim(),
                name: nameInput.value.trim(),
                technique: techniqueInput.value.trim(),
                year: yearInput.value.trim(),
                description: descriptionInput.value.trim(),
                price: priceInput.value.trim()
            };

            let hasErrors = false;
            const width = Number(payload.width);
            const height = Number(payload.height);
            const year = Number(payload.year);
            const price = Number(payload.price);
            const currentYear = new Date().getFullYear();

            if (!payload.width || Number.isNaN(width) || width <= 0) {
                showFieldError(widthInput, 'Укажите корректную ширину');
                hasErrors = true;
            }

            if (!payload.height || Number.isNaN(height) || height <= 0) {
                showFieldError(heightInput, 'Укажите корректную высоту');
                hasErrors = true;
            }

            if (!payload.name) {
                showFieldError(nameInput, 'Введите название картины');
                hasErrors = true;
            } else if (payload.name.length < 3) {
                showFieldError(nameInput, 'Название должно содержать минимум 3 символа');
                hasErrors = true;
            }

            if (!payload.technique) {
                showFieldError(techniqueInput, 'Укажите технику написания');
                hasErrors = true;
            } else if (payload.technique.length < 2) {
                showFieldError(techniqueInput, 'Техника должна содержать минимум 2 символа');
                hasErrors = true;
            }

            if (!payload.year || Number.isNaN(year)) {
                showFieldError(yearInput, 'Укажите год написания');
                hasErrors = true;
            } else if (year < 1000 || year > currentYear) {
                showFieldError(yearInput, 'Укажите корректный год');
                hasErrors = true;
            }

            if (!payload.description) {
                showFieldError(descriptionInput, 'Введите описание картины');
                hasErrors = true;
            } else if (payload.description.length < 10) {
                showFieldError(descriptionInput, 'Описание должно содержать минимум 10 символов');
                hasErrors = true;
            }

            if (!formState.genre_id) {
                showFieldError(document.getElementById('genreSelect'), 'Выберите жанр');
                hasErrors = true;
            }

            if (!formState.style_id) {
                showFieldError(document.getElementById('styleSelect'), 'Выберите стиль');
                hasErrors = true;
            }

            if (!formState.era_id) {
                showFieldError(document.getElementById('eraSelect'), 'Выберите эпоху');
                hasErrors = true;
            }

            if (!payload.price || Number.isNaN(price) || price < 100) {
                showFieldError(priceInput, 'Минимальная цена - 100 ₽');
                hasErrors = true;
            }

            if (hasErrors) {
                showToast('Исправьте ошибки в форме', 'error');
                return;
            }

            const originalHtml = this.innerHTML;
            this.disabled = true;
            this.innerHTML = 'Сохранение...';

            const formData = new FormData();
            formData.append('picture_id', pictureId);
            formData.append('width', width);
            formData.append('height', height);
            formData.append('name', payload.name);
            formData.append('technique', payload.technique);
            formData.append('year', year);
            formData.append('description', payload.description);
            formData.append('genre_id', formState.genre_id);
            formData.append('style_id', formState.style_id);
            formData.append('era_id', formState.era_id);
            formData.append('price', price);

            if (newImageFile) {
                formData.append('image', newImageFile);
            }

            try {
                const response = await fetch('/api/picture/edit', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();

                if (!result.success) {
                    showToast(result.message || 'Ошибка при редактировании картины', 'error');
                    this.disabled = false;
                    this.innerHTML = originalHtml;
                    return;
                }

                document.getElementById('editSuccessModal').style.display = 'flex';
            } catch (error) {
                showToast('Ошибка подключения к серверу', 'error');
                this.disabled = false;
                this.innerHTML = originalHtml;
            }
        });

        document.getElementById('editSuccessOkBtn').addEventListener('click', () => {
            window.location.href = '/picture/{{ $picture->id }}';
        });
    });
</script>
@endsection
