@extends('layouts.guest')
@section('title', 'Galeri · Candra Resort')
@section('content')
    <section id="page-top" class="page-hero" style="background-image:url('{{ $content?->image_path ? Storage::url($content->image_path) : asset('landing-lage/img/hero/hero-2.jpg') }}')"><div class="container text-center"><h1>{{ $content?->title ?? 'Galeri Candra Resort' }}</h1><p>{{ $content?->content ?? 'Lihat suasana yang menanti kunjungan Anda.' }}</p></div></section>
    <section class="gallery-section spad"><div class="container"><div class="row">
        @forelse ($images as $image)
            <div class="col-lg-4 col-sm-6 mb-4"><div class="gallery-item set-bg" data-setbg="{{ asset('storage/'.$image->image_path) }}"><div class="gi-text"><h3>{{ $image->caption }}</h3></div></div></div>
        @empty
            @foreach (range(1, 6) as $index)
                <div class="col-lg-4 col-sm-6 mb-4"><div class="gallery-item set-bg" data-setbg="{{ asset('landing-lage/img/gallery/gallery-'.((($index - 1) % 4) + 1).'.jpg') }}"><div class="gi-text"><h3>Candra Experience</h3></div></div></div>
            @endforeach
        @endforelse
    </div><div class="row"><div class="col-12 d-flex justify-content-center">{{ $images->links() }}</div></div></div></section>
@endsection
