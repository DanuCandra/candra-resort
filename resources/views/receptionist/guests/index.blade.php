@extends('layouts.main')

@section('title', 'Direktori Tamu')

@push('styles')
    <style>
        .guest-directory { --guest-primary: #5d87ff; --guest-ink: #17233c; --guest-muted: #6b778c; --guest-border: #eaf0f7; }
        .guest-metric-card { position: relative; display: block; height: calc(100% - 24px); margin-bottom: 24px; overflow: hidden; border: 1px solid var(--guest-border); border-radius: 17px; background: #fff; color: inherit; box-shadow: 0 8px 24px rgba(17, 38, 85, .04); transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease; }
        .guest-metric-card:hover { transform: translateY(-3px); border-color: color-mix(in srgb, var(--metric-color) 32%, var(--guest-border)); box-shadow: 0 13px 28px rgba(17, 38, 85, .09); }
        .guest-metric-card.is-selected { border-color: var(--metric-color); box-shadow: 0 10px 28px color-mix(in srgb, var(--metric-color) 15%, transparent); }
        .guest-metric-line { position: absolute; inset: 0 auto 0 0; width: 4px; background: var(--metric-color); }
        .guest-metric-icon { display: inline-flex; align-items: center; justify-content: center; width: 43px; height: 43px; border-radius: 13px; background: color-mix(in srgb, var(--metric-color) 12%, white); color: var(--metric-color); }
        .guest-metric-card h3 { color: var(--guest-ink); }
        .guest-panel { overflow: hidden; border: 1px solid var(--guest-border); border-radius: 19px; box-shadow: 0 8px 28px rgba(17, 38, 85, .045); }
        .guest-panel .card-header { padding: 20px 24px; border-bottom: 1px solid var(--guest-border); background: #fff; }
        .guest-panel .card-body { padding: 24px; }
        .guest-section-eyebrow { color: var(--guest-primary); font-size: 11px; font-weight: 700; letter-spacing: .09em; text-transform: uppercase; }
        .guest-search { position: relative; }
        .guest-search > i { position: absolute; z-index: 2; top: 50%; left: 15px; color: #8290a8; font-size: 19px; transform: translateY(-50%); }
        .guest-search .form-control { min-height: 46px; padding-left: 45px; border-color: #e3eaf3; border-radius: 12px; }
        .guest-filter-select { min-height: 46px; border-color: #e3eaf3; border-radius: 12px; }
        .guest-filter-button { min-height: 46px; border-radius: 12px; }
        .active-filter-note { display: inline-flex; align-items: center; gap: 6px; padding: 6px 10px; border-radius: 999px; background: #edf3ff; color: #416bd2; font-size: 12px; font-weight: 600; }
        .guest-table { margin-bottom: 0; }
        .guest-table thead th { padding: 13px 16px; border-bottom: 1px solid var(--guest-border); background: #f8fafd; color: #77849a; font-size: 11px; font-weight: 700; letter-spacing: .055em; text-transform: uppercase; white-space: nowrap; }
        .guest-table tbody td { padding: 16px; border-color: #eef2f7; }
        .guest-table tbody tr { transition: background-color .18s ease; }
        .guest-table tbody tr:hover { background: #fafcff; }
        .guest-avatar { display: inline-flex; align-items: center; justify-content: center; width: 43px; height: 43px; border-radius: 13px; background: linear-gradient(145deg, #e9efff, #dfe8ff); color: #426bd4; font-size: 16px; font-weight: 800; flex-shrink: 0; }
        .guest-avatar.walk-in { background: linear-gradient(145deg, #f0f2f5, #e7ebf0); color: #68758a; }
        .guest-name { color: var(--guest-ink); font-weight: 700; }
        .guest-name:hover { color: var(--guest-primary); }
        .guest-contact-line { display: flex; align-items: center; gap: 7px; color: #4f5d73; font-size: 13px; }
        .guest-contact-line + .guest-contact-line { margin-top: 5px; }
        .guest-contact-line i { width: 16px; color: #96a2b5; text-align: center; }
        .guest-source { display: inline-flex; align-items: center; gap: 6px; padding: 6px 9px; border-radius: 8px; font-size: 11px; font-weight: 700; }
        .guest-visits { display: inline-flex; align-items: center; gap: 7px; padding: 6px 10px; border-radius: 9px; background: #f4f7fb; color: #46546b; font-size: 12px; white-space: nowrap; }
        .guest-status { display: inline-flex; align-items: center; gap: 7px; padding: 6px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; white-space: nowrap; }
        .guest-status::before { width: 6px; height: 6px; border-radius: 50%; background: currentColor; content: ''; }
        .guest-detail-button { display: inline-flex; align-items: center; justify-content: center; width: 37px; height: 37px; border: 1px solid #dfe7f2; border-radius: 10px; background: #fff; color: #5d6b82; transition: .18s ease; }
        .guest-detail-button:hover { border-color: #5d87ff; background: #5d87ff; color: #fff; transform: translateX(2px); }
        .guest-mobile-card { padding: 18px; border: 1px solid var(--guest-border); border-radius: 15px; background: #fff; }
        .guest-mobile-card + .guest-mobile-card { margin-top: 12px; }
        .guest-mobile-meta { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; padding: 14px 0; margin: 14px 0; border-top: 1px solid #edf1f6; border-bottom: 1px solid #edf1f6; }
        .guest-mobile-meta small { display: block; margin-bottom: 3px; color: #8a96a9; font-size: 10px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
        .guest-empty-state { padding: 50px 20px; text-align: center; }
        .guest-empty-icon { display: inline-flex; align-items: center; justify-content: center; width: 68px; height: 68px; margin-bottom: 14px; border-radius: 20px; background: #edf3ff; color: #5d87ff; font-size: 31px; }
        @media (max-width: 767.98px) {
            .guest-panel .card-header, .guest-panel .card-body { padding: 18px; }
        }
    </style>
@endpush

@section('content')
    <div class="guest-directory">
        @php
            $metricCards = [
                ['Total Tamu', $metrics['total'], 'Seluruh profil', 'ti-users', '#5d87ff', route('receptionist.guests.index'), blank(request('source')) && blank(request('status'))],
                ['Punya Akun', $metrics['accounts'], 'Guest terdaftar', 'ti-user-check', '#49beff', route('receptionist.guests.index', ['source' => 'account']), request('source') === 'account' && blank(request('status'))],
                ['Pernah Check-in', $metrics['has_stayed'], 'Memiliki riwayat', 'ti-history', '#13deb9', route('receptionist.guests.index', ['status' => 'has_stayed']), request('status') === 'has_stayed'],
                ['Sedang Menginap', $metrics['active'], 'Stay aktif sekarang', 'ti-bed', '#ffae1f', route('receptionist.guests.index', ['status' => 'active']), request('status') === 'active'],
                ['Tamu Walk-in', $metrics['walk_in'], 'Datang tanpa akun', 'ti-walk', '#7c8fac', route('receptionist.guests.index', ['source' => 'walk_in']), request('source') === 'walk_in'],
            ];
        @endphp

        <div class="row">
            @foreach ($metricCards as [$label, $value, $helper, $icon, $color, $url, $selected])
                <div class="col-xl col-md-4 col-sm-6">
                    <a href="{{ $url }}" class="guest-metric-card {{ $selected ? 'is-selected' : '' }}" style="--metric-color: {{ $color }}">
                        <span class="guest-metric-line"></span>
                        <div class="card-body p-3 p-xxl-4">
                            <div class="d-flex align-items-center justify-content-between mb-3"><span class="guest-metric-icon"><i class="ti {{ $icon }} fs-6"></i></span><h3 class="fw-bolder mb-0">{{ number_format($value) }}</h3></div>
                            <strong class="d-block text-dark mb-1">{{ $label }}</strong><small class="text-muted">{{ $helper }}</small>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="card guest-panel mb-4">
            <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                <div><span class="guest-section-eyebrow">Pencarian &amp; penyaringan</span><h5 class="fw-semibold mb-0 mt-1">Temukan Tamu</h5></div>
                @if (request()->filled('search') || request()->filled('source') || request()->filled('status'))
                    <span class="active-filter-note"><i class="ti ti-filter-check"></i>Filter sedang diterapkan</span>
                @endif
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('receptionist.guests.index') }}" class="row g-3 align-items-end">
                    <div class="col-xl-5 col-lg-12">
                        <label for="guest-search" class="form-label small fw-semibold">Nama atau kontak tamu</label>
                        <div class="guest-search"><i class="ti ti-search"></i><input id="guest-search" type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama, nomor telepon, atau email..."></div>
                    </div>
                    <div class="col-xl-2 col-md-4">
                        <label for="guest-source" class="form-label small fw-semibold">Sumber tamu</label>
                        <select id="guest-source" name="source" class="form-select guest-filter-select"><option value="">Semua sumber</option><option value="account" @selected(request('source') === 'account')>Akun Guest</option><option value="walk_in" @selected(request('source') === 'walk_in')>Walk-in</option></select>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <label for="guest-status" class="form-label small fw-semibold">Riwayat kunjungan</label>
                        <select id="guest-status" name="status" class="form-select guest-filter-select"><option value="">Semua riwayat</option><option value="registered_only" @selected(request('status') === 'registered_only')>Register, belum check-in</option><option value="has_stayed" @selected(request('status') === 'has_stayed')>Pernah check-in</option><option value="active" @selected(request('status') === 'active')>Sedang menginap</option></select>
                    </div>
                    <div class="col-xl-2 col-md-4 d-flex gap-2">
                        <button class="btn btn-primary guest-filter-button flex-grow-1"><i class="ti ti-adjustments-horizontal me-1"></i>Terapkan</button>
                        <a href="{{ route('receptionist.guests.index') }}" class="btn btn-outline-secondary guest-filter-button d-inline-flex align-items-center" title="Reset filter" aria-label="Reset filter"><i class="ti ti-refresh"></i></a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card guest-panel mb-0">
            <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                <div><span class="guest-section-eyebrow">Database tamu</span><h5 class="fw-semibold mb-0 mt-1">Daftar Profil</h5></div>
                <span class="text-muted small">Menampilkan <strong class="text-dark">{{ $guests->firstItem() ?? 0 }}–{{ $guests->lastItem() ?? 0 }}</strong> dari <strong class="text-dark">{{ number_format($guests->total()) }}</strong> tamu</span>
            </div>

            <div class="d-none d-lg-block table-responsive">
                <table class="table guest-table align-middle">
                    <thead><tr><th>Profil Tamu</th><th>Kontak</th><th>Sumber</th><th>Kunjungan</th><th>Terakhir Check-in</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                    <tbody>
                        @forelse ($guests as $guest)
                            @php
                                $detailRoute = $guest['type'] === 'account' ? route('receptionist.guests.accounts.show', $guest['id']) : route('receptionist.guests.walk-ins.show', $guest['id']);
                                $isAccount = $guest['type'] === 'account';
                            @endphp
                            <tr>
                                <td><div class="d-flex align-items-center gap-3"><span class="guest-avatar {{ $isAccount ? '' : 'walk-in' }}">{{ str($guest['name'] ?: 'T')->substr(0, 1)->upper() }}</span><div><a href="{{ $detailRoute }}" class="guest-name">{{ $guest['name'] ?: 'Nama tidak tersedia' }}</a><small class="d-block text-muted mt-1">{{ $isAccount ? 'Terdaftar '.$guest['registered_at']?->translatedFormat('d M Y') : 'Profil dari data check-in' }}</small></div></div></td>
                                <td><div class="guest-contact-line"><i class="ti ti-phone"></i><span>{{ $guest['phone'] ?: 'Nomor tidak tersedia' }}</span></div><div class="guest-contact-line"><i class="ti ti-mail"></i><span>{{ $guest['email'] ?: 'Email tidak tersedia' }}</span></div></td>
                                <td><span class="guest-source {{ $isAccount ? 'bg-light-primary text-primary' : 'bg-light-secondary text-secondary' }}"><i class="ti {{ $isAccount ? 'ti-user-check' : 'ti-walk' }}"></i>{{ $isAccount ? 'Akun Guest' : 'Walk-in' }}</span></td>
                                <td><span class="guest-visits"><i class="ti ti-history"></i><strong>{{ $guest['checkin_count'] }}</strong> kali</span></td>
                                <td>@if ($guest['last_check_in_at'])<span class="fw-semibold text-dark">{{ $guest['last_check_in_at']->translatedFormat('d M Y') }}</span><small class="d-block text-muted mt-1">Pukul {{ $guest['last_check_in_at']->format('H:i') }}</small>@else<span class="text-muted">Belum pernah</span>@endif</td>
                                <td>
                                    @if ($guest['has_active_stay'])<span class="guest-status bg-light-success text-success">Sedang Menginap</span>
                                    @elseif ($guest['checkin_count'] > 0)<span class="guest-status bg-light-info text-info">Pernah Menginap</span>
                                    @else<span class="guest-status bg-light-warning text-warning">Belum Check-in</span>@endif
                                </td>
                                <td class="text-end"><a href="{{ $detailRoute }}" class="guest-detail-button" title="Lihat profil {{ $guest['name'] }}" aria-label="Lihat detail tamu"><i class="ti ti-arrow-right"></i></a></td>
                            </tr>
                        @empty
                            <tr><td colspan="7"><div class="guest-empty-state"><span class="guest-empty-icon"><i class="ti ti-users-off"></i></span><h5 class="fw-semibold mb-1">Tamu tidak ditemukan</h5><p class="text-muted mb-3">Coba ubah kata pencarian atau pilihan filter Anda.</p><a href="{{ route('receptionist.guests.index') }}" class="btn btn-sm btn-light-primary text-primary">Reset pencarian</a></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-body d-lg-none">
                @forelse ($guests as $guest)
                    @php
                        $detailRoute = $guest['type'] === 'account' ? route('receptionist.guests.accounts.show', $guest['id']) : route('receptionist.guests.walk-ins.show', $guest['id']);
                        $isAccount = $guest['type'] === 'account';
                    @endphp
                    <article class="guest-mobile-card">
                        <div class="d-flex align-items-start gap-3"><span class="guest-avatar {{ $isAccount ? '' : 'walk-in' }}">{{ str($guest['name'] ?: 'T')->substr(0, 1)->upper() }}</span><div class="flex-grow-1 min-w-0"><a href="{{ $detailRoute }}" class="guest-name d-block text-truncate">{{ $guest['name'] ?: 'Nama tidak tersedia' }}</a><span class="guest-source {{ $isAccount ? 'bg-light-primary text-primary' : 'bg-light-secondary text-secondary' }} mt-2"><i class="ti {{ $isAccount ? 'ti-user-check' : 'ti-walk' }}"></i>{{ $isAccount ? 'Akun Guest' : 'Walk-in' }}</span></div><a href="{{ $detailRoute }}" class="guest-detail-button" aria-label="Lihat detail tamu"><i class="ti ti-arrow-right"></i></a></div>
                        <div class="guest-mobile-meta"><div><small>Nomor telepon</small><span class="text-dark">{{ $guest['phone'] ?: '-' }}</span></div><div><small>Total kunjungan</small><strong class="text-dark">{{ $guest['checkin_count'] }} kali</strong></div><div><small>Email</small><span class="text-dark text-break">{{ $guest['email'] ?: '-' }}</span></div><div><small>Check-in terakhir</small><span class="text-dark">{{ $guest['last_check_in_at']?->translatedFormat('d M Y') ?? 'Belum pernah' }}</span></div></div>
                        @if ($guest['has_active_stay'])<span class="guest-status bg-light-success text-success">Sedang Menginap</span>
                        @elseif ($guest['checkin_count'] > 0)<span class="guest-status bg-light-info text-info">Pernah Menginap</span>
                        @else<span class="guest-status bg-light-warning text-warning">Belum Check-in</span>@endif
                    </article>
                @empty
                    <div class="guest-empty-state"><span class="guest-empty-icon"><i class="ti ti-users-off"></i></span><h5 class="fw-semibold mb-1">Tamu tidak ditemukan</h5><p class="text-muted mb-3">Coba ubah kata pencarian atau pilihan filter Anda.</p><a href="{{ route('receptionist.guests.index') }}" class="btn btn-sm btn-light-primary text-primary">Reset pencarian</a></div>
                @endforelse
            </div>

            @if ($guests->hasPages())<div class="card-footer bg-white border-top px-4 py-3">{{ $guests->links() }}</div>@endif
        </div>
    </div>
@endsection
