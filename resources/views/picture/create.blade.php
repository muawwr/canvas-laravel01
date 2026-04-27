@extends('layouts.app')

@section('content')

<div class="add_steps">
    <div class="add_step active" data-step-indicator="1">
        <span class="step_number">1</span>
    </div>
    <div class="step_line" data-step-line="1"></div>
    <div class="add_step" data-step-indicator="2">
        <span class="step_number">2</span>
    </div>
    <div class="step_line" data-step-line="2"></div>
    <div class="add_step" data-step-indicator="3">
        <span class="step_number">3</span>
    </div>
    <div class="step_line" data-step-line="3"></div>
    <div class="add_step" data-step-indicator="4">
        <span class="step_number">₽</span>
    </div>
    <div class="step_line end" data-step-line="4"></div>
</div>

<main class="add_main container" data-managed-add-page="custom">
    <div class="add_modal active" data-step="1">
        <div class="add_modal_content">
            <h1 class="add_title">Загрузка картины</h1>
            <p class="add_subtitle">Заполните форму и загрузите изображение, чтобы добавить <br> картину в галерею.</p>
            <div class="add_line"></div>

            <div class="add_upload_area" id="uploadArea">
                <img id="uploadPreview" class="add_upload_preview" alt="Предпросмотр картины" style="display: none;">
                <div class="upload_placeholder" id="uploadPlaceholder">
                    <div class="upload_icon">
                        <img src="{{ asset('assets/images/add/Upload.svg') }}" alt="Upload">
                    </div>
                    <p class="upload_text" id="uploadText">Перетащите файл или загрузите</p>
                </div>
                <input type="file" id="imageUpload" class="upload_input" accept="image/jpeg,image/png,image/jpg,image/webp">
            </div>
            <div class="field_error" id="imageUploadError"></div>

            <div class="add_section">
                <label class="add_label" for="pictureWidth">Размер картины</label>
                <div class="add_size_inputs">
                    <input type="number" id="pictureWidth" class="add_input" placeholder="Ширина" min="1" value="{{ old('width') }}">
                    <span class="size_separator">x</span>
                    <input type="number" id="pictureHeight" class="add_input" placeholder="Высота" min="1" value="{{ old('height') }}">
                </div>
                <div class="field_error" id="pictureWidthError"></div>
                <div class="field_error" id="pictureHeightError"></div>
            </div>

            <div class="add_section">
                <label class="add_label" for="pictureName">Название картины</label>
                <input type="text" id="pictureName" class="add_input add_input_full" placeholder="Введите название картины" maxlength="255" value="{{ old('name') }}">
                <div class="field_error" id="pictureNameError"></div>
            </div>

            <div class="add_buttons">
                <button class="add_btn add_btn_cancel" type="button" id="cancelAdd">Отмена</button>
                <button class="add_btn add_btn_next" type="button" data-next="2">
                    Следующий шаг
                    <img src="{{ asset('assets/images/add/Right.svg') }}" alt="Next">
                </button>
            </div>
        </div>
    </div>

    <div class="add_modal" data-step="2">
        <div class="add_modal_content">
            <h1 class="add_title">Шаг 2</h1>
            <p class="add_subtitle">Заполните дополнительную информацию о картине</p>
            <div class="add_line"></div>

            <div class="add_row">
                <div class="add_section add_section_half">
                    <label class="add_label" for="pictureTechnique">Техника написания</label>
                    <input type="text" id="pictureTechnique" class="add_input" placeholder="Гуашь, пастель" maxlength="255" value="{{ old('technique') }}">
                    <div class="field_error" id="pictureTechniqueError"></div>
                </div>
                <div class="add_section add_section_half">
                    <label class="add_label" for="pictureYear">Год написания</label>
                    <input type="number" id="pictureYear" class="add_input" placeholder="ГГГГ" min="1000" max="{{ date('Y') }}" value="{{ old('year') }}">
                    <div class="field_error" id="pictureYearError"></div>
                </div>
            </div>

            <div class="add_section">
                <label class="add_label" for="pictureDescription">Описание картины</label>
                <textarea id="pictureDescription" class="add_textarea" placeholder="Введите описание картины">{{ old('description') }}</textarea>
                <div class="field_error" id="pictureDescriptionError"></div>
            </div>

            <div class="add_buttons">
                <button class="add_btn add_btn_back" type="button" data-prev="1">
                    <img src="{{ asset('assets/images/add/Left.svg') }}" alt="Back">
                    Назад
                </button>
                <button class="add_btn add_btn_next" type="button" data-next="3">
                    Следующий шаг
                    <img src="{{ asset('assets/images/add/Right.svg') }}" alt="Next">
                </button>
            </div>
        </div>
    </div>

    <div class="add_modal" data-step="3">
        <div class="add_modal_content">
            <h1 class="add_title">Шаг 3</h1>
            <p class="add_subtitle">Заполните данные о стиле, жанре и временном периоде</p>
            <div class="add_line"></div>

            <div class="add_section">
                <label class="add_label">Жанр</label>
                <div class="custom_select" data-select="genre_id">
                    <input type="hidden" id="genreId" value="{{ old('genre_id') }}">
                    <div class="custom_select_trigger">
                        <span class="custom_select_text">Выберите жанр вашей картины</span>
                        <img src="{{ asset('assets/images/add/Arrow.svg') }}" alt="Arrow" class="select_arrow">
                    </div>
                    <div class="custom_select_options">
                        @forelse($genres as $genre)
                            <button class="custom_select_option" type="button" data-value="{{ $genre->id }}">{{ $genre->name }}</button>
                        @empty
                            <button class="custom_select_option" type="button" disabled>Жанры не добавлены</button>
                        @endforelse
                    </div>
                </div>
                <div class="field_error" id="genreIdError"></div>
            </div>

            <div class="add_section">
                <label class="add_label">Стиль</label>
                <div class="custom_select" data-select="style_id">
                    <input type="hidden" id="styleId" value="{{ old('style_id') }}">
                    <div class="custom_select_trigger">
                        <span class="custom_select_text">Выберите стиль написания</span>
                        <img src="{{ asset('assets/images/add/Arrow.svg') }}" alt="Arrow" class="select_arrow">
                    </div>
                    <div class="custom_select_options">
                        @forelse($styles as $style)
                            <button class="custom_select_option" type="button" data-value="{{ $style->id }}">{{ $style->name }}</button>
                        @empty
                            <button class="custom_select_option" type="button" disabled>Стили не добавлены</button>
                        @endforelse
                    </div>
                </div>
                <div class="field_error" id="styleIdError"></div>
            </div>

            <div class="add_section">
                <label class="add_label">Эпоха</label>
                <div class="custom_select" data-select="era_id">
                    <input type="hidden" id="eraId" value="{{ old('era_id') }}">
                    <div class="custom_select_trigger">
                        <span class="custom_select_text">Выберите эпоху картины</span>
                        <img src="{{ asset('assets/images/add/Arrow.svg') }}" alt="Arrow" class="select_arrow">
                    </div>
                    <div class="custom_select_options">
                        @forelse($eras as $era)
                            <button class="custom_select_option" type="button" data-value="{{ $era->id }}">{{ $era->name }}</button>
                        @empty
                            <button class="custom_select_option" type="button" disabled>Эпохи не добавлены</button>
                        @endforelse
                    </div>
                </div>
                <div class="field_error" id="eraIdError"></div>
            </div>

            <div class="add_buttons">
                <button class="add_btn add_btn_back" type="button" data-prev="2">
                    <img src="{{ asset('assets/images/add/Left.svg') }}" alt="Back">
                    Назад
                </button>
                <button class="add_btn add_btn_next" type="button" data-next="4">
                    Следующий шаг
                    <img src="{{ asset('assets/images/add/Right.svg') }}" alt="Next">
                </button>
            </div>
        </div>
    </div>

    <div class="add_modal" data-step="4">
        <div class="add_modal_content">
            <h1 class="add_title">Расчет стоимости</h1>
            <p class="add_subtitle">Заполните данные о желаемой цене</p>
            <div class="add_line"></div>

            <div class="add_section">
                <div class="add_price_header">
                    <label class="add_label" for="priceYouGet">Цена, ₽</label>
                    <span class="add_price_hint">Сколько вы получите</span>
                </div>
                <input type="number" class="add_input add_input_full" placeholder="Введите цену вашей картины" id="priceYouGet" min="100" value="{{ old('price') }}">
                <div class="field_error" id="priceYouGetError"></div>
            </div>

            <div class="add_section">
                <div class="add_price_header">
                    <label class="add_label" for="priceBuyerPays">Цена, ₽</label>
                    <span class="add_price_hint">Сколько заплатит покупатель (с комиссией 7%)</span>
                </div>
                <input type="number" class="add_input add_input_full" placeholder="Введите сумму для покупателя" id="priceBuyerPays" min="107">
            </div>

            <div class="add_agreement">
                <label class="add_checkbox">
                    <input type="checkbox" id="agreeTerms">
                    <span class="checkbox_custom"></span>
                    <span class="checkbox_text">Я принимаю условия <a href="#" class="agreement_link">Соглашение пользователя КАНВАС</a></span>
                </label>
                <div class="field_error" id="agreeTermsError"></div>
            </div>

            <div class="add_buttons">
                <button class="add_btn add_btn_back" type="button" data-prev="3">
                    <img src="{{ asset('assets/images/add/Left.svg') }}" alt="Back">
                    Назад
                </button>
                <button class="add_btn add_btn_submit" type="button" id="submitPicture">
                    Выставить на продажу
                </button>
            </div>
        </div>
    </div>

    <div class="success_modal_overlay" id="successModal">
        <div class="success_modal">
            <div class="success_icon">
                <img src="{{ asset('assets/images/add/Success.svg') }}" alt="Success">
            </div>
            <h2 class="success_title">Ваша картина будет выставлена<br>в галерею после модерации</h2>
            <button class="success_btn" id="successOkBtn" type="button">OK!</button>
        </div>
    </div>
</main>

@endsection

@section('scripts')
<style>
.add_upload_area.drag-over,
.add_upload_area.uploaded {
    border-color: #fbff83;
    background-color: rgba(251, 255, 131, 0.05);
}

.add_upload_preview {
    width: 100%;
    max-height: 320px;
    object-fit: contain;
    border-radius: 12px;
}

.upload_placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 15px;
}

.field_invalid {
    border-color: #ff6b6b !important;
}

.custom_select.field_invalid .custom_select_trigger {
    border-color: #ff6b6b !important;
}

.field_error {
    min-height: 18px;
    margin-top: 6px;
    font-size: 13px;
    line-height: 1.35;
    color: #ff7f7f;
}
</style>

<script>
(() => {
    const currentYear = new Date().getFullYear();
    const commissionRate = 0.07;
    const addPictureUrl = "{{ url('/api/picture/add') }}";
    const accountUrl = "{{ url('/account') }}";
    const formState = {
        image: null,
        genre_id: Number(document.getElementById('genreId')?.value || 0),
        style_id: Number(document.getElementById('styleId')?.value || 0),
        era_id: Number(document.getElementById('eraId')?.value || 0),
    };

    const modals = Array.from(document.querySelectorAll('.add_modal'));
    const stepIndicators = Array.from(document.querySelectorAll('.add_step'));
    const stepLines = Array.from(document.querySelectorAll('.step_line'));
    const successModal = document.getElementById('successModal');
    const uploadArea = document.getElementById('uploadArea');
    const imageUpload = document.getElementById('imageUpload');
    const uploadPreview = document.getElementById('uploadPreview');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const uploadText = document.getElementById('uploadText');
    const priceYouGet = document.getElementById('priceYouGet');
    const priceBuyerPays = document.getElementById('priceBuyerPays');
    const submitButton = document.getElementById('submitPicture');
    const successOkBtn = document.getElementById('successOkBtn');

    const fields = {
        width: document.getElementById('pictureWidth'),
        height: document.getElementById('pictureHeight'),
        name: document.getElementById('pictureName'),
        technique: document.getElementById('pictureTechnique'),
        year: document.getElementById('pictureYear'),
        description: document.getElementById('pictureDescription'),
        agreeTerms: document.getElementById('agreeTerms'),
    };

    function clearInvalidState() {
        document.querySelectorAll('.field_invalid').forEach((element) => {
            element.classList.remove('field_invalid');
        });

        document.querySelectorAll('.field_error').forEach((element) => {
            element.textContent = '';
        });
    }

    function markInvalid(element, errorId, message) {
        if (element) {
            element.classList.add('field_invalid');
        }

        if (errorId) {
            const errorElement = document.getElementById(errorId);
            if (errorElement) {
                errorElement.textContent = message;
            }
        }
    }

    function getStepModal(step) {
        return document.querySelector(`.add_modal[data-step="${step}"]`);
    }

    function updateStepIndicators(step) {
        stepIndicators.forEach((indicator, index) => {
            indicator.classList.toggle('active', index + 1 <= step);
        });

        stepLines.forEach((line, index) => {
            line.classList.toggle('active', index + 1 < step);
        });
    }

    function changeStep(targetStep) {
        const activeModal = document.querySelector('.add_modal.active');
        const currentStep = Number(activeModal?.dataset.step || 1);
        const nextModal = getStepModal(targetStep);

        if (!nextModal || currentStep === targetStep) {
            return;
        }

        if (targetStep > currentStep && !validateStep(currentStep)) {
            return;
        }

        modals.forEach((modal) => {
            if (modal !== activeModal && modal !== nextModal) {
                modal.classList.remove('active', 'slide-out-left', 'slide-out-right');
            }
        });

        activeModal.classList.remove('active');
        activeModal.classList.add(targetStep > currentStep ? 'slide-out-left' : 'slide-out-right');
        nextModal.classList.remove('slide-out-left', 'slide-out-right');
        nextModal.classList.add('active');
        updateStepIndicators(targetStep);
    }

    function handleImage(file) {
        if (!file) {
            return;
        }

        clearInvalidState();

        if (!/^image\/(jpeg|jpg|png|webp)$/.test(file.type)) {
            markInvalid(uploadArea, 'imageUploadError', 'Допустимы только изображения JPEG, PNG, JPG и WEBP');
            return;
        }

        if (file.size > 10 * 1024 * 1024) {
            markInvalid(uploadArea, 'imageUploadError', 'Размер файла не должен превышать 10 МБ');
            return;
        }

        formState.image = file;
        uploadArea.classList.add('uploaded');
        uploadArea.classList.remove('field_invalid');
        uploadPlaceholder.style.display = 'none';
        uploadPreview.style.display = 'block';
        uploadText.textContent = file.name;
        document.getElementById('imageUploadError').textContent = '';

        const reader = new FileReader();
        reader.onload = (event) => {
            uploadPreview.src = event.target.result;
        };
        reader.readAsDataURL(file);
    }

    function validateStep(step) {
        clearInvalidState();

        if (step === 1) {
            if (!formState.image) {
                markInvalid(uploadArea, 'imageUploadError', 'Загрузите изображение картины');
                return false;
            }

            const width = Number(fields.width.value);
            const height = Number(fields.height.value);
            const name = fields.name.value.trim();

            if (!width || width < 1) {
                markInvalid(fields.width, 'pictureWidthError', 'Укажите корректную ширину картины');
                return false;
            }

            if (!height || height < 1) {
                markInvalid(fields.height, 'pictureHeightError', 'Укажите корректную высоту картины');
                return false;
            }

            if (name.length < 3) {
                markInvalid(fields.name, 'pictureNameError', 'Название должно содержать минимум 3 символа');
                return false;
            }

            return true;
        }

        if (step === 2) {
            const technique = fields.technique.value.trim();
            const year = Number(fields.year.value);
            const description = fields.description.value.trim();

            if (technique.length < 2) {
                markInvalid(fields.technique, 'pictureTechniqueError', 'Укажите технику написания');
                return false;
            }

            if (!year || year < 1000 || year > currentYear) {
                markInvalid(fields.year, 'pictureYearError', `Укажите корректный год от 1000 до ${currentYear}`);
                return false;
            }

            if (description.length < 10) {
                markInvalid(fields.description, 'pictureDescriptionError', 'Описание должно содержать минимум 10 символов');
                return false;
            }

            return true;
        }

        if (step === 3) {
            const genreSelect = document.querySelector('.custom_select[data-select="genre_id"]');
            const styleSelect = document.querySelector('.custom_select[data-select="style_id"]');
            const eraSelect = document.querySelector('.custom_select[data-select="era_id"]');

            if (!formState.genre_id) {
                markInvalid(genreSelect, 'genreIdError', 'Выберите жанр');
                return false;
            }

            if (!formState.style_id) {
                markInvalid(styleSelect, 'styleIdError', 'Выберите стиль');
                return false;
            }

            if (!formState.era_id) {
                markInvalid(eraSelect, 'eraIdError', 'Выберите эпоху');
                return false;
            }

            return true;
        }

        if (step === 4) {
            const sellerPrice = Number(priceYouGet.value);

            if (!sellerPrice || sellerPrice < 100) {
                markInvalid(priceYouGet, 'priceYouGetError', 'Минимальная цена картины 100 ₽');
                return false;
            }

            if (!fields.agreeTerms.checked) {
                markInvalid(fields.agreeTerms, 'agreeTermsError', 'Подтвердите согласие с условиями');
                return false;
            }

            return true;
        }

        return true;
    }

    function updateBuyerPriceFromSeller() {
        const sellerPrice = Number(priceYouGet.value);

        if (!sellerPrice) {
            priceBuyerPays.value = '';
            return;
        }

        priceBuyerPays.value = Math.ceil(sellerPrice * (1 + commissionRate));
    }

    function updateSellerPriceFromBuyer() {
        const buyerPrice = Number(priceBuyerPays.value);

        if (!buyerPrice) {
            priceYouGet.value = '';
            return;
        }

        priceYouGet.value = Math.floor(buyerPrice / (1 + commissionRate));
    }

    async function submitPicture() {
        if (!validateStep(4)) {
            return;
        }

        const formData = new FormData();
        formData.append('image', formState.image);
        formData.append('width', fields.width.value);
        formData.append('height', fields.height.value);
        formData.append('name', fields.name.value.trim());
        formData.append('technique', fields.technique.value.trim());
        formData.append('year', fields.year.value);
        formData.append('description', fields.description.value.trim());
        formData.append('genre_id', formState.genre_id);
        formData.append('style_id', formState.style_id);
        formData.append('era_id', formState.era_id);
        formData.append('price', priceYouGet.value);

        submitButton.disabled = true;
        submitButton.textContent = 'Отправка...';

        try {
            const response = await fetch(addPictureUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                markInvalid(null, 'agreeTermsError', result.message || 'Не удалось добавить картину');
                submitButton.disabled = false;
                submitButton.textContent = 'Выставить на продажу';
                return;
            }

            successModal.classList.add('show');
        } catch (error) {
            markInvalid(null, 'agreeTermsError', 'Ошибка сервера. Попробуйте еще раз.');
            submitButton.disabled = false;
            submitButton.textContent = 'Выставить на продажу';
        }
    }

    document.querySelectorAll('[data-next]').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();

            const activeModal = document.querySelector('.add_modal.active');
            const currentStep = Number(activeModal?.dataset.step || 1);
            const nextStep = Number(button.dataset.next);

            if (!validateStep(currentStep)) {
                return;
            }

            changeStep(nextStep);
        });
    });

    document.querySelectorAll('[data-prev]').forEach((button) => {
        button.addEventListener('click', () => {
            changeStep(Number(button.dataset.prev));
        });
    });

    document.getElementById('cancelAdd').addEventListener('click', () => {
        window.location.href = accountUrl;
    });

    imageUpload.addEventListener('change', (event) => handleImage(event.target.files[0]));

    Object.values(fields).forEach((field) => {
        if (!field) {
            return;
        }

        const eventName = field.type === 'checkbox' ? 'change' : 'input';
        field.addEventListener(eventName, () => {
            field.classList.remove('field_invalid');

            const errorElement = document.getElementById(`${field.id}Error`);
            if (errorElement) {
                errorElement.textContent = '';
            }
        });
    });

    uploadArea.addEventListener('dragover', (event) => {
        event.preventDefault();
        uploadArea.classList.add('drag-over');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('drag-over');
    });

    uploadArea.addEventListener('drop', (event) => {
        event.preventDefault();
        uploadArea.classList.remove('drag-over');
        handleImage(event.dataTransfer.files[0]);
    });

    document.querySelectorAll('.custom_select').forEach((select) => {
        const trigger = select.querySelector('.custom_select_trigger');
        const text = select.querySelector('.custom_select_text');
        const options = select.querySelectorAll('.custom_select_option:not([disabled])');
        const fieldName = select.dataset.select;
        const hiddenInput = select.querySelector('input[type="hidden"]');

        trigger.addEventListener('click', () => {
            document.querySelectorAll('.custom_select').forEach((item) => {
                if (item !== select) {
                    item.classList.remove('open');
                }
            });
            select.classList.toggle('open');
        });

        options.forEach((option) => {
            const value = Number(option.dataset.value);

            option.addEventListener('click', () => {
                formState[fieldName] = value;
                select.classList.add('has-value');
                select.classList.remove('field_invalid');
                text.textContent = option.textContent.trim();
                select.classList.remove('open');

                if (hiddenInput) {
                    hiddenInput.value = value;
                    const errorElement = document.getElementById(`${hiddenInput.id}Error`);
                    if (errorElement) {
                        errorElement.textContent = '';
                    }
                }

                options.forEach((item) => item.classList.remove('selected'));
                option.classList.add('selected');
            });

            if (value === formState[fieldName]) {
                option.classList.add('selected');
                select.classList.add('has-value');
                text.textContent = option.textContent.trim();
            }
        });
    });

    document.addEventListener('click', (event) => {
        if (!event.target.closest('.custom_select')) {
            document.querySelectorAll('.custom_select').forEach((select) => {
                select.classList.remove('open');
            });
        }
    });

    priceYouGet.addEventListener('input', updateBuyerPriceFromSeller);
    priceBuyerPays.addEventListener('input', updateSellerPriceFromBuyer);
    submitButton.addEventListener('click', submitPicture);

    successOkBtn.addEventListener('click', () => {
        window.location.href = accountUrl;
    });

    updateStepIndicators(1);
    updateBuyerPriceFromSeller();
})();
</script>
@endsection
