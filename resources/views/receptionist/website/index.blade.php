@extends('layouts.main')

@section('title', 'Konten Website')

@push('styles')
    <style>
        .website-manager .manager-hero { overflow: hidden; border: 0; border-radius: 20px; background: linear-gradient(120deg, #253b70, #5d87ff); color: #fff; box-shadow: 0 16px 38px rgba(63,104,204,.18); }
        .website-manager .metric-card, .website-manager .content-card, .website-manager .page-selector, .website-manager .manager-card { border: 1px solid #edf1f7; border-radius: 17px; box-shadow: 0 8px 26px rgba(17,38,85,.045); }
        .website-manager .manager-tabs { gap: 7px; padding: 5px; border-radius: 13px; background: #f2f5f9; }
        .website-manager .manager-tabs .nav-link { border-radius: 10px; color: #647187; font-weight: 600; }
        .website-manager .manager-tabs .nav-link.active { background: #fff; color: #4e78df; box-shadow: 0 5px 14px rgba(24,51,103,.08); }
        .page-choice { display: block; height: 100%; padding: 15px; border: 1px solid #e8edf5; border-radius: 14px; color: #26334c; transition: .2s ease; }
        .page-choice:hover, .page-choice.active { transform: translateY(-2px); border-color: #bfcfff; background: #f5f8ff; color: #426fdc; box-shadow: 0 8px 20px rgba(54,93,177,.08); }
        .page-choice-icon { display: inline-flex; align-items: center; justify-content: center; width: 39px; height: 39px; margin-bottom: 10px; border-radius: 12px; background: #edf3ff; color: #4c76dd; }
        .content-card { overflow: hidden; }
        .content-card-header { padding: 18px 20px; border-bottom: 1px solid #edf1f7; background: linear-gradient(180deg, #fff, #fbfcfe); }
        .content-preview-image { width: 100%; height: 185px; object-fit: cover; }
        .usage-note { padding: 10px 12px; border-radius: 10px; background: #f4f7fb; color: #66738a; font-size: 12px; }
        .slot-status { display: inline-flex; align-items: center; gap: 5px; padding: 5px 9px; border-radius: 999px; font-size: 11px; font-weight: 700; }
        .preview-frame { width: 100%; height: 72vh; border: 0; background: #f5f7fa; }
        .gallery-thumb { width: 100%; height: 220px; object-fit: cover; }
        .section-kicker { color: #5d87ff; font-size: 11px; font-weight: 700; letter-spacing: .09em; text-transform: uppercase; }
    </style>
@endpush

@section('content')
    <div class="website-manager">
        <div class="card manager-hero mb-4">
            <div class="card-body p-4 p-lg-5 d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <div><span class="badge bg-white text-primary mb-3"><i class="ti ti-world me-1"></i>PUBLIC WEBSITE MANAGER</span><h3 class="text-white fw-bolder mb-2">Konten Website</h3><p class="text-white opacity-75 mb-0">Pilih halaman dan bagian yang ingin diubah. Setiap kartu menunjukkan lokasi tampil serta menyediakan pratinjau langsung.</p></div>
                <div class="d-flex flex-wrap gap-2"><button type="button" class="btn btn-light text-primary js-preview-page" data-preview-url="{{ route('home') }}" data-preview-title="Pratinjau Beranda" data-bs-toggle="modal" data-bs-target="#website-preview-modal"><i class="ti ti-device-desktop me-1"></i>Pratinjau Website</button><a href="{{ route('home') }}" target="_blank" rel="noopener" class="btn btn-outline-light"><i class="ti ti-external-link me-1"></i>Buka Tab Baru</a></div>
            </div>
        </div>

        <div class="row">
            @foreach ([
                ['Informasi Hotel', $metrics['settings'], 'ti-settings', 'primary'], ['Slot Halaman', count(\App\Support\WebsiteContentRegistry::slots()), 'ti-layout-grid', 'info'],
                ['Gambar Galeri', $metrics['gallery'], 'ti-photo', 'success'], ['Konten Aktif', $metrics['active_contents'], 'ti-circle-check', 'warning'],
            ] as [$label, $value, $icon, $color])
                <div class="col-xl-3 col-md-6"><div class="card metric-card"><div class="card-body d-flex align-items-center"><span class="round-48 rounded bg-light-{{ $color }} d-flex align-items-center justify-content-center"><i class="ti {{ $icon }} fs-6 text-{{ $color }}"></i></span><div class="ms-3"><span class="text-muted">{{ $label }}</span><h4 class="fw-semibold mb-0">{{ $value }}</h4></div></div></div></div>
            @endforeach
        </div>

        <div class="card manager-card">
            <div class="card-body pb-3">
                <ul class="nav nav-pills manager-tabs" role="tablist">
                    <li class="nav-item"><a class="nav-link {{ $activeTab === 'settings' ? 'active' : '' }}" href="{{ route('receptionist.website.index', ['tab' => 'settings']) }}"><i class="ti ti-building me-1"></i>Informasi Hotel</a></li>
                    <li class="nav-item"><a class="nav-link {{ $activeTab === 'contents' ? 'active' : '' }}" href="{{ route('receptionist.website.index', ['tab' => 'contents', 'page' => $selectedPage]) }}"><i class="ti ti-file-text me-1"></i>Konten Halaman</a></li>
                    <li class="nav-item"><a class="nav-link {{ $activeTab === 'gallery' ? 'active' : '' }}" href="{{ route('receptionist.website.index', ['tab' => 'gallery']) }}"><i class="ti ti-photo me-1"></i>Galeri</a></li>
                </ul>
            </div>
        </div>

        @if ($activeTab === 'settings')
            <div class="card manager-card">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                        <div><span class="section-kicker">Dipakai di navbar, footer, dan kontak</span><h5 class="fw-semibold mb-1 mt-1">Informasi Utama Hotel</h5><p class="text-muted mb-0">Perubahan nama dan kontak akan tampil konsisten pada seluruh website Guest.</p></div>
                        <button type="button" class="btn btn-light-primary text-primary js-preview-page" data-preview-url="{{ route('public.contact') }}#contact-information" data-preview-title="Pratinjau Informasi Hotel" data-bs-toggle="modal" data-bs-target="#website-preview-modal"><i class="ti ti-eye me-1"></i>Lihat di Halaman Kontak</button>
                    </div>
                    <form method="POST" action="{{ route('receptionist.website.settings.update') }}">
                        @csrf @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">Nama Hotel *</label><input name="hotel_name" value="{{ old('hotel_name', $settings->get('hotel.name')) }}" class="form-control @error('hotel_name') is-invalid @enderror" required>@error('hotel_name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="col-md-6"><label class="form-label">Tagline Hotel</label><input name="hotel_tagline" value="{{ old('hotel_tagline', $settings->get('hotel.tagline', 'Hotel & Experience')) }}" class="form-control @error('hotel_tagline') is-invalid @enderror" placeholder="Hotel & Experience">@error('hotel_tagline')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="col-md-6"><label class="form-label">Nomor Telepon *</label><input name="hotel_phone" value="{{ old('hotel_phone', $settings->get('hotel.phone')) }}" class="form-control @error('hotel_phone') is-invalid @enderror" required>@error('hotel_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="col-md-6"><label class="form-label">Nomor WhatsApp Reservasi</label><input name="hotel_whatsapp" value="{{ old('hotel_whatsapp', $settings->get('hotel.whatsapp')) }}" class="form-control @error('hotel_whatsapp') is-invalid @enderror" placeholder="+62 812 3456 7890">@error('hotel_whatsapp')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="col-md-6"><label class="form-label">Email Hotel *</label><input type="email" name="hotel_email" value="{{ old('hotel_email', $settings->get('hotel.email')) }}" class="form-control @error('hotel_email') is-invalid @enderror" required>@error('hotel_email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="col-md-3"><label class="form-label">Waktu Check-in *</label><input type="time" name="check_in_time" value="{{ old('check_in_time', $settings->get('hotel.check_in_time', '14:00')) }}" class="form-control @error('check_in_time') is-invalid @enderror" required>@error('check_in_time')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="col-md-3"><label class="form-label">Waktu Check-out *</label><input type="time" name="check_out_time" value="{{ old('check_out_time', $settings->get('hotel.check_out_time', '12:00')) }}" class="form-control @error('check_out_time') is-invalid @enderror" required>@error('check_out_time')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="col-12"><label class="form-label">Alamat *</label><textarea name="hotel_address" rows="3" class="form-control @error('hotel_address') is-invalid @enderror" required>{{ old('hotel_address', $settings->get('hotel.address')) }}</textarea>@error('hotel_address')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="col-md-6"><label class="form-label">URL Instagram</label><input type="url" name="instagram_url" value="{{ old('instagram_url', $settings->get('social.instagram')) }}" class="form-control @error('instagram_url') is-invalid @enderror" placeholder="https://instagram.com/...">@error('instagram_url')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="col-md-6"><label class="form-label">URL Facebook</label><input type="url" name="facebook_url" value="{{ old('facebook_url', $settings->get('social.facebook')) }}" class="form-control @error('facebook_url') is-invalid @enderror" placeholder="https://facebook.com/...">@error('facebook_url')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        </div>
                        <div class="text-end mt-4"><button class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>Simpan Informasi Hotel</button></div>
                    </form>
                </div>
            </div>
        @elseif ($activeTab === 'contents')
            <div class="card page-selector">
                <div class="card-body p-4">
                    <div class="mb-3"><span class="section-kicker">Langkah 1</span><h5 class="fw-semibold mb-1 mt-1">Pilih Halaman yang Diedit</h5><p class="text-muted mb-0">Hanya konten untuk halaman terpilih yang ditampilkan agar lokasi perubahan tidak membingungkan.</p></div>
                    <div class="row g-3">
                        @foreach ($pages as $pageKey => $page)
                            <div class="col-xl-3 col-md-4 col-6"><a href="{{ route('receptionist.website.index', ['tab' => 'contents', 'page' => $pageKey]) }}" class="page-choice {{ $selectedPage === $pageKey ? 'active' : '' }}"><span class="page-choice-icon"><i class="ti {{ $page['icon'] }}"></i></span><strong class="d-block">{{ $page['label'] }}</strong><small class="text-muted d-none d-md-block">{{ $page['description'] }}</small></a></div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 my-4">
                <div><span class="section-kicker">Langkah 2 · {{ $pages[$selectedPage]['label'] }}</span><h4 class="fw-semibold mb-1 mt-1">Edit Bagian Halaman</h4><p class="text-muted mb-0">Isi field pada kartu, simpan, lalu gunakan tombol pratinjau untuk melihat posisi aslinya.</p></div>
                <button type="button" class="btn btn-outline-primary js-preview-page" data-preview-url="{{ route($pages[$selectedPage]['route']) }}" data-preview-title="Pratinjau {{ $pages[$selectedPage]['label'] }}" data-bs-toggle="modal" data-bs-target="#website-preview-modal"><i class="ti ti-device-desktop me-1"></i>Pratinjau {{ $pages[$selectedPage]['label'] }}</button>
            </div>

            <div class="row">
                @foreach ($selectedSlots as $key => $slot)
                    @php($content = $registeredContents->get($key))
                    <div class="col-xl-6 mb-4">
                        <div class="card content-card h-100 mb-0">
                            @if ($content?->image_path)<img src="{{ Storage::url($content->image_path) }}" alt="{{ $slot['label'] }}" class="content-preview-image">@endif
                            <div class="content-card-header d-flex justify-content-between align-items-start gap-3">
                                <div><span class="section-kicker">{{ $pages[$slot['page']]['label'] }}</span><h5 class="fw-semibold mb-1 mt-1">{{ $slot['label'] }}</h5><small class="text-muted">{{ $slot['description'] }}</small></div>
                                <span class="slot-status {{ $content?->is_active ? 'bg-light-success text-success' : 'bg-light-secondary text-secondary' }}"><i class="ti {{ $content?->is_active ? 'ti-circle-check' : 'ti-circle-dashed' }}"></i>{{ $content ? ($content->is_active ? 'Dipakai' : 'Pakai teks bawaan') : 'Teks bawaan' }}</span>
                            </div>
                            <div class="card-body p-4">
                                <div class="usage-note mb-3"><i class="ti ti-map-pin me-1"></i>Lokasi: halaman {{ $pages[$slot['page']]['label'] }}, bagian “{{ $slot['label'] }}”. <button type="button" class="btn btn-link btn-sm p-0 ms-1 js-preview-page" data-preview-url="{{ route($pages[$slot['page']]['route']).$slot['anchor'] }}" data-preview-title="{{ $slot['label'] }}" data-bs-toggle="modal" data-bs-target="#website-preview-modal">Lihat posisi</button></div>
                                <form method="POST" action="{{ $content ? route('receptionist.website.contents.update', $content) : route('receptionist.website.contents.store') }}" enctype="multipart/form-data">
                                    @csrf @if($content) @method('PUT') @endif
                                    <input type="hidden" name="return_page" value="{{ $selectedPage }}"><input type="hidden" name="section" value="{{ $slot['section'] }}"><input type="hidden" name="content_key" value="{{ $key }}"><input type="hidden" name="sort_order" value="{{ $slot['sortOrder'] }}">
                                    @if ($slot['titleLabel'])<div class="mb-3"><label class="form-label">{{ $slot['titleLabel'] }}</label><input name="title" value="{{ $content?->title ?? $slot['defaultTitle'] }}" class="form-control" maxlength="255"></div>@else<input type="hidden" name="title" value="{{ $content?->title ?? $slot['defaultTitle'] }}">@endif
                                    @if ($slot['contentLabel'])<div class="mb-3"><label class="form-label">{{ $slot['contentLabel'] }}</label><textarea name="content" rows="4" class="form-control">{{ $content?->content ?? $slot['defaultContent'] }}</textarea>@if($key === 'about_values')<small class="text-muted">Satu keunggulan per baris.</small>@endif</div>@else<input type="hidden" name="content" value="{{ $content?->content ?? $slot['defaultContent'] }}">@endif
                                    @if ($slot['image'])<div class="mb-3"><label class="form-label">{{ $content?->image_path ? 'Ganti gambar' : 'Gambar (opsional)' }}</label><input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/webp"><small class="text-muted d-block">JPG, PNG, atau WebP. Maksimal 4 MB.</small>@if($content?->image_path)<div class="form-check mt-2"><input type="hidden" name="remove_image" value="0"><input class="form-check-input" type="checkbox" name="remove_image" value="1" id="remove-image-{{ $key }}"><label class="form-check-label text-danger" for="remove-image-{{ $key }}">Hapus gambar saat ini dan gunakan gambar bawaan</label></div>@endif @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>@endif
                                    <div class="d-flex align-items-center justify-content-between gap-3"><div class="form-check form-switch"><input type="hidden" name="is_active" value="0"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="slot-active-{{ $key }}" @checked($content?->is_active ?? true)><label class="form-check-label" for="slot-active-{{ $key }}">Gunakan konten ini (jika mati, gunakan teks bawaan)</label></div><button class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>{{ $content ? 'Simpan Perubahan' : 'Aktifkan Bagian' }}</button></div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card manager-card">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4"><div><span class="section-kicker">Opsional</span><h5 class="fw-semibold mb-1 mt-1">Konten Tambahan {{ $pages[$selectedPage]['label'] }}</h5><p class="text-muted mb-0">Gunakan untuk blok tambahan. Konten tambahan halaman Tentang akan ikut tampil sebagai kartu informasi.</p></div><button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#custom-content-form"><i class="ti ti-plus me-1"></i>Tambah Konten</button></div>
                    <div class="collapse" id="custom-content-form">
                        <form method="POST" action="{{ route('receptionist.website.contents.store') }}" enctype="multipart/form-data" class="border rounded p-3 mb-4">
                            @csrf <input type="hidden" name="return_page" value="{{ $selectedPage }}">
                            <div class="row g-3"><div class="col-md-4"><label class="form-label">Bagian Halaman *</label><select name="section" class="form-select" required>@foreach($pages[$selectedPage]['sections'] as $section)<option value="{{ $section }}">{{ str($section)->replace('_', ' ')->title() }}</option>@endforeach</select></div><div class="col-md-4"><label class="form-label">Key Unik *</label><input name="content_key" class="form-control" placeholder="contoh: sejarah_resort" required></div><div class="col-md-4"><label class="form-label">Judul</label><input name="title" class="form-control"></div><div class="col-md-8"><label class="form-label">Isi</label><textarea name="content" rows="3" class="form-control"></textarea></div><div class="col-md-4"><label class="form-label">Gambar</label><input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/webp"></div><div class="col-md-3"><label class="form-label">Urutan</label><input type="number" name="sort_order" value="100" min="0" class="form-control"></div><div class="col-md-3 d-flex align-items-end"><div class="form-check form-switch mb-2"><input type="hidden" name="is_active" value="0"><input type="checkbox" class="form-check-input" name="is_active" value="1" id="custom-active" checked><label for="custom-active" class="form-check-label">Aktif</label></div></div><div class="col-md-6 d-flex align-items-end justify-content-end"><button class="btn btn-primary"><i class="ti ti-plus me-1"></i>Simpan Konten Tambahan</button></div></div>
                        </form>
                    </div>

                    <div class="row">
                        @forelse ($customContents as $content)
                            <div class="col-xl-6"><div class="border rounded p-3 mb-3"><div class="d-flex justify-content-between mb-3"><div><span class="badge bg-light-secondary text-secondary">Tambahan · {{ $content->section }}</span><h6 class="fw-semibold mt-2 mb-0">{{ $content->title ?: $content->content_key }}</h6></div><span class="badge {{ $content->is_active ? 'bg-light-success text-success' : 'bg-light-secondary text-secondary' }}">{{ $content->is_active ? 'Aktif' : 'Nonaktif' }}</span></div><form method="POST" action="{{ route('receptionist.website.contents.update', $content) }}" enctype="multipart/form-data">@csrf @method('PUT')<input type="hidden" name="return_page" value="{{ $selectedPage }}"><input type="hidden" name="content_key" value="{{ $content->content_key }}"><div class="row g-2"><div class="col-md-4"><label class="form-label">Bagian</label><select name="section" class="form-select">@foreach($pages[$selectedPage]['sections'] as $section)<option value="{{ $section }}" @selected($content->section === $section)>{{ str($section)->title() }}</option>@endforeach</select></div><div class="col-md-8"><label class="form-label">Judul</label><input name="title" value="{{ $content->title }}" class="form-control"></div><div class="col-12"><label class="form-label">Isi</label><textarea name="content" rows="3" class="form-control">{{ $content->content }}</textarea></div><div class="col-md-5"><input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/webp"></div><div class="col-md-2"><input type="number" name="sort_order" value="{{ $content->sort_order }}" min="0" class="form-control"></div><div class="col-md-2 d-flex align-items-center"><input type="hidden" name="is_active" value="0"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_active" value="1" @checked($content->is_active)></div></div><div class="col-md-3 text-end"><button class="btn btn-sm btn-primary">Simpan</button></div></div></form><form method="POST" action="{{ route('receptionist.website.contents.destroy', $content) }}" class="text-end mt-2" data-confirm="Konten tambahan akan dihapus." data-confirm-title="Hapus konten?">@csrf @method('DELETE')<input type="hidden" name="return_page" value="{{ $selectedPage }}"><button class="btn btn-sm btn-outline-danger"><i class="ti ti-trash me-1"></i>Hapus</button></form></div></div>
                        @empty
                            <div class="col-12 text-center text-muted py-4">Belum ada konten tambahan untuk halaman ini.</div>
                        @endforelse
                    </div>
                    {{ $customContents->links() }}
                </div>
            </div>
        @else
            <div class="card manager-card">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4"><div><span class="section-kicker">Halaman Galeri</span><h5 class="fw-semibold mb-1 mt-1">Tambah Gambar Galeri</h5><p class="text-muted mb-0">Gambar aktif tampil pada halaman Galeri Guest dan pilihan foto landing.</p></div><button type="button" class="btn btn-light-primary text-primary js-preview-page" data-preview-url="{{ route('public.gallery') }}" data-preview-title="Pratinjau Galeri" data-bs-toggle="modal" data-bs-target="#website-preview-modal"><i class="ti ti-eye me-1"></i>Pratinjau Galeri</button></div>
                    <form method="POST" action="{{ route('receptionist.website.gallery.store') }}" enctype="multipart/form-data">@csrf<div class="row g-3 align-items-end"><div class="col-md-4"><label class="form-label">Gambar *</label><input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/webp" required>@error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror</div><div class="col-md-3"><label class="form-label">Caption</label><input name="caption" value="{{ old('caption') }}" class="form-control"></div><div class="col-md-2"><label class="form-label">Alt Text</label><input name="alt_text" value="{{ old('alt_text') }}" class="form-control"></div><div class="col-md-1"><label class="form-label">Urutan</label><input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="form-control"></div><div class="col-md-2"><input type="hidden" name="is_active" value="0"><div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="new-gallery-active" checked><label class="form-check-label" for="new-gallery-active">Aktif</label></div><button class="btn btn-primary w-100"><i class="ti ti-upload me-1"></i>Unggah</button></div></div></form>
                </div>
            </div>
            <div class="row">
                @forelse ($galleryImages as $image)
                    <div class="col-xl-4 col-md-6"><div class="card content-card h-100"><img src="{{ Storage::url($image->image_path) }}" alt="{{ $image->alt_text }}" class="gallery-thumb"><div class="card-body"><form method="POST" action="{{ route('receptionist.website.gallery.update', $image) }}" enctype="multipart/form-data">@csrf @method('PUT')<div class="mb-3"><label class="form-label">Caption</label><input name="caption" value="{{ $image->caption }}" class="form-control"></div><div class="mb-3"><label class="form-label">Alt Text</label><input name="alt_text" value="{{ $image->alt_text }}" class="form-control"></div><div class="row g-2"><div class="col-8"><label class="form-label">Ganti Gambar</label><input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/webp"></div><div class="col-4"><label class="form-label">Urutan</label><input type="number" name="sort_order" value="{{ $image->sort_order }}" min="0" class="form-control"></div></div><div class="d-flex justify-content-between align-items-center mt-3"><div class="form-check form-switch"><input type="hidden" name="is_active" value="0"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="gallery-active-{{ $image->id }}" @checked($image->is_active)><label class="form-check-label" for="gallery-active-{{ $image->id }}">Aktif</label></div><button class="btn btn-sm btn-primary"><i class="ti ti-device-floppy me-1"></i>Simpan</button></div></form><form method="POST" action="{{ route('receptionist.website.gallery.destroy', $image) }}" class="mt-3 text-end" data-confirm="Gambar tidak lagi tampil pada galeri publik." data-confirm-title="Hapus gambar galeri?">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="ti ti-trash me-1"></i>Hapus</button></form></div></div></div>
                @empty
                    <div class="col-12"><div class="card"><div class="card-body text-center text-muted py-5">Belum ada gambar galeri.</div></div></div>
                @endforelse
            </div>
            <div class="d-flex justify-content-center mt-3">{{ $galleryImages->links() }}</div>
        @endif
    </div>

    <div class="modal fade" id="website-preview-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered"><div class="modal-content"><div class="modal-header"><div><small class="section-kicker">Pratinjau Website</small><h5 class="modal-title mt-1" id="website-preview-title">Pratinjau Halaman</h5></div><div class="d-flex align-items-center gap-2"><a href="{{ route('home') }}" id="website-preview-new-tab" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary"><i class="ti ti-external-link me-1"></i>Buka Tab</a><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div></div><div class="modal-body p-0"><iframe class="preview-frame" id="website-preview-frame" title="Pratinjau website"></iframe></div></div></div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const frame = document.getElementById('website-preview-frame');
            const title = document.getElementById('website-preview-title');
            const newTab = document.getElementById('website-preview-new-tab');
            document.querySelectorAll('.js-preview-page').forEach(button => button.addEventListener('click', function () {
                frame.src = this.dataset.previewUrl;
                title.textContent = this.dataset.previewTitle || 'Pratinjau Halaman';
                newTab.href = this.dataset.previewUrl;
            }));
            document.getElementById('website-preview-modal')?.addEventListener('hidden.bs.modal', function () { frame.src = 'about:blank'; });
        });
    </script>
@endpush
