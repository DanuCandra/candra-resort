@extends('layouts.main')

@section('title', 'Konten Website')

@section('content')
    <x-dashboard.page-heading title="Konten Website" description="Kelola informasi hotel, teks landing page, dan galeri yang dilihat Guest.">
        <a href="{{ route('home') }}" target="_blank" rel="noopener" class="btn btn-outline-primary"><i class="ti ti-external-link me-1"></i>Lihat Website</a>
    </x-dashboard.page-heading>

    <div class="row">
        @foreach ([
            ['Pengaturan Hotel', $metrics['settings'], 'ti-settings', 'primary'],
            ['Konten Halaman', $metrics['contents'], 'ti-file-text', 'info'],
            ['Gambar Galeri', $metrics['gallery'], 'ti-photo', 'success'],
            ['Konten Aktif', $metrics['active_contents'], 'ti-circle-check', 'warning'],
        ] as [$label, $value, $icon, $color])
            <div class="col-xl-3 col-md-6"><div class="card"><div class="card-body d-flex align-items-center"><span class="round-48 rounded bg-light-{{ $color }} d-flex align-items-center justify-content-center"><i class="ti {{ $icon }} fs-6 text-{{ $color }}"></i></span><div class="ms-3"><span class="text-muted">{{ $label }}</span><h4 class="fw-semibold mb-0">{{ $value }}</h4></div></div></div></div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-body pb-0">
            <ul class="nav nav-pills gap-2" role="tablist">
                <li class="nav-item"><a class="nav-link {{ $activeTab === 'settings' ? 'active' : '' }}" href="{{ route('receptionist.website.index', ['tab' => 'settings']) }}"><i class="ti ti-building me-1"></i>Informasi Hotel</a></li>
                <li class="nav-item"><a class="nav-link {{ $activeTab === 'contents' ? 'active' : '' }}" href="{{ route('receptionist.website.index', ['tab' => 'contents']) }}"><i class="ti ti-file-text me-1"></i>Konten Halaman</a></li>
                <li class="nav-item"><a class="nav-link {{ $activeTab === 'gallery' ? 'active' : '' }}" href="{{ route('receptionist.website.index', ['tab' => 'gallery']) }}"><i class="ti ti-photo me-1"></i>Galeri</a></li>
            </ul>
        </div>
    </div>

    @if ($activeTab === 'settings')
        <div class="card">
            <div class="card-body">
                <div class="mb-4"><h5 class="fw-semibold mb-1">Informasi Utama Hotel</h5><p class="text-muted mb-0">Data ini digunakan pada halaman kontak dan informasi operasional Guest.</p></div>
                <form method="POST" action="{{ route('receptionist.website.settings.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Nama Hotel *</label><input name="hotel_name" value="{{ old('hotel_name', $settings->get('hotel.name')) }}" class="form-control @error('hotel_name') is-invalid @enderror" required>@error('hotel_name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-md-6"><label class="form-label">Nomor Telepon *</label><input name="hotel_phone" value="{{ old('hotel_phone', $settings->get('hotel.phone')) }}" class="form-control @error('hotel_phone') is-invalid @enderror" required>@error('hotel_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-md-6"><label class="form-label">Email Hotel *</label><input type="email" name="hotel_email" value="{{ old('hotel_email', $settings->get('hotel.email')) }}" class="form-control @error('hotel_email') is-invalid @enderror" required>@error('hotel_email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-md-3"><label class="form-label">Waktu Check-in *</label><input type="time" name="check_in_time" value="{{ old('check_in_time', $settings->get('hotel.check_in_time', '14:00')) }}" class="form-control @error('check_in_time') is-invalid @enderror" required>@error('check_in_time')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-md-3"><label class="form-label">Waktu Check-out *</label><input type="time" name="check_out_time" value="{{ old('check_out_time', $settings->get('hotel.check_out_time', '12:00')) }}" class="form-control @error('check_out_time') is-invalid @enderror" required>@error('check_out_time')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-12"><label class="form-label">Alamat *</label><textarea name="hotel_address" rows="3" class="form-control @error('hotel_address') is-invalid @enderror" required>{{ old('hotel_address', $settings->get('hotel.address')) }}</textarea>@error('hotel_address')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    </div>
                    <div class="text-end mt-4"><button class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>Simpan Informasi Hotel</button></div>
                </form>
            </div>
        </div>
    @elseif ($activeTab === 'contents')
        <div class="card">
            <div class="card-body">
                <h5 class="fw-semibold mb-1">Tambah Konten Halaman</h5>
                <p class="text-muted mb-4">Gunakan key yang konsisten. Konten bawaan seperti <code>hero_title</code> dan <code>about_summary</code> langsung digunakan landing page.</p>
                <form method="POST" action="{{ route('receptionist.website.contents.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-3"><label class="form-label">Bagian *</label><input name="section" value="{{ old('section') }}" class="form-control @error('section') is-invalid @enderror" placeholder="contoh: about" required>@error('section')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-md-3"><label class="form-label">Content Key *</label><input name="content_key" value="{{ old('content_key') }}" class="form-control @error('content_key') is-invalid @enderror" placeholder="contoh: about_history" required>@error('content_key')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-md-6"><label class="form-label">Judul</label><input name="title" value="{{ old('title') }}" class="form-control"></div>
                        <div class="col-md-8"><label class="form-label">Isi Konten</label><textarea name="content" rows="3" class="form-control">{{ old('content') }}</textarea></div>
                        <div class="col-md-4"><label class="form-label">Gambar</label><input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">@error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-md-3"><label class="form-label">Urutan</label><input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="form-control"></div>
                        <div class="col-md-3 d-flex align-items-end"><div class="form-check form-switch mb-2"><input type="hidden" name="is_active" value="0"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="new-content-active" @checked(old('is_active', true))><label class="form-check-label" for="new-content-active">Aktif</label></div></div>
                        <div class="col-md-6 d-flex align-items-end justify-content-end"><button class="btn btn-primary"><i class="ti ti-plus me-1"></i>Tambah Konten</button></div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            @forelse ($contents as $content)
                <div class="col-xl-6">
                    <div class="card h-100">
                        @if ($content->image_path)
                            <img src="{{ Storage::url($content->image_path) }}" alt="{{ $content->title ?: $content->content_key }}" style="height:180px;object-fit:cover" class="card-img-top">
                        @endif
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3"><div><span class="badge bg-light-primary text-primary mb-2">{{ $content->section }}</span><h5 class="fw-semibold mb-0">{{ $content->content_key }}</h5></div><span class="badge {{ $content->is_active ? 'bg-light-success text-success' : 'bg-light-secondary text-secondary' }}">{{ $content->is_active ? 'Aktif' : 'Nonaktif' }}</span></div>
                            <form method="POST" action="{{ route('receptionist.website.contents.update', $content) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row g-3">
                                    <div class="col-md-4"><label class="form-label">Bagian</label><input name="section" value="{{ $content->section }}" class="form-control" required></div>
                                    <div class="col-md-8"><label class="form-label">Content Key</label><input name="content_key" value="{{ $content->content_key }}" class="form-control" required></div>
                                    <div class="col-12"><label class="form-label">Judul</label><input name="title" value="{{ $content->title }}" class="form-control"></div>
                                    <div class="col-12"><label class="form-label">Isi Konten</label><textarea name="content" rows="4" class="form-control">{{ $content->content }}</textarea></div>
                                    <div class="col-md-8"><label class="form-label">Ganti Gambar</label><input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/webp"></div>
                                    <div class="col-md-4"><label class="form-label">Urutan</label><input type="number" name="sort_order" value="{{ $content->sort_order }}" min="0" class="form-control"></div>
                                    <div class="col-6"><div class="form-check form-switch"><input type="hidden" name="is_active" value="0"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="content-active-{{ $content->id }}" @checked($content->is_active)><label class="form-check-label" for="content-active-{{ $content->id }}">Aktif</label></div></div>
                                    <div class="col-6 text-end"><button class="btn btn-sm btn-primary"><i class="ti ti-device-floppy me-1"></i>Simpan</button></div>
                                </div>
                            </form>
                            <form method="POST" action="{{ route('receptionist.website.contents.destroy', $content) }}" class="mt-3 text-end" data-confirm="Konten akan dihapus dari website publik." data-confirm-title="Hapus konten?">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="ti ti-trash me-1"></i>Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12"><div class="card"><div class="card-body text-center text-muted py-5">Belum ada konten website.</div></div></div>
            @endforelse
        </div>
        <div class="d-flex justify-content-center mt-3">{{ $contents->links() }}</div>
    @else
        <div class="card">
            <div class="card-body">
                <h5 class="fw-semibold mb-1">Tambah Gambar Galeri</h5>
                <p class="text-muted mb-4">Gambar aktif akan tampil pada halaman Galeri Guest.</p>
                <form method="POST" action="{{ route('receptionist.website.gallery.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4"><label class="form-label">Gambar *</label><input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/webp" required>@error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-md-3"><label class="form-label">Caption</label><input name="caption" value="{{ old('caption') }}" class="form-control"></div>
                        <div class="col-md-2"><label class="form-label">Alt Text</label><input name="alt_text" value="{{ old('alt_text') }}" class="form-control"></div>
                        <div class="col-md-1"><label class="form-label">Urutan</label><input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="form-control"></div>
                        <div class="col-md-2"><input type="hidden" name="is_active" value="0"><div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="new-gallery-active" checked><label class="form-check-label" for="new-gallery-active">Aktif</label></div><button class="btn btn-primary w-100"><i class="ti ti-upload me-1"></i>Unggah</button></div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            @forelse ($galleryImages as $image)
                <div class="col-xl-4 col-md-6">
                    <div class="card h-100">
                        <img src="{{ Storage::url($image->image_path) }}" alt="{{ $image->alt_text }}" class="card-img-top" style="height:220px;object-fit:cover">
                        <div class="card-body">
                            <form method="POST" action="{{ route('receptionist.website.gallery.update', $image) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="mb-3"><label class="form-label">Caption</label><input name="caption" value="{{ $image->caption }}" class="form-control"></div>
                                <div class="mb-3"><label class="form-label">Alt Text</label><input name="alt_text" value="{{ $image->alt_text }}" class="form-control"></div>
                                <div class="row g-2"><div class="col-8"><label class="form-label">Ganti Gambar</label><input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/webp"></div><div class="col-4"><label class="form-label">Urutan</label><input type="number" name="sort_order" value="{{ $image->sort_order }}" min="0" class="form-control"></div></div>
                                <div class="d-flex justify-content-between align-items-center mt-3"><div class="form-check form-switch"><input type="hidden" name="is_active" value="0"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="gallery-active-{{ $image->id }}" @checked($image->is_active)><label class="form-check-label" for="gallery-active-{{ $image->id }}">Aktif</label></div><button class="btn btn-sm btn-primary"><i class="ti ti-device-floppy me-1"></i>Simpan</button></div>
                            </form>
                            <form method="POST" action="{{ route('receptionist.website.gallery.destroy', $image) }}" class="mt-3 text-end" data-confirm="Gambar tidak lagi tampil pada galeri publik." data-confirm-title="Hapus gambar galeri?">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="ti ti-trash me-1"></i>Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12"><div class="card"><div class="card-body text-center text-muted py-5">Belum ada gambar galeri.</div></div></div>
            @endforelse
        </div>
        <div class="d-flex justify-content-center mt-3">{{ $galleryImages->links() }}</div>
    @endif
@endsection
