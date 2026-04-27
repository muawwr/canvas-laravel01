@extends('layouts.app')

@section('content')
<main class="admin_main container">
    <h1 class="admin_title">Админ-панель</h1>
    
    <div class="admin_tabs">
        <div class="admin_tab active" data-tab="moderation">Модерация <span class="admin_badge">{{ $pending_pictures->count() }}</span></div>
        <div class="admin_tab" data-tab="deals">Сделки <span class="admin_badge">{{ $deals->count() }}</span></div>
        <div class="admin_tab" data-tab="users">Пользователи <span class="admin_badge">{{ $users->count() }}</span></div>
        <div class="admin_tab" data-tab="categories">Категории</div>
    </div>
    
    <!-- Модерация -->
    <div class="admin_content active" data-content="moderation">
        @if($pending_pictures->isEmpty())
            <div class="admin_empty">
                <p>Нет картин для модерации</p>
            </div>
        @else
            @foreach($pending_pictures as $picture)
            <div class="moderate_card" id="moderateCard_{{ $picture->id }}">
                <div class="moderate_image">
                    <img src="{{ asset($picture->img) }}" alt="{{ $picture->name }}">
                </div>
                <div class="moderate_info">
                    <h3>{{ $picture->name }}</h3>
                    <p class="moderate_author">{{ $picture->user->name }}</p>
                    <p class="moderate_desc">{{ Str::limit($picture->description, 100) }}</p>
                    <p class="moderate_price">{{ number_format($picture->price, 0, '.', ' ') }} ₽</p>
                </div>
                <div class="moderate_actions">
                    <button class="moderate_btn moderate_approve" onclick="moderatePicture({{ $picture->id }}, 'approve')">
                        <img src="{{ asset('assets/images/admin/approve.svg') }}" alt="Одобрить">
                    </button>
                    <button class="moderate_btn moderate_reject" onclick="moderatePicture({{ $picture->id }}, 'reject')">
                        <img src="{{ asset('assets/images/admin/reject.svg') }}" alt="Отклонить">
                    </button>
                </div>
            </div>
            @endforeach
        @endif
    </div>
    
    <!-- Сделки -->
    <div class="admin_content" data-content="deals" style="display: none;">
        @if($deals->isEmpty())
            <div class="admin_empty"><p>Нет сделок</p></div>
        @else
            <div class="deals_table">
                <div class="deals_header">
                    <span>Картина</span>
                    <span>Продавец</span>
                    <span>Покупатель</span>
                    <span>Цена</span>
                    <span>Дата</span>
                </div>
                @foreach($deals as $deal)
                <div class="deal_row">
                    <span>{{ $deal->picture->name }}</span>
                    <span>{{ $deal->seller->name }}</span>
                    <span>{{ $deal->buyer->name }}</span>
                    <span>{{ number_format($deal->price, 0, '.', ' ') }} ₽</span>
                    <span>{{ $deal->created_at->format('d.m.Y') }}</span>
                </div>
                @endforeach
            </div>
        @endif
    </div>
    
    <!-- Пользователи -->
    <div class="admin_content" data-content="users" style="display: none;">
        <div class="users_list">
            @foreach($users as $user)
            <div class="user_card_admin">
                <img src="{{ asset($user->img) }}" alt="{{ $user->name }}" class="user_avatar_admin">
                <div class="user_info_admin">
                    <h4>{{ $user->name }}</h4>
                    <p>{{ $user->email }}</p>
                </div>
                <span class="user_role_admin">{{ $user->role == 2 ? 'Админ' : 'Пользователь' }}</span>
                <span class="user_date_admin">{{ $user->date_of_reg ? \Carbon\Carbon::parse($user->date_of_reg)->format('d.m.Y') : '-' }}</span>
            </div>
            @endforeach
        </div>
    </div>
    
    <!-- Категории -->
    <div class="admin_content" data-content="categories" style="display: none;">
        <div class="categories_grid">
            <!-- Жанры -->
            <div class="category_section">
                <h3 class="category_section_title">Жанры</h3>
                <div class="category_add">
                    <input type="text" class="category_input" id="genreInput" placeholder="Новый жанр">
                    <button class="category_add_btn" onclick="addCategory('genre', 'genreInput')">+</button>
                </div>
                <div class="category_list" id="genreList">
                    @foreach($genres as $genre)
                    <div class="category_item" id="genre_{{ $genre->id }}">
                        <span>{{ $genre->name }}</span>
                        <button class="category_delete_btn" onclick="deleteCategory('genre', {{ $genre->id }})">✕</button>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Стили -->
            <div class="category_section">
                <h3 class="category_section_title">Стили</h3>
                <div class="category_add">
                    <input type="text" class="category_input" id="styleInput" placeholder="Новый стиль">
                    <button class="category_add_btn" onclick="addCategory('style', 'styleInput')">+</button>
                </div>
                <div class="category_list" id="styleList">
                    @foreach($styles as $style)
                    <div class="category_item" id="style_{{ $style->id }}">
                        <span>{{ $style->name }}</span>
                        <button class="category_delete_btn" onclick="deleteCategory('style', {{ $style->id }})">✕</button>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Эпохи -->
            <div class="category_section">
                <h3 class="category_section_title">Эпохи</h3>
                <div class="category_add">
                    <input type="text" class="category_input" id="eraInput" placeholder="Новая эпоха">
                    <button class="category_add_btn" onclick="addCategory('era', 'eraInput')">+</button>
                </div>
                <div class="category_list" id="eraList">
                    @foreach($eras as $era)
                    <div class="category_item" id="era_{{ $era->id }}">
                        <span>{{ $era->name }}</span>
                        <button class="category_delete_btn" onclick="deleteCategory('era', {{ $era->id }})">✕</button>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</main>

@endsection

@section('scripts')
<style>
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
    setTimeout(() => toast.remove(), 3000);
}

// Tabs
document.querySelectorAll('.admin_tab').forEach(tab => {
    tab.addEventListener('click', function() {
        const target = this.getAttribute('data-tab');
        document.querySelectorAll('.admin_tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        document.querySelectorAll('.admin_content').forEach(c => { c.style.display = 'none'; c.classList.remove('active'); });
        const content = document.querySelector(`[data-content="${target}"]`);
        if (content) { content.style.display = 'flex'; content.classList.add('active'); }
    });
});

async function moderatePicture(id, action) {
    const formData = new FormData();
    formData.append('picture_id', id);
    formData.append('action', action);
    try {
        const response = await fetch('/api/picture/moderate', { method: 'POST', body: formData, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
        const result = await response.json();
        if (result.success) {
            showToast(result.message);
            document.getElementById(`moderateCard_${id}`)?.remove();
        } else { showToast(result.message || 'Ошибка', 'error'); }
    } catch (error) { showToast('Ошибка сервера', 'error'); }
}

async function addCategory(type, inputId) {
    const input = document.getElementById(inputId);
    const name = input.value.trim();
    if (!name) { showToast('Введите название', 'error'); return; }
    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('type', type);
    formData.append('name', name);
    try {
        const response = await fetch('/api/categories', { method: 'POST', body: formData, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
        const result = await response.json();
        if (result.success) {
            showToast('Категория добавлена');
            input.value = '';
            const list = document.getElementById(`${type}List`);
            const item = document.createElement('div');
            item.className = 'category_item';
            item.id = `${type}_${result.id}`;
            item.innerHTML = `<span>${name}</span><button class="category_delete_btn" onclick="deleteCategory('${type}', ${result.id})">✕</button>`;
            list.appendChild(item);
        } else { showToast(result.message || 'Ошибка', 'error'); }
    } catch (error) { showToast('Ошибка сервера', 'error'); }
}

async function deleteCategory(type, id) {
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('type', type);
    formData.append('id', id);
    try {
        const response = await fetch('/api/categories', { method: 'POST', body: formData, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
        const result = await response.json();
        if (result.success) {
            showToast('Категория удалена');
            document.getElementById(`${type}_${id}`)?.remove();
        } else { showToast(result.message || 'Ошибка', 'error'); }
    } catch (error) { showToast('Ошибка сервера', 'error'); }
}
</script>
@endsection
