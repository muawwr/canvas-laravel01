@extends('layouts.app')

@section('content')
<main class="fav_main container">
     <div class="fav_header">
    <h1 class="fav_title">Избранное: <span class="fav_badge">{{ $favorites->count() }}</span></h1>
     </div>
    @if($favorites->isEmpty())
    <div class="empty-fav" style="text-align: center; padding: 60px 20px; color: #939393;">
        <img src="{{ asset('assets/images/header/fav.svg') }}" alt="Избранное" style="width: 80px; height: 80px; opacity: 0.3; margin-bottom: 20px;">
        <h3 style="font-size: 24px; margin-bottom: 10px;">Избранное пусто</h3>
        <p style="font-size: 16px; margin-bottom: 30px;">Добавляйте понравившиеся картины</p>
        <a href="{{ url('/gallery') }}" class="hero_btn">Перейти в галерею</a>
    </div>
    @else
    <div class="gallery-grid-masonry">
        @foreach($favorites as $fav)
        <div class="gallery-card" data-picture-id="{{ $fav->picture_id }}">
            <a href="{{ url('/picture/' . $fav->picture_id) }}">
                <img src="{{ asset($fav->picture->img) }}" alt="{{ $fav->picture->name }}">
            </a>
            <button class="fav_remove_btn" onclick="removeFav({{ $fav->picture_id }})">
                <img src="{{ asset('assets/images/cart/delete.svg') }}" alt="Удалить">
            </button>
        </div>
        @endforeach
    </div>
    @endif
</main>

@endsection

@section('scripts')
<style>
.fav_remove_btn { position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.6); border: none; border-radius: 50%; width: 36px; height: 36px; cursor: pointer; display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s; }
.gallery-card:hover .fav_remove_btn { opacity: 1; }
.fav_remove_btn img { width: 16px; height: 16px; }
.gallery-card { position: relative; }
@keyframes slideIn { from { transform: translateX(400px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
</style>
<script>
function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    const icon = type === 'success' ? '✓' : '✗';
    toast.innerHTML = `<span style="font-size:20px">${icon}</span><span>${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => { toast.style.animation = 'slideOut 0.3s ease-out forwards'; setTimeout(() => toast.remove(), 300); }, 3000);
}

function removeFav(pictureId) {
    const formData = new FormData();
    formData.append('action', 'remove');
    formData.append('picture_id', pictureId);
    
    fetch('/api/favorites', { method: 'POST', body: formData, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('Удалено из избранного');
            const card = document.querySelector(`[data-picture-id="${pictureId}"]`);
            if (card) { card.style.opacity = '0'; setTimeout(() => card.remove(), 300); }
        } else {
            showToast(data.message || 'Ошибка', 'error');
        }
    });
}
</script>
@endsection
