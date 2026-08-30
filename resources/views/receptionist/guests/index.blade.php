@extends('layouts.main')

@section('title', 'Direktori Tamu')

@section('content')
    <x-dashboard.page-heading title="Direktori Tamu" description="Akun Guest yang terdaftar dan riwayat tamu walk-in yang pernah check-in." />

    <div class="alert alert-light-primary text-primary border-0 d-flex align-items-start gap-3">
        <i class="ti ti-info-circle fs-6 mt-1"></i>
        <div>
            <strong>Sumber data tamu</strong>
            <div class="small">Akun Guest tetap ditampilkan meskipun belum pernah menginap. Tamu walk-in muncul setelah check-in dan dikelompokkan berdasarkan nomor telepon.</div>
        </div>
    </div>

    <div class="row">
        @foreach ([
            ['Total Tamu', $metrics['total'], 'ti-users', 'primary'],
            ['Punya Akun', $metrics['accounts'], 'ti-user-check', 'info'],
            ['Pernah Check-in', $metrics['has_stayed'], 'ti-login', 'success'],
            ['Sedang Menginap', $metrics['active'], 'ti-bed', 'warning'],
            ['Tamu Walk-in', $metrics['walk_in'], 'ti-walk', 'secondary'],
        ] as [$label, $value, $icon, $color])
            <div class="col-xl col-md-4 col-sm-6">
                <div class="card">
                    <div class="card-body d-flex align-items-center">
                        <span class="round-48 rounded bg-light-{{ $color }} d-flex align-items-center justify-content-center flex-shrink-0"><i class="ti {{ $icon }} fs-6 text-{{ $color }}"></i></span>
                        <div class="ms-3"><span class="text-muted d-block">{{ $label }}</span><h4 class="fw-semibold mb-0">{{ number_format($value) }}</h4></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('receptionist.guests.index') }}" class="row g-2 mb-4">
                <div class="col-lg-5">
                    <input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama, nomor telepon, atau email...">
                </div>
                <div class="col-lg-2 col-md-4">
                    <select name="source" class="form-select">
                        <option value="">Semua sumber</option>
                        <option value="account" @selected(request('source') === 'account')>Akun Guest</option>
                        <option value="walk_in" @selected(request('source') === 'walk_in')>Walk-in</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-4">
                    <select name="status" class="form-select">
                        <option value="">Semua riwayat</option>
                        <option value="registered_only" @selected(request('status') === 'registered_only')>Register, belum check-in</option>
                        <option value="has_stayed" @selected(request('status') === 'has_stayed')>Pernah check-in</option>
                        <option value="active" @selected(request('status') === 'active')>Sedang menginap</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 d-flex gap-2">
                    <button class="btn btn-outline-primary flex-grow-1"><i class="ti ti-filter me-1"></i>Filter</button>
                    <a href="{{ route('receptionist.guests.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="ti ti-refresh"></i></a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Tamu</th><th>Kontak</th><th>Sumber Data</th><th>Riwayat Check-in</th><th>Kunjungan Terakhir</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @forelse ($guests as $guest)
                            @php
                                $detailRoute = $guest['type'] === 'account'
                                    ? route('receptionist.guests.accounts.show', $guest['id'])
                                    : route('receptionist.guests.walk-ins.show', $guest['id']);
                            @endphp
                            <tr>
                                <td><a href="{{ $detailRoute }}" class="fw-semibold text-dark">{{ $guest['name'] }}</a><small class="d-block text-muted">{{ $guest['type'] === 'account' ? 'Terdaftar '.$guest['registered_at']?->translatedFormat('d M Y') : 'Tamu tanpa akun' }}</small></td>
                                <td><span>{{ $guest['phone'] ?: '-' }}</span><small class="d-block text-muted">{{ $guest['email'] ?: 'Email tidak tersedia' }}</small></td>
                                <td><span class="badge {{ $guest['type'] === 'account' ? 'bg-light-primary text-primary' : 'bg-light-secondary text-secondary' }}">{{ $guest['type'] === 'account' ? 'Akun Guest' : 'Walk-in' }}</span></td>
                                <td><strong>{{ $guest['checkin_count'] }}</strong> kali</td>
                                <td>{{ $guest['last_check_in_at']?->translatedFormat('d M Y H:i') ?? 'Belum pernah' }}</td>
                                <td>
                                    @if ($guest['has_active_stay'])
                                        <span class="badge bg-light-success text-success">Sedang Menginap</span>
                                    @elseif ($guest['checkin_count'] > 0)
                                        <span class="badge bg-light-info text-info">Pernah Menginap</span>
                                    @else
                                        <span class="badge bg-light-warning text-warning">Belum Check-in</span>
                                    @endif
                                </td>
                                <td class="text-end"><a href="{{ $detailRoute }}" class="btn btn-sm btn-outline-primary"><i class="ti ti-eye me-1"></i>Detail</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-5"><i class="ti ti-users-off fs-7 d-block mb-2"></i>Tidak ada tamu yang sesuai dengan filter.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $guests->links() }}
        </div>
    </div>
@endsection
