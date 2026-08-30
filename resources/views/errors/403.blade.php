@extends('layouts.guest')
@section('title', 'Akses Ditolak · Candra Resort')
@section('content')
    <section class="auth-section"><div class="container text-center"><div class="section-title"><span>403</span><h2>Akses Ditolak</h2></div><p>Anda tidak memiliki izin untuk membuka halaman ini.</p><a href="{{ auth()->check() ? route(auth()->user()->dashboardRouteName()) : route('home') }}" class="sona-button d-inline-block">Kembali</a></div></section>
@endsection
