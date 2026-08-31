@extends('layouts.main')

@section('title', 'Profil Saya')

@push('styles')
    <style>
        .staff-profile-page { --profile-primary:#5d87ff; --profile-ink:#17233c; --profile-muted:#6b778c; --profile-border:#e7ecf4; padding-bottom:16px; }
        .profile-page-heading { display:flex; align-items:flex-start; justify-content:space-between; gap:20px; margin-bottom:24px; }
        .profile-page-heading small { display:block; margin-bottom:5px; color:var(--profile-primary); font-size:9px; font-weight:800; letter-spacing:.09em; text-transform:uppercase; }
        .profile-page-heading h1 { margin:0 0 5px; color:var(--profile-ink); font-size:25px; font-weight:800; letter-spacing:-.02em; }
        .profile-page-heading p { margin:0; color:var(--profile-muted); font-size:10px; }
        .profile-back { display:inline-flex; align-items:center; justify-content:center; gap:7px; min-height:38px; padding:8px 12px; border:1px solid #dfe5ee; border-radius:10px; background:#fff; color:#657287; font-size:9px; font-weight:750; white-space:nowrap; transition:.17s ease; }
        .profile-back:hover { border-color:#5d87ff; color:#4d76dc; }
        .profile-card { overflow:hidden; border:1px solid var(--profile-border); border-radius:17px; background:#fff; box-shadow:0 7px 24px rgba(24,45,89,.045); }
        .profile-card + .profile-card { margin-top:20px; }
        .profile-card-header { display:flex; align-items:center; gap:11px; padding:18px 20px; border-bottom:1px solid var(--profile-border); }
        .profile-card-icon { display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:11px; background:#edf3ff; color:#4f78df; font-size:17px; flex:0 0 auto; }
        .profile-card-header h2 { margin:0 0 2px; color:var(--profile-ink); font-size:14px; font-weight:750; }
        .profile-card-header p { margin:0; color:#8a95a6; font-size:8px; }
        .profile-card-body { padding:21px; }
        .avatar-area { text-align:center; }
        .avatar-preview { display:flex; align-items:center; justify-content:center; width:108px; height:108px; margin:0 auto 13px; overflow:hidden; border:5px solid #fff; border-radius:50%; background:#e8efff; box-shadow:0 8px 24px rgba(65,101,190,.18); color:#426fdc; font-size:35px; font-weight:800; }
        .avatar-preview img { width:100%; height:100%; object-fit:cover; }
        .avatar-area h3 { margin:0 0 2px; color:var(--profile-ink); font-size:15px; font-weight:750; }
        .avatar-area > p { margin:0; color:#8792a4; font-size:9px; }
        .role-pill { display:inline-flex; align-items:center; gap:5px; margin-top:9px; padding:5px 8px; border-radius:999px; background:#edf3ff; color:#4d76dc; font-size:8px; font-weight:800; letter-spacing:.04em; text-transform:uppercase; }
        .photo-actions { display:grid; gap:8px; margin-top:19px; }
        .avatar-upload { position:relative; display:flex; align-items:center; justify-content:center; gap:7px; min-height:40px; border-radius:10px; background:#5d87ff; color:#fff; cursor:pointer; font-size:9px; font-weight:750; transition:.17s ease; }
        .avatar-upload:hover { background:#466ed6; }
        .avatar-upload input { position:absolute; width:1px; height:1px; opacity:0; }
        .avatar-remove { display:flex; align-items:center; justify-content:center; gap:6px; min-height:36px; margin:0; border:1px solid #f1d9d5; border-radius:9px; color:#c95c4c; cursor:pointer; font-size:8px; }
        .avatar-remove input { margin:0; }
        .photo-help { display:block; margin-top:9px; color:#929cac; font-size:8px; line-height:1.5; }
        .account-summary { margin-top:20px; padding-top:8px; border-top:1px solid var(--profile-border); }
        .summary-row { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:10px 0; border-bottom:1px solid #edf1f6; }
        .summary-row:last-child { border-bottom:0; }
        .summary-row span { color:#8b96a7; font-size:8px; }
        .summary-row strong { overflow:hidden; color:#536075; font-size:9px; font-weight:700; text-align:right; text-overflow:ellipsis; white-space:nowrap; }
        .profile-label { margin-bottom:6px; color:#4f5b6d; font-size:9px; font-weight:700; }
        .profile-input { min-height:44px; border-color:#dde4ee; border-radius:10px; background:#fff; font-size:10px; }
        .profile-input:focus { border-color:#5d87ff; box-shadow:0 0 0 3px rgba(93,135,255,.1); }
        .input-help { display:block; margin-top:5px; color:#929dae; font-size:8px; line-height:1.45; }
        .profile-form-footer { display:flex; align-items:center; justify-content:space-between; gap:14px; margin-top:22px; padding-top:17px; border-top:1px solid var(--profile-border); }
        .profile-form-footer small { color:#8c97a8; font-size:8px; }
        .profile-submit { display:inline-flex; align-items:center; justify-content:center; gap:7px; min-height:40px; padding:8px 14px; border:0; border-radius:10px; background:#5d87ff; color:#fff; font-size:9px; font-weight:750; transition:.17s ease; }
        .profile-submit:hover { background:#466ed6; }
        .password-intro { display:flex; align-items:flex-start; gap:10px; margin-bottom:18px; padding:11px 12px; border-radius:11px; background:#f7f9fc; color:#778397; font-size:8px; line-height:1.55; }
        .password-intro i { color:#5d87ff; font-size:16px; flex:0 0 auto; }
        .password-requirements { display:flex; flex-wrap:wrap; gap:7px; margin-top:8px; }
        .password-requirements span { display:inline-flex; align-items:center; gap:4px; padding:5px 7px; border-radius:7px; background:#f3f6fa; color:#7b8799; font-size:7px; font-weight:700; }
        @media (max-width:991.98px) { .profile-card { margin-bottom:20px; } .profile-card + .profile-card { margin-top:0; } }
        @media (max-width:575.98px) { .profile-page-heading { flex-direction:column; } .profile-back { width:100%; } .profile-card-body { padding:18px; } .profile-form-footer { align-items:flex-start; flex-direction:column; } .profile-submit { width:100%; } }
    </style>
@endpush

@section('content')
    @php
        $routePrefix = $user->hasRole(\App\Enums\UserRole::Owner) ? 'owner' : 'receptionist';
        $avatarUrl = $user->avatar_path ? asset('storage/'.$user->avatar_path) : null;
    @endphp

    <div id="staff-profile-page" class="staff-profile-page">
        <div class="profile-page-heading">
            <div><small>Pengaturan akun</small><h1>Profil Saya</h1><p>Perbarui informasi pribadi, foto profil, dan keamanan akun Anda.</p></div>
            <a href="{{ route($user->dashboardRouteName()) }}" class="profile-back"><i class="ti ti-arrow-left"></i>Kembali ke Dashboard</a>
        </div>

        <form method="POST" action="{{ route($routePrefix.'.profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="remove_avatar" value="0">
            <div class="row g-4">
                <div class="col-lg-4">
                    <section class="profile-card">
                        <div class="profile-card-body">
                            <div class="avatar-area">
                                <span class="avatar-preview">
                                    <img id="avatar-preview-image" src="{{ $avatarUrl ?? '' }}" alt="Pratinjau foto profil" @if (! $avatarUrl) hidden @endif>
                                    <span id="avatar-preview-initial" @if ($avatarUrl) hidden @endif>{{ str($user->name)->substr(0, 1)->upper() }}</span>
                                </span>
                                <h3>{{ $user->name }}</h3><p>{{ $user->email }}</p><span class="role-pill"><i class="ti ti-shield-check"></i>{{ $user->role->label() }}</span>
                            </div>
                            <div class="photo-actions">
                                <label class="avatar-upload"><i class="ti ti-camera"></i>Pilih Foto Profil<input id="avatar-input" type="file" name="avatar" accept="image/jpeg,image/png,image/webp"></label>
                                @if ($avatarUrl)<label class="avatar-remove"><input id="remove-avatar" type="checkbox" name="remove_avatar" value="1">Hapus foto saat disimpan</label>@endif
                            </div>
                            <small class="photo-help">Format JPG, PNG, atau WebP dengan ukuran maksimal 3 MB.</small>
                            @error('avatar')<div class="text-danger fs-2 mt-2 text-center">{{ $message }}</div>@enderror
                            <div class="account-summary">
                                <div class="summary-row"><span>Kode pegawai</span><strong>{{ $user->employee_code ?: '-' }}</strong></div>
                                <div class="summary-row"><span>Username</span><strong>{{ $user->username ? '@'.$user->username : '-' }}</strong></div>
                                <div class="summary-row"><span>Login terakhir</span><strong>{{ $user->last_login_at?->translatedFormat('d M Y, H:i') ?? 'Belum tercatat' }}</strong></div>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="col-lg-8">
                    <section class="profile-card">
                        <header class="profile-card-header"><span class="profile-card-icon"><i class="ti ti-edit"></i></span><div><h2>Informasi Profil</h2><p>Data utama yang digunakan pada akun staf Anda.</p></div></header>
                        <div class="profile-card-body">
                            <div class="row g-3">
                                <div class="col-12"><label for="profile-name" class="form-label profile-label">Nama lengkap</label><input id="profile-name" name="name" value="{{ old('name', $user->name) }}" class="form-control profile-input @error('name') is-invalid @enderror" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                                <div class="col-md-6"><label for="profile-email" class="form-label profile-label">Alamat email</label><input id="profile-email" type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control profile-input @error('email') is-invalid @enderror" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror<small class="input-help">Email digunakan untuk masuk ke sistem.</small></div>
                                <div class="col-md-6"><label for="profile-phone" class="form-label profile-label">Nomor telepon</label><input id="profile-phone" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control profile-input @error('phone') is-invalid @enderror" placeholder="Contoh: 0812 3456 7890">@error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror<small class="input-help">Nomor disimpan dalam format Indonesia.</small></div>
                            </div>
                            <div class="profile-form-footer"><small><i class="ti ti-info-circle me-1"></i>Perubahan akan langsung diterapkan pada akun Anda.</small><button type="submit" class="profile-submit"><i class="ti ti-device-floppy"></i>Simpan Perubahan</button></div>
                        </div>
                    </section>
                </div>
            </div>
        </form>

        <div class="row g-4 mt-1">
            <div class="col-lg-8 ms-auto">
                <section class="profile-card">
                    <header class="profile-card-header"><span class="profile-card-icon"><i class="ti ti-lock"></i></span><div><h2>Ganti Password</h2><p>Perbarui password secara berkala untuk menjaga keamanan akun.</p></div></header>
                    <div class="profile-card-body">
                        <div class="password-intro"><i class="ti ti-shield-lock"></i><span>Password lama wajib dimasukkan untuk memastikan perubahan dilakukan oleh pemilik akun.</span></div>
                        <form method="POST" action="{{ route($routePrefix.'.profile.password.update') }}" data-confirm="Password akun Anda akan diganti. Pastikan password baru sudah dicatat dengan aman." data-confirm-title="Ganti password sekarang?" data-confirm-button="Ya, Ganti Password">
                            @csrf
                            @method('PUT')
                            <div class="row g-3">
                                <div class="col-12"><label for="current-password" class="form-label profile-label">Password saat ini</label><input id="current-password" type="password" name="current_password" class="form-control profile-input @error('current_password', 'updatePassword') is-invalid @enderror" autocomplete="current-password" required>@error('current_password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                                <div class="col-md-6"><label for="new-password" class="form-label profile-label">Password baru</label><input id="new-password" type="password" name="password" class="form-control profile-input @error('password', 'updatePassword') is-invalid @enderror" autocomplete="new-password" required>@error('password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                                <div class="col-md-6"><label for="password-confirmation" class="form-label profile-label">Konfirmasi password baru</label><input id="password-confirmation" type="password" name="password_confirmation" class="form-control profile-input" autocomplete="new-password" required></div>
                            </div>
                            <div class="password-requirements"><span><i class="ti ti-check"></i>Minimal 8 karakter</span><span><i class="ti ti-check"></i>Mengandung huruf</span><span><i class="ti ti-check"></i>Mengandung angka</span></div>
                            <div class="profile-form-footer"><small>Password tersimpan secara aman dan tidak dapat dilihat oleh staf lain.</small><button type="submit" class="profile-submit"><i class="ti ti-lock"></i>Perbarui Password</button></div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('avatar-input');
            const image = document.getElementById('avatar-preview-image');
            const initial = document.getElementById('avatar-preview-initial');
            const remove = document.getElementById('remove-avatar');

            input?.addEventListener('change', function () {
                const file = input.files?.[0];
                if (!file) return;

                image.src = URL.createObjectURL(file);
                image.hidden = false;
                initial.hidden = true;
                if (remove) remove.checked = false;
            });

            remove?.addEventListener('change', function () {
                if (!remove.checked) return;
                input.value = '';
                image.hidden = true;
                initial.hidden = false;
            });
        });
    </script>
@endpush
