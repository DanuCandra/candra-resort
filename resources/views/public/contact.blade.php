@extends('layouts.guest')

@section('title', 'Kontak · Candra Resort')

@section('content')
    @php
        $hero = $contents->get('contact_hero');
        $intro = $contents->get('contact_intro');
        $reservationInfo = $contents->get('contact_reservation');
    @endphp

    <section id="page-top" class="page-hero" style="background-image:url('{{ $hero?->image_path ? Storage::url($hero->image_path) : asset('landing-lage/img/hero/hero-1.jpg') }}')">
        <div class="container text-center"><h1>{{ $hero?->title ?? 'Hubungi Kami' }}</h1><p>{{ $hero?->content ?? 'Kami siap membantu merencanakan masa inap Anda.' }}</p></div>
    </section>

    <section class="contact-section spad" id="contact-information">
        <div class="container"><div class="row">
            <div class="col-lg-4">
                <div class="contact-text">
                    <div class="section-title"><span>Kontak</span><h2>{{ $intro?->title ?? $settings->get('hotel.name', 'Candra Resort') }}</h2></div>
                    <p>{{ $intro?->content ?? 'Hubungi tim kami untuk informasi kamar, fasilitas, atau kebutuhan khusus.' }}</p>
                    <table><tbody>
                        <tr><td class="c-o">Alamat:</td><td>{{ $settings->get('hotel.address', 'Indonesia') }}</td></tr>
                        <tr><td class="c-o">Telepon:</td><td><a href="tel:{{ preg_replace('/[^0-9+]/', '', $settings->get('hotel.phone', '')) }}">{{ $settings->get('hotel.phone', '+62 812 3456 7890') }}</a></td></tr>
                        <tr><td class="c-o">Email:</td><td><a href="mailto:{{ $settings->get('hotel.email', 'info@candraresort.test') }}">{{ $settings->get('hotel.email', 'info@candraresort.test') }}</a></td></tr>
                        <tr><td class="c-o">Check-in:</td><td>{{ $settings->get('hotel.check_in_time', '14:00') }} WIB</td></tr>
                        <tr><td class="c-o">Check-out:</td><td>{{ $settings->get('hotel.check_out_time', '12:00') }} WIB</td></tr>
                    </tbody></table>
                </div>
            </div>
            <div class="col-lg-7 offset-lg-1">
                <div class="auth-card">
                    <h4 class="mb-4">{{ $reservationInfo?->title ?? 'Informasi Reservasi' }}</h4>
                    <p>{{ $reservationInfo?->content ?? 'Pertanyaan dapat disampaikan melalui telepon atau email. Tim kami siap membantu kebutuhan masa inap Anda.' }}</p>
                    <div class="d-flex flex-wrap mt-4">
                        <a href="{{ route('public.rooms.index') }}" class="sona-button d-inline-block mr-3 mb-2">Lihat Kamar</a>
                        @if($settings->get('hotel.whatsapp'))<a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings->get('hotel.whatsapp')) }}" target="_blank" rel="noopener" class="primary-btn d-inline-block mr-3 mb-2">WhatsApp</a>@endif
                        <a href="mailto:{{ $settings->get('hotel.email', 'info@candraresort.test') }}" class="primary-btn d-inline-block mb-2">Kirim Email</a>
                    </div>
                </div>
            </div>
        </div></div>
    </section>
@endsection
