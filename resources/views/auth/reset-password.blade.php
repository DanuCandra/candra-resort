@extends('layouts.guest')
@section('title', 'Reset Password · Candra Resort')
@section('content')
    <section class="auth-section"><div class="container"><div class="row justify-content-center"><div class="col-lg-5 col-md-7"><div class="auth-card">
        <div class="section-title text-center"><span>Keamanan Akun</span><h2>Reset Password</h2></div>
        @include('partials.validation-errors')
        <form action="{{ route('password.update') }}" method="POST" class="sona-form">@csrf<input type="hidden" name="token" value="{{ $token }}"><div class="form-group"><label for="email">Email</label><input type="email" class="form-control" id="email" name="email" value="{{ old('email', $email) }}" required></div><div class="form-group"><label for="password">Password Baru</label><input type="password" class="form-control" id="password" name="password" required></div><div class="form-group"><label for="password_confirmation">Konfirmasi Password</label><input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required></div><button type="submit" class="sona-button w-100">Simpan Password Baru</button></form>
    </div></div></div></div></section>
@endsection
