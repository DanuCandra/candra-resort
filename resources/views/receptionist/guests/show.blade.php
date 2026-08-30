@extends('layouts.main')

@section('title', 'Detail Tamu - '.$guest['name'])

@section('content')
    <x-dashboard.page-heading :title="$guest['name']" description="Profil kontak dan riwayat kunjungan tamu." :back="route('receptionist.guests.index')">
        <span class="badge {{ $guestType === 'account' ? 'bg-light-primary text-primary' : 'bg-light-secondary text-secondary' }} px-3 py-2">
            <i class="ti {{ $guestType === 'account' ? 'ti-user-check' : 'ti-walk' }} me-1"></i>{{ $guestType === 'account' ? 'Akun Guest' : 'Tamu Walk-in' }}
        </span>
    </x-dashboard.page-heading>

    @php
        $paidTotal = $reservations->sum(fn ($reservation) => (float) ($reservation->paid_total ?? 0));
        $totalNights = $reservations->sum('total_nights');
        $activeStay = $stays->first(fn ($stay) => $stay->status === \App\Enums\StayStatus::Active);
        $reservationStatusLabels = [
            'pending_payment' => 'Menunggu Pembayaran', 'paid' => 'Sudah Dibayar', 'confirmed' => 'Dikonfirmasi',
            'checked_in' => 'Check-in', 'checked_out' => 'Check-out', 'cancelled' => 'Dibatalkan', 'no_show' => 'No Show',
        ];
    @endphp

    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <span class="round-80 rounded-circle bg-light-primary d-inline-flex align-items-center justify-content-center"><i class="ti ti-user fs-10 text-primary"></i></span>
                        <h4 class="fw-semibold mt-3 mb-1">{{ $guest['name'] }}</h4>
                        @if ($activeStay)
                            <span class="badge bg-light-success text-success">Sedang Menginap · Kamar {{ $activeStay->room?->room_number }}</span>
                        @elseif ($stays->isNotEmpty())
                            <span class="badge bg-light-info text-info">Pernah Menginap</span>
                        @else
                            <span class="badge bg-light-warning text-warning">Belum Pernah Check-in</span>
                        @endif
                    </div>
                    <div class="d-flex gap-3 py-3 border-top"><i class="ti ti-phone fs-6 text-primary"></i><div><small class="text-muted d-block">Nomor Telepon</small><strong>{{ $guest['phone'] ?: 'Tidak tersedia' }}</strong></div></div>
                    <div class="d-flex gap-3 py-3 border-top"><i class="ti ti-mail fs-6 text-primary"></i><div><small class="text-muted d-block">Email</small><strong>{{ $guest['email'] ?: 'Tidak tersedia' }}</strong></div></div>
                    <div class="d-flex gap-3 py-3 border-top"><i class="ti ti-id fs-6 text-primary"></i><div><small class="text-muted d-block">Jenis Data</small><strong>{{ $guestType === 'account' ? 'Akun website Guest' : 'Snapshot check-in walk-in' }}</strong></div></div>
                    @if ($guest['registered_at'])
                        <div class="d-flex gap-3 py-3 border-top"><i class="ti ti-calendar-plus fs-6 text-primary"></i><div><small class="text-muted d-block">Terdaftar Sejak</small><strong>{{ $guest['registered_at']->translatedFormat('d F Y H:i') }}</strong></div></div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-8">
            <div class="row">
                @foreach ([
                    ['Total Reservasi', $reservations->count(), 'ti-calendar-stats', 'primary'],
                    ['Jumlah Check-in', $stays->count(), 'ti-login', 'success'],
                    ['Total Malam', $totalNights, 'ti-moon', 'info'],
                    ['Pembayaran Lunas', 'Rp'.number_format($paidTotal, 0, ',', '.'), 'ti-cash', 'warning'],
                ] as [$label, $value, $icon, $color])
                    <div class="col-md-6"><div class="card"><div class="card-body d-flex align-items-center"><span class="round-48 rounded bg-light-{{ $color }} d-flex align-items-center justify-content-center"><i class="ti {{ $icon }} fs-6 text-{{ $color }}"></i></span><div class="ms-3"><span class="text-muted">{{ $label }}</span><h4 class="fw-semibold mb-0">{{ $value }}</h4></div></div></div></div>
                @endforeach
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="fw-semibold mb-1">Riwayat Check-in</h5>
                    <p class="text-muted mb-4">Kunjungan yang benar-benar sudah diproses check-in oleh Receptionist.</p>
                    @forelse ($stays as $stay)
                        <div class="d-flex gap-3 pb-4 position-relative">
                            <span class="round-40 rounded-circle {{ $stay->status === \App\Enums\StayStatus::Active ? 'bg-light-success text-success' : 'bg-light-primary text-primary' }} d-flex align-items-center justify-content-center flex-shrink-0"><i class="ti ti-bed"></i></span>
                            <div class="flex-grow-1 border-bottom pb-3">
                                <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                                    <div><a href="{{ route('receptionist.reservations.show', $stay->reservation_id) }}" class="fw-semibold">{{ $stay->reservation?->booking_code ?? 'Reservasi #'.$stay->reservation_id }}</a><span class="d-block text-muted">Kamar {{ $stay->room?->room_number ?? '-' }} · {{ $stay->reservation?->total_nights ?? 0 }} malam</span></div>
                                    <span class="badge align-self-start {{ $stay->status === \App\Enums\StayStatus::Active ? 'bg-light-success text-success' : 'bg-light-secondary text-secondary' }}">{{ $stay->status === \App\Enums\StayStatus::Active ? 'Aktif' : 'Selesai' }}</span>
                                </div>
                                <small class="text-muted"><i class="ti ti-login me-1"></i>{{ $stay->check_in_at?->translatedFormat('d M Y H:i') ?? '-' }} <span class="mx-2">→</span><i class="ti ti-logout me-1"></i>{{ $stay->check_out_at?->translatedFormat('d M Y H:i') ?? 'Masih menginap' }}</small>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4"><i class="ti ti-calendar-off fs-8 d-block mb-2"></i>Tamu ini sudah memiliki akun, tetapi belum pernah check-in.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="fw-semibold mb-3">Riwayat Reservasi</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Reservasi</th><th>Tanggal Menginap</th><th>Kamar</th><th>Sumber</th><th>Status</th><th class="text-end">Total / Dibayar</th><th></th></tr></thead>
                    <tbody>
                        @forelse ($reservations as $reservation)
                            <tr>
                                <td><strong>{{ $reservation->booking_code }}</strong><small class="d-block text-muted">{{ $reservation->created_at->translatedFormat('d M Y H:i') }}</small></td>
                                <td>{{ $reservation->check_in_date->format('d/m/Y') }} - {{ $reservation->check_out_date->format('d/m/Y') }}<small class="d-block text-muted">{{ $reservation->total_nights }} malam</small></td>
                                <td>{{ $reservation->roomType?->name ?? '-' }}<small class="d-block text-muted">{{ $reservation->room?->room_number ? 'Kamar '.$reservation->room->room_number : 'Belum ditentukan' }}</small></td>
                                <td><span class="badge {{ $reservation->source === 'walk_in' ? 'bg-light-secondary text-secondary' : 'bg-light-primary text-primary' }}">{{ $reservation->source === 'walk_in' ? 'Walk-in' : 'Online' }}</span></td>
                                <td>{{ $reservationStatusLabels[$reservation->status->value] ?? str($reservation->status->value)->replace('_', ' ')->title() }}</td>
                                <td class="text-end"><strong>Rp{{ number_format((float) $reservation->grand_total, 0, ',', '.') }}</strong><small class="d-block text-success">Dibayar Rp{{ number_format((float) ($reservation->paid_total ?? 0), 0, ',', '.') }}</small></td>
                                <td class="text-end"><a href="{{ route('receptionist.reservations.show', $reservation) }}" class="btn btn-sm btn-outline-primary"><i class="ti ti-eye"></i></a></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-5">Belum ada reservasi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
