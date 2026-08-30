@extends('layouts.guest')
@section('title', 'Kamar · Candra Resort')
@section('content')
    <section class="page-hero" style="background-image:url('{{ asset('landing-lage/img/hero/hero-2.jpg') }}')"><div class="container text-center"><h1>Kamar & Suite</h1><p>Temukan ruang terbaik untuk perjalanan Anda.</p></div></section>
    <section class="py-5" style="background:#f6f6f6"><div class="container"><div class="auth-card"><form method="GET" action="{{ route('public.rooms.index') }}" class="sona-form"><div class="row align-items-end">
        <div class="col-md-3 mb-3"><label>Check-In</label><input type="date" name="check_in" min="{{ today()->format('Y-m-d') }}" value="{{ old('check_in',request('check_in',today()->addDay()->format('Y-m-d'))) }}" class="form-control @error('check_in') is-invalid @enderror" required>@error('check_in')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-3 mb-3"><label>Check-Out</label><input type="date" name="check_out" min="{{ today()->addDay()->format('Y-m-d') }}" value="{{ old('check_out',request('check_out',today()->addDays(2)->format('Y-m-d'))) }}" class="form-control @error('check_out') is-invalid @enderror" required>@error('check_out')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-2 mb-3"><label>Dewasa</label><input type="number" name="adults" min="1" value="{{ request('adults',1) }}" class="form-control"></div>
        <div class="col-md-2 mb-3"><label>Anak</label><input type="number" name="children" min="0" value="{{ request('children',0) }}" class="form-control"></div>
        <div class="col-md-2 mb-3"><button class="sona-button w-100" type="submit">Cari</button></div>
    </div></form></div></div></section>
    <section class="rooms-section spad"><div class="container">
        @if($search)<div class="section-title text-center"><span>Hasil Ketersediaan</span><h2>{{ Carbon\Carbon::parse($search['check_in'])->translatedFormat('d M Y') }} – {{ Carbon\Carbon::parse($search['check_out'])->translatedFormat('d M Y') }}</h2><p>{{ $search['adults'] }} dewasa, {{ $search['children'] }} anak</p></div>@endif
        <div class="row">@forelse($roomTypes as $index=>$roomType)
            @php($image=$roomType->images->first()?->image_path ? asset('storage/'.$roomType->images->first()->image_path) : asset('landing-lage/img/room/room-'.(($index%6)+1).'.jpg'))
            <div class="col-lg-4 col-md-6"><div class="room-item"><img src="{{ $image }}" alt="{{ $roomType->name }}"><div class="ri-text"><h4>{{ $roomType->name }}</h4>
                @if($search)<h3>Rp{{ number_format((float)$roomType->search_quote['grand_total'],0,',','.') }}<span>/{{ $roomType->search_quote['total_nights'] }} malam</span></h3>@else<h3>Rp{{ number_format((float)$roomType->base_price,0,',','.') }}<span>/malam</span></h3>@endif
                <table><tbody><tr><td class="r-o">Kapasitas:</td><td>{{ $roomType->capacity }} tamu</td></tr><tr><td class="r-o">Bed:</td><td>{{ $roomType->bed_count }} {{ $roomType->bed_type }}</td></tr><tr><td class="r-o">Kamar:</td><td>{{ $search ? $roomType->available_rooms.' tersedia' : $roomType->rooms_count.' unit' }}</td></tr></tbody></table>
                @if($search && $roomType->available_rooms > 0)<a href="{{ route('guest.reservations.create',['roomType'=>$roomType]+$search) }}" class="primary-btn">Pesan Sekarang</a>@elseif($search)<span class="primary-btn text-muted">Tidak Tersedia</span>@else<a href="{{ route('public.rooms.show',$roomType) }}" class="primary-btn">Lihat Detail</a>@endif
            </div></div></div>
        @empty<div class="col-12 text-center py-5"><h4>Tidak ada kamar yang sesuai.</h4><p>Ubah tanggal atau jumlah tamu untuk melihat pilihan lain.</p></div>@endforelse</div>
        <div class="row"><div class="col-12">{{ $roomTypes->withQueryString()->links() }}</div></div>
    </div></section>
@endsection
