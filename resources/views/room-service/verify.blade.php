@extends('layouts.guest')
@section('title', 'Verifikasi Layanan Kamar')
@section('content')
    <section class="auth-section"><div class="container"><div class="row justify-content-center"><div class="col-lg-6"><div class="auth-card text-center"><div class="mb-4"><span style="display:inline-flex;width:72px;height:72px;align-items:center;justify-content:center;background:#f8efe6;color:#dfa974;border-radius:50%;font-size:32px"><i class="fa fa-qrcode"></i></span></div><p class="text-uppercase mb-2" style="letter-spacing:2px;color:#dfa974;font-weight:700">Room Service</p><h2 class="mb-2">Kamar {{ $room->room_number }}</h2><p class="text-muted mb-4">{{ $room->roomType->name }}</p>
        @if($hasActiveStay)
            <p>Masukkan nomor telepon yang dicatat oleh Receptionist saat check-in.</p><form method="POST" action="{{ route('room-service.verify.store',$room->qr_token) }}" class="sona-form text-left">@csrf<div class="form-group"><label>Nomor telepon</label><input type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" class="form-control @error('phone') is-invalid @enderror" placeholder="08xxxxxxxxxx" required>@error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror</div><button type="submit" class="sona-button btn-block">Verifikasi & Masuk</button></form><p class="small text-muted mt-4 mb-0"><i class="fa fa-lock mr-1"></i>Tidak menggunakan OTP. Nomor hanya dicocokkan dengan data active stay.</p>
        @else
            <div class="alert alert-warning text-left"><strong>Akses belum tersedia.</strong><br>Tidak ada masa menginap aktif untuk kamar ini. Hubungi Receptionist bila Anda baru saja check-in.</div><a href="{{ route('public.contact') }}" class="sona-button d-inline-block">Hubungi Receptionist</a>
        @endif
    </div></div></div></div></section>
@endsection
