@extends('layouts.guest')
@section('title', 'Promosi · Candra Resort')
@section('content')
    <section class="page-hero" style="background-image: url('{{ asset('landing-lage/img/hero/hero-3.jpg') }}')"><div class="container text-center"><h1>Promosi</h1><p>Lebih banyak pengalaman dengan penawaran terbaik.</p></div></section>
    <section class="blog-section spad"><div class="container"><div class="row">
        @forelse ($promotions as $promotion)
            <div class="col-lg-4"><div class="blog-item set-bg" data-setbg="{{ asset('landing-lage/img/blog/blog-'.(($loop->index % 3) + 1).'.jpg') }}"><div class="bi-text"><span class="b-tag">Kode Promo: {{ $promotion->code }}</span><h4><a href="{{ route('public.rooms.index', ['promo_code' => $promotion->code]) }}">{{ $promotion->name }}</a></h4><div class="b-time"><i class="icon_clock_alt"></i> {{ $promotion->ends_at ? 'Berlaku sampai '.$promotion->ends_at->translatedFormat('d M Y') : 'Tanpa batas waktu' }}</div></div></div><p class="promotion-card-copy">{{ $promotion->description ?: 'Gunakan kode promo ini saat melakukan pemesanan kamar.' }}</p></div>
        @empty
            <div class="col-12 text-center"><h4>Belum ada promosi aktif.</h4><p>Silakan kembali lagi untuk penawaran terbaru Candra Resort.</p></div>
        @endforelse
    </div><div class="row"><div class="col-12">{{ $promotions->links() }}</div></div></div></section>
@endsection
