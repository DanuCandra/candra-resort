@extends('layouts.guest')
@section('title', 'Fasilitas · Candra Resort')
@section('content')
    <section id="page-top" class="page-hero" style="background-image:url('{{ $content?->image_path ? Storage::url($content->image_path) : asset('landing-lage/img/hero/hero-1.jpg') }}')"><div class="container text-center"><h1>{{ $content?->title ?? 'Fasilitas Hotel' }}</h1><p>{{ $content?->content ?? 'Semua yang Anda perlukan untuk tinggal dengan nyaman.' }}</p></div></section>
    <section class="services-section spad"><div class="container"><div class="row">
        @forelse ($facilities as $facility)
            <div class="col-lg-4 col-sm-6"><div class="service-item"><i class="{{ $facility->icon ?: 'flaticon-026-bed' }}"></i><h4>{{ $facility->name }}</h4><p>{{ $facility->description ?: 'Fasilitas pilihan untuk melengkapi pengalaman menginap Anda.' }}</p></div></div>
        @empty
            <div class="col-12 text-center"><h4>Informasi fasilitas sedang disiapkan.</h4></div>
        @endforelse
    </div><div class="row"><div class="col-12 d-flex justify-content-center">{{ $facilities->links() }}</div></div></div></section>
@endsection
