@extends('layouts.guest')
@section('title', 'Lupa Password · Candra Resort')
@section('content')
    <section class="auth-section"><div class="container"><div class="row justify-content-center"><div class="col-lg-5 col-md-7"><div class="auth-card">
        <div class="section-title text-center"><span>Bantuan Akun</span><h2>Lupa Password</h2></div><p class="text-center">Masukkan email akun Anda. Kami akan mengirimkan tautan reset jika email terdaftar.</p>
        @include('partials.validation-errors')
        <form action="{{ route('password.email') }}" method="POST" class="sona-form">@csrf<div class="form-group"><label for="email">Email</label><input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus></div><button type="submit" class="sona-button w-100">Kirim Tautan Reset</button></form>
        <p class="text-center mt-4 mb-0"><a href="{{ route('login') }}" style="color:#dfa974">Kembali ke halaman masuk</a></p>
    </div></div></div></div></section>
@endsection
