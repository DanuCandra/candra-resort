@extends('layouts.guest')

@section('title', 'Tentang Kami · Candra Resort')

@section('content')
    @php
        $hero = $contents->get('about_hero');
        $story = $contents->get('about_story');
        $values = $contents->get('about_values');
        $experience = $contents->get('about_video');
        $valueItems = collect(preg_split('/\r\n|\r|\n/', $values?->content ?? "Kamar nyaman dan terawat\nLayanan tamu selama menginap\nFood & Beverage\nProses reservasi yang mudah"))->filter();
        $registeredKeys = array_keys(\App\Support\WebsiteContentRegistry::slots());
        $additionalContents = $contents->reject(fn ($item, $key) => in_array($key, $registeredKeys, true));
    @endphp

    <section id="page-top" class="page-hero" style="background-image:url('{{ $hero?->image_path ? Storage::url($hero->image_path) : asset('landing-lage/img/hero/hero-3.jpg') }}')">
        <div class="container text-center">
            <h1>{{ $hero?->title ?? 'Tentang Candra Resort' }}</h1>
            <p>{{ $hero?->content ?? 'Keramahtamahan yang terasa seperti rumah.' }}</p>
        </div>
    </section>

    <section class="aboutus-page-section about-content-spaced" id="about-story">
        <div class="container">
            <div class="about-page-text">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="ap-title">
                            <h2>{{ $story?->title ?? 'Selamat Datang di Candra Resort' }}</h2>
                            <p>{{ $story?->content ?? 'Candra Resort dibangun untuk menghadirkan tempat istirahat yang tenang dengan pelayanan personal, fasilitas lengkap, dan suasana yang menghubungkan tamu dengan pengalaman terbaik di sekitarnya.' }}</p>
                        </div>
                        @if ($story?->image_path)
                            <img src="{{ Storage::url($story->image_path) }}" alt="{{ $story->title }}" class="img-fluid mt-4">
                        @endif
                    </div>
                    <div class="col-lg-5 offset-lg-1">
                        <h4 class="mb-3">{{ $values?->title ?? 'Mengapa Memilih Kami' }}</h4>
                        <ul class="ap-services">
                            @foreach ($valueItems as $value)
                                <li><i class="icon_check"></i> {{ $value }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="video-section set-bg" id="about-experience" data-setbg="{{ $experience?->image_path ? Storage::url($experience->image_path) : asset('landing-lage/img/video-bg.jpg') }}">
        <div class="container"><div class="row"><div class="col-lg-12"><div class="video-text">
            <h2>{{ $experience?->title ?? 'Temukan Hotel & Layanan Kami' }}</h2>
            <p>{{ $experience?->content ?? 'Temukan pengalaman baru bersama Candra Resort.' }}</p>
        </div></div></div></div>
    </section>

    <section class="services-section spad" id="hotel-policies">
        <div class="container">
            <div class="section-title"><span>Informasi Menginap</span><h2>Kebijakan Hotel</h2></div>
            <div class="row">
                @foreach (['check_in_policy', 'check_out_policy'] as $key)
                    @php($policy = $contents->get($key))
                    <div class="col-md-6"><div class="service-item"><i class="{{ $key === 'check_in_policy' ? 'icon_key' : 'icon_clock_alt' }}"></i><h4>{{ $policy?->title ?? ($key === 'check_in_policy' ? 'Waktu Check-in' : 'Waktu Check-out') }}</h4><p>{{ $policy?->content ?? ($key === 'check_in_policy' ? 'Check-in mulai pukul 14.00 WIB.' : 'Check-out maksimal pukul 12.00 WIB.') }}</p></div></div>
                @endforeach
            </div>

            @if ($additionalContents->isNotEmpty())
                <div class="row mt-4">
                    @foreach ($additionalContents as $additional)
                        <div class="col-lg-6 mb-4"><div class="auth-card h-100">
                            @if ($additional->image_path)<img src="{{ Storage::url($additional->image_path) }}" alt="{{ $additional->title }}" class="img-fluid mb-3">@endif
                            <h4>{{ $additional->title ?: str($additional->content_key)->replace('_', ' ')->title() }}</h4>
                            <p class="mb-0">{{ $additional->content }}</p>
                        </div></div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
