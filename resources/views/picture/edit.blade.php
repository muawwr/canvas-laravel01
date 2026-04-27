@extends('layouts.app')

@section('content')
<main class="add_main container">
    <h1 class="admin_title">Редактирование картины</h1>
    <div class="step_fields" style="max-width: 600px; margin: 0 auto;">
        <div class="field_group">
            <label class="field_label">Изображение</label>
            <div class="upload_area" id="uploadArea" style="max-height: 300px;">
                <img id="previewImage" class="upload_preview" src="{{ asset($picture->img) }}" style="display: block; max-height: 280px; object-fit: contain;">
                <input type="file" id="imageInput" accept="image/jpeg,image/png,image/webp" style="display: none;">
            </div>
        </div>
        <div class="field_row">
            <div class="field_group">
                <label class="field_label">Ширина (см)</label>
                <input type="number" class="field_input" id="widthInput" value="{{ $picture->width }}" min="1">
            </div>
            <div class="field_group">
                <label class="field_label">Высота (см)</label>
                <input type="number" class="field_input" id="heightInput" value="{{ $picture->height }}" min="1">
            </div>
        </div>
        <div class="field_group">
            <label class="field_label">Название</label>
            <input type="text" class="field_input" id="nameInput" value="{{ $picture->name }}" maxlength="255">
        </div>
        <div class="field_group">
            <label class="field_label">Техника</label>
            <input type="text" class="field_input" id="techniqueInput" value="{{ $picture->technique }}" maxlength="255">
        </div>
        <div class="field_group">
            <label class="field_label">Год</label>
            <input type="number" class="field_input" id="yearInput" value="{{ $picture->year }}" min="1000" max="{{ date('Y') }}">
        </div>
        <div class="field_group">
            <label class="field_label">Описание</label>
            <textarea class="field_textarea" id="descriptionInput" rows="4" maxlength="5000">{{ $picture->description }}</textarea>
        </div>
        <div class="field_group">
            <label class="field_label">Жанр</label>
            <select class="field_input" id="genreSelect">
                @foreach($genres as $genre)
                <option value="{{ $genre->id }}" {{ $picture->genre_id == $genre->id ? 'selected' : '' }}>{{ $genre->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="field_group">
            <label class="field_label">Стиль</label>
            <select class="field_input" id="styleSelect">
                @foreach($styles as $style)
                <option value="{{ $style->id }}" {{ $picture->style_id == $style->id ? 'selected' : '' }}>{{ $style->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="field_group">
            <label class="field_label">Эпоха</label>
            <select class="field_input" id="eraSelect">
                @foreach($eras as $era)
                <option value="{{ $era->id }}" {{ $picture->era_id == $era->id ? 'selected' : '' }}>{{ $era->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="field_group">
            <label class="field_label">Цена (₽)</label>
            <input type="number" class="field_input" id="priceInput" value="{{ $picture->price }}" min="100">
        </div>
        <div class="wizard_buttons" style="margin-top: 20px;">
            <a href="{{ url('/picture/' . $picture->id) }}" class="wizard_btn wizard_btn_back"><img src="{{ asset('assets/images/add/Left.svg') }}" alt="Back"> Назад</a>
            <button class="wizard_btn wizard_btn_next" id="saveBtn">Сохранить <img src="{{ asset('assets/images/add/Right.svg') }}" alt="Save"></button>
        </div>
    </div>
</main>

@endsection

@section('scripts')
<style>
@keyframes slideIn { from { transform: translateX(400px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
</style>
<script>
let newImageFile = null;

function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    const icon = type === 'success' ? '✓' : '✗';
    toast.innerHTML = `<span style="font-size:20px">${icon}</span><span>${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

document.getElementById('uploadArea').addEventListener('click', () => document.getElementById('imageInput').click());
document.getElementById('imageInput').addEventListener('change', function(e) {
    if (e.target.files.length) {
        newImageFile = e.target.files[0];
        const reader = new FileReader();
        reader.onload = e => { document.getElementById('previewImage').src = e.target.result; };
        reader.readAsDataURL(newImageFile);
    }
});

document.getElementById('saveBtn').addEventListener('click', async function() {
    this.disabled = true;
    this.textContent = 'Сохранение...';
    const formData = new FormData();
    formData.append('picture_id', {{ $picture->id }});
    formData.append('width', document.getElementById('widthInput').value);
    formData.append('height', document.getElementById('heightInput').value);
    formData.append('name', document.getElementById('nameInput').value);
    formData.append('technique', document.getElementById('techniqueInput').value);
    formData.append('year', document.getElementById('yearInput').value);
    formData.append('description', document.getElementById('descriptionInput').value);
    formData.append('genre_id', document.getElementById('genreSelect').value);
    formData.append('style_id', document.getElementById('styleSelect').value);
    formData.append('era_id', document.getElementById('eraSelect').value);
    formData.append('price', document.getElementById('priceInput').value);
    if (newImageFile) formData.append('image', newImageFile);
    
    try {
        const response = await fetch('/api/picture/edit', { method: 'POST', body: formData, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
        const result = await response.json();
        if (result.success) { showToast('Картина обновлена'); setTimeout(() => window.location.href = '/picture/{{ $picture->id }}', 1500); }
        else { showToast(result.message || 'Ошибка', 'error'); this.disabled = false; this.textContent = 'Сохранить'; }
    } catch (error) { showToast('Ошибка сервера', 'error'); this.disabled = false; this.textContent = 'Сохранить'; }
});
</script>
@endsection
