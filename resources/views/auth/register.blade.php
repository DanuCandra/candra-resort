@extends('layouts.guest')
@section('title', 'Daftar · Candra Resort')
@section('content')
    <section class="auth-section"><div class="container"><div class="row justify-content-center"><div class="col-lg-6 col-md-8"><div class="auth-card">
        <div class="section-title text-center"><span>Guest Candra Resort</span><h2>Buat Akun</h2></div>
        @include('partials.validation-errors')
        <form action="{{ route('register.store') }}" method="POST" class="sona-form">
            @csrf
            <div class="form-group"><label for="name">Nama Lengkap</label><input class="form-control" id="name" name="name" value="{{ old('name') }}" required autocomplete="name"></div>
            <div class="form-group"><label for="email">Email</label><input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autocomplete="email"></div>
            <div class="form-group"><label for="phone">Nomor Telepon</label><input class="form-control" id="phone" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" required autocomplete="tel"></div>
            <div class="row"><div class="col-md-6"><div class="form-group"><label for="password">Password</label><input type="password" class="form-control" id="password" name="password" required autocomplete="new-password"></div></div><div class="col-md-6"><div class="form-group"><label for="password_confirmation">Konfirmasi Password</label><input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"></div></div></div>
            <button type="submit" class="sona-button w-100">Daftar</button>
        </form>
        <p class="text-center mt-4 mb-0">Sudah memiliki akun? <a href="{{ route('login') }}" style="color:#dfa974">Masuk</a></p>
    </div></div></div></div></section>
@endsection
