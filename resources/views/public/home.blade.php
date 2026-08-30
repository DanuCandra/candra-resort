@extends('layouts.guest')

@section('title', 'Candra Resort · Stay, Relax, Experience')

@section('content')
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="hero-text">
                        <h1>{{ $contents->get('hero_title')?->title ?? 'A Warm Escape at Candra Resort' }}</h1>
                        <p>{{ $contents->get('hero_description')?->content ?? 'Nikmati ketenangan, layanan yang hangat, dan pengalaman menginap yang dirancang untuk membuat setiap perjalanan lebih berkesan.' }}</p>
                        <a href="{{ route('public.rooms.index') }}" class="primary-btn">Jelajahi Kamar</a>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-5 offset-xl-2 offset-lg-1">
                    <div class="booking-form">
                        <h3>Cari Kamar</h3>
                        <form action="{{ route('public.rooms.index') }}" method="GET">
                            <div class="check-date">
                                <label for="check_in">Check-In</label>
                                <input type="date" id="check_in" name="check_in" min="{{ today()->format('Y-m-d') }}" value="{{ old('check_in', today()->addDay()->format('Y-m-d')) }}" class="@error('check_in') is-invalid @enderror" required>
                                @error('check_in')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                            </div>
                            <div class="check-date">
                                <label for="check_out">Check-Out</label>
                                <input type="date" id="check_out" name="check_out" min="{{ today()->addDay()->format('Y-m-d') }}" value="{{ old('check_out', today()->addDays(2)->format('Y-m-d')) }}" class="@error('check_out') is-invalid @enderror" required>
                                @error('check_out')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                            </div>
                            <div class="select-option">
                                <label for="adults">Tamu</label>
                                <select id="adults" name="adults">
                                    @foreach (range(1, 6) as $guestCount)
                                        <option value="{{ $guestCount }}" @selected((int) old('adults', 1) === $guestCount)>{{ $guestCount }} Dewasa</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit">Cek Ketersediaan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="hero-slider owl-carousel">
            @foreach (['hero-1.jpg', 'hero-2.jpg', 'hero-3.jpg'] as $hero)
                <div class="hs-item set-bg" data-setbg="{{ asset('landing-lage/img/hero/'.$hero) }}"></div>
            @endforeach
        </div>
    </section>

    <section class="aboutus-section spad">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="about-text">
                        <div class="section-title"><span>Tentang Kami</span><h2>Hospitality yang Hangat<br>di Setiap Kunjungan</h2></div>
                        <p class="f-para">{{ $contents->get('about_summary')?->content ?? 'Candra Resort menghadirkan suasana nyaman untuk liburan keluarga, perjalanan bisnis, maupun waktu tenang bersama orang terdekat.' }}</p>
                        <a href="{{ route('public.about') }}" class="primary-btn about-btn">Selengkapnya</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about-pic"><div class="row"><div class="col-sm-6"><img src="{{ asset('landing-lage/img/about/about-1.jpg') }}" alt="Suasana Candra Resort"></div><div class="col-sm-6"><img src="{{ asset('landing-lage/img/about/about-2.jpg') }}" alt="Kenyamanan Candra Resort"></div></div></div>
                </div>
            </div>
        </div>
    </section>

    <section class="services-section spad" id="facilities">
        <div class="container">
            <div class="row"><div class="col-lg-12"><div class="section-title"><span>Yang Kami Sediakan</span><h2>Fasilitas Candra Resort</h2></div></div></div>
            <div class="row">
                @forelse ($facilities as $facility)
                    <div class="col-lg-4 col-sm-6">
                        <div class="service-item"><i class="{{ $facility->icon ?: 'flaticon-026-bed' }}"></i><h4>{{ $facility->name }}</h4><p>{{ $facility->description ?: 'Fasilitas terbaik untuk melengkapi kenyamanan masa inap Anda.' }}</p></div>
                    </div>
                @empty
                    @foreach ([['flaticon-033-dinner', 'Restaurant'], ['flaticon-036-parking', 'Area Parkir'], ['flaticon-024-towel', 'Housekeeping'], ['flaticon-044-clock-1', 'Layanan 24 Jam'], ['flaticon-026-bed', 'Kamar Nyaman'], ['flaticon-012-cocktail', 'Food & Beverage']] as [$icon, $name])
                        <div class="col-lg-4 col-sm-6"><div class="service-item"><i class="{{ $icon }}"></i><h4>{{ $name }}</h4><p>Layanan berkualitas untuk pengalaman menginap yang lebih nyaman.</p></div></div>
                    @endforeach
                @endforelse
            </div>
        </div>
    </section>

    <section class="hp-room-section home-room-showcase" id="rooms">
        <div class="container-fluid">
            <div class="hp-room-items"><div class="row">
                @forelse ($roomTypes as $index => $roomType)
                    @php
                        $fallback = asset('landing-lage/img/room/room-b'.(($index % 4) + 1).'.jpg');
                        $image = $roomType->images->first()?->image_path ? asset('storage/'.$roomType->images->first()->image_path) : $fallback;
                    @endphp
                    <div class="col-lg-3 col-md-6">
                        <div class="hp-room-item set-bg" data-setbg="{{ $image }}">
                            <div class="hr-text">
                                <h3>{{ $roomType->name }}</h3>
                                <h2>Rp{{ number_format((float) $roomType->base_price, 0, ',', '.') }}<span>/malam</span></h2>
                                <table><tbody>
                                    <tr><td class="r-o">Ukuran:</td><td>{{ $roomType->room_size_sqm ? $roomType->room_size_sqm.' m²' : '-' }}</td></tr>
                                    <tr><td class="r-o">Kapasitas:</td><td>{{ $roomType->capacity }} tamu</td></tr>
                                    <tr><td class="r-o">Tempat Tidur:</td><td>{{ $roomType->bed_count }} {{ $roomType->bed_type }}</td></tr>
                                    <tr><td class="r-o">Fasilitas:</td><td>{{ $roomType->facilities->pluck('name')->take(3)->join(', ') ?: '-' }}</td></tr>
                                </tbody></table>
                                <a href="{{ route('public.rooms.show', $roomType) }}" class="primary-btn">Detail Kamar</a>
                            </div>
                        </div>
                    </div>
                @empty
                    @foreach (['Deluxe Room', 'Premium King', 'Family Room', 'Candra Suite'] as $index => $name)
                        <div class="col-lg-3 col-md-6"><div class="hp-room-item set-bg" data-setbg="{{ asset('landing-lage/img/room/room-b'.($index + 1).'.jpg') }}"><div class="hr-text"><h3>{{ $name }}</h3><h2>Segera<span>tersedia</span></h2><p class="text-white">Data kamar sedang disiapkan oleh Receptionist.</p><a href="{{ route('public.rooms.index') }}" class="primary-btn">Lihat Kamar</a></div></div></div>
                    @endforeach
                @endforelse
            </div></div>
        </div>
    </section>

    <section class="blog-section spad">
        <div class="container">
            <div class="row"><div class="col-lg-12"><div class="section-title"><span>Penawaran Terbaik</span><h2>Promosi Terbaru</h2></div></div></div>
            <div class="row">
                @forelse ($promotions as $promotion)
                    <div class="col-lg-4"><div class="blog-item set-bg" data-setbg="{{ asset('landing-lage/img/blog/blog-'.(($loop->index % 3) + 1).'.jpg') }}"><div class="bi-text"><span class="b-tag">Kode Promo: {{ $promotion->code }}</span><h4><a href="{{ route('public.promotions.index') }}">{{ $promotion->name }}</a></h4><div class="b-time"><i class="icon_clock_alt"></i> {{ $promotion->ends_at ? 'Berlaku sampai '.$promotion->ends_at->translatedFormat('d M Y') : 'Tanpa batas waktu' }}</div></div></div></div>
                @empty
                    @foreach (['Stay Longer, Save More', 'Weekend Escape', 'Family Holiday'] as $index => $name)
                        <div class="col-lg-4"><div class="blog-item set-bg" data-setbg="{{ asset('landing-lage/img/blog/blog-'.($index + 1).'.jpg') }}"><div class="bi-text"><span class="b-tag">Candra Offer</span><h4><a href="{{ route('public.promotions.index') }}">{{ $name }}</a></h4><div class="b-time"><i class="icon_clock_alt"></i> Segera hadir</div></div></div></div>
                    @endforeach
                @endforelse
            </div>
        </div>
    </section>
@endsection
