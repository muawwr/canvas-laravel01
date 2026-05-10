@extends('layouts.app')

@section('content')
<main class="auction_workspace container notifications_screen">
    <div class="auction_workspace_title">
        <h1>Уведомления:</h1>
        <span>{{ $notifications->count() }}</span>
    </div>

    @if($notifications->isEmpty())
        <div class="auction_empty">
            <h2>Уведомлений пока нет</h2>
            <p>Когда появятся новые события по картинам, заказам и аукционам, они отобразятся здесь.</p>
        </div>
    @else
        <div class="notifications_list">
            @foreach($notifications as $notification)
                <article
                    class="notification_card"
                    data-notification-card
                    @if($notification->url) data-notification-url="{{ $notification->url }}" @endif
                >
                    <div class="notification_card_head">
                        <div class="notification_card_title_wrap">
                            <h2>{{ $notification->title }}</h2>
                        </div>
                        <time datetime="{{ optional($notification->created_at)->toIso8601String() }}">
                            {{ optional($notification->created_at)->format('d.m.Y H:i') }}
                        </time>
                    </div>
                    <p class="notification_card_text">{{ $notification->message }}</p>
                </article>
            @endforeach
        </div>
    @endif
</main>
@endsection

@section('scripts')
<script>
(() => {
    document.querySelectorAll('[data-notification-card]').forEach((card) => {
        card.addEventListener('click', () => {
            const targetUrl = card.dataset.notificationUrl || '';
            if (targetUrl) {
                window.location.href = targetUrl;
            }
        });
    });
})();
</script>
@endsection
