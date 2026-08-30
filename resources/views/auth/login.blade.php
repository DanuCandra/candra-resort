@extends('layouts.guest')
@section('title', 'Masuk · Candra Resort')
@section('content')
    <section class="auth-section"><div class="container"><div class="row justify-content-center"><div class="col-lg-5 col-md-7"><div class="auth-card">
        <div class="section-title text-center"><span>Selamat Datang</span><h2>Masuk ke Akun</h2></div>
        @include('partials.validation-errors')
        <form action="{{ route('login.store') }}" method="POST" class="sona-form">
            @csrf
            <div class="form-group"><label for="login">Email atau Username</label><input class="form-control" id="login" name="login" value="{{ old('login') }}" required autofocus autocomplete="username"></div>
            <div class="form-group"><label for="password">Password</label><input type="password" class="form-control" id="password" name="password" required autocomplete="current-password"></div>
            <div class="d-flex justify-content-between align-items-center mb-4"><label class="mb-0"><input type="checkbox" name="remember" value="1"> Ingat saya</label><a href="{{ route('password.request') }}" style="color:#dfa974">Lupa password?</a></div>
            <button type="submit" class="sona-button w-100">Masuk</button>
        </form>
        <p class="text-center mt-4 mb-0">Belum punya akun? <a href="{{ route('register') }}" style="color:#dfa974">Daftar sebagai Guest</a></p>
    </div></div></div></div></section>
@endsection
