@extends('layouts.app')
@section('nav-home-active', 'active')

@section('content')
<main>
    <!-- Hero Section -->
    <section class="hero section-spacing">
        <div class="hero-banner">
            <div class="hero-content container">
                <div class="hero-text">
                    <p class="hero-subtitle">Безграничное творческое <br> пространство для покупки и <br> продажи искусства</p>
                </div>
                <div class="hero-title">
                    <img src="{{ asset('assets/images/banner/canvas.svg') }}" alt="">
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <div class="gallery container">
        <div class="title">
            <p class="section-subtitle">веб-платформа</p>
            <h2 class="section-title">ГАЛЕРЕЯ</h2>
            <div class="line"></div>
        </div>
        
        <div class="wrap">
            <!-- верхний текстовый блок -->
            <div class="lead">
                <div>
                    <p>
                        пространство, где коллекционирование искусства <br> становится личной историей.
                        Мы объединяем мастеров <br> и тех, кто ценит изысканность, качество и смысл <br> в каждой детали.
                    </p>
                </div>
            </div>
        

                <!-- Fallback на статические изображения если нет данных в БД -->
                <figure class="tile i1"><img src="{{ asset('assets/images/mainGallery/1.png') }}" alt=""></figure>
                <figure class="tile i2"><img src="{{ asset('assets/images/mainGallery/2.png') }}" alt=""></figure>
                <figure class="tile i3"><img src="{{ asset('assets/images/mainGallery/3.png') }}" alt=""></figure>
                <figure class="tile i4"><img src="{{ asset('assets/images/mainGallery/5.png') }}" alt=""></figure>
                <figure class="tile i5"><img src="{{ asset('assets/images/mainGallery/4.png') }}" alt=""></figure>
                <figure class="tile i6"><img src="{{ asset('assets/images/mainGallery/6.png') }}" alt=""></figure>
                <figure class="tile i7"><img src="{{ asset('assets/images/mainGallery/7.png') }}" alt=""></figure>

        </div>
    </div>

    <!-- Artists Section -->
    <section class="artists container section-spacing">
        <div class="artists-content">
            <div class="title">
                <p class="section-subtitle">популярные</p>
                <h2 class="section-title">ХУДОЖНИКИ</h2>
                <div class="line"></div>
            </div>
            
            <section class="artists container">
                <div class="artists-carousel">
                    <div class="artists-track">
                        @if($topArtists->count() > 0)
                            @foreach($topArtists as $artist)
                                <a href="{{ url('/account?user_id=' . $artist->id) }}" class="artist-card">
                                    <div class="artist-avatar">
                                        <img src="{{ asset($artist->img ?? 'assets/images/account/mainUser.png') }}" alt="{{ $artist->name }}">
                                    </div>
                                    <h3 class="artist-name">{{ $artist->name }}</h3>
                                    <p class="artist-sales">{{ $artist->sales_count }} продаж</p>
                                </a>
                            @endforeach
                        @else
                            <div class="artist-card" style="text-align: center; color: #999;">
                                <p>Пока нет художников с продажами</p>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        </div>
    </section>
</main>
@endsection
