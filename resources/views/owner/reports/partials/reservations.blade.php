@php
    $statusLabels = [
        'pending_payment' => 'Menunggu Pembayaran', 'paid' => 'Sudah Dibayar', 'confirmed' => 'Dikonfirmasi',
        'checked_in' => 'Check-in', 'checked_out' => 'Check-out', 'cancelled' => 'Dibatalkan', 'no_show' => 'No Show',
    ];
    $statusColors = [
        'pending_payment' => 'warning', 'paid' => 'info', 'confirmed' => 'primary', 'checked_in' => 'success',
        'checked_out' => 'secondary', 'cancelled' => 'danger', 'no_show' => 'dark',
    ];
    $chartConfig = [
        'series' => [['name' => 'Reservasi', 'data' => $trend['reservations']]],
        'chart' => ['type' => 'area', 'height' => 300, 'toolbar' => ['show' => false], 'fontFamily' => 'inherit'],
        'colors' => ['#5D87FF'], 'stroke' => ['curve' => 'smooth', 'width' => 3],
        'fill' => ['type' => 'gradient', 'gradient' => ['opacityFrom' => .3, 'opacityTo' => .04]],
        'dataLabels' => ['enabled' => false], 'xaxis' => ['categories' => $trend['labels']],
        'yaxis' => ['min' => 0, 'forceNiceScale' => true, 'decimalsInFloat' => 0],
        'grid' => ['borderColor' => '#edf2f9'],
    ];
@endphp

<div class="row">
    @foreach ([
        ['Total Reservasi', number_format($metrics['total']), 'ti-calendar-stats', 'primary'],
        ['Booking Online', number_format($metrics['online']), 'ti-world-www', 'info'],
        ['Walk-in', number_format($metrics['walk_in']), 'ti-walk', 'warning'],
        ['Nilai Reservasi', 'Rp'.number_format($metrics['value'], 0, ',', '.'), 'ti-receipt', 'success'],
    ] as [$label, $value, $icon, $color])
        <div class="col-xl-3 col-md-6">
            <div class="card"><div class="card-body d-flex align-items-center"><span class="round-48 rounded bg-light-{{ $color }} d-flex align-items-center justify-content-center"><i class="ti {{ $icon }} fs-6 text-{{ $color }}"></i></span><div class="ms-3"><span class="text-muted">{{ $label }}</span><h4 class="mb-0 fw-semibold">{{ $value }}</h4></div></div></div>
        </div>
    @endforeach
</div>

<div class="row">
    <div class="col-xl-8"><div class="card"><div class="card-body"><h5 class="fw-semibold mb-1">Tren Reservasi</h5><p class="text-muted">Jumlah reservasi berdasarkan tanggal dibuat.</p><div id="owner-report-chart" data-chart="{{ json_encode($chartConfig) }}"></div></div></div></div>
    <div class="col-xl-4"><div class="card"><div class="card-body"><h5 class="fw-semibold mb-3">Status Reservasi</h5>
        @forelse ($statusSummary as $status => $count)
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom"><span>{{ $statusLabels[$status] ?? str($status)->replace('_', ' ')->title() }}</span><span class="badge bg-light-{{ $statusColors[$status] ?? 'secondary' }} text-{{ $statusColors[$status] ?? 'secondary' }}">{{ $count }}</span></div>
        @empty
            <p class="text-muted mb-0">Belum ada data pada periode ini.</p>
        @endforelse
    </div></div></div>
</div>

<div class="card"><div class="card-body"><h5 class="fw-semibold mb-3">Detail Reservasi</h5><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Kode</th><th>Tamu</th><th>Kamar</th><th>Menginap</th><th>Sumber</th><th>Status</th><th class="text-end">Total</th></tr></thead><tbody>
    @forelse ($rows as $reservation)
        <tr><td class="fw-semibold">{{ $reservation->booking_code }}</td><td>{{ $reservation->guest_name }}<small class="d-block text-muted">{{ $reservation->created_at->translatedFormat('d M Y H:i') }}</small></td><td>{{ $reservation->roomType?->name ?? '-' }}<small class="d-block text-muted">{{ $reservation->room?->room_number ? 'Kamar '.$reservation->room->room_number : 'Belum ditentukan' }}</small></td><td>{{ $reservation->check_in_date->format('d/m/Y') }} - {{ $reservation->check_out_date->format('d/m/Y') }}</td><td>{{ $reservation->source === 'walk_in' ? 'Walk-in' : 'Online' }}</td><td><span class="badge bg-light-{{ $statusColors[$reservation->status->value] ?? 'secondary' }} text-{{ $statusColors[$reservation->status->value] ?? 'secondary' }}">{{ $statusLabels[$reservation->status->value] ?? $reservation->status->value }}</span></td><td class="text-end fw-semibold">Rp{{ number_format((float) $reservation->grand_total, 0, ',', '.') }}</td></tr>
    @empty
        <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada reservasi pada periode ini.</td></tr>
    @endforelse
</tbody></table></div><div class="no-print">{{ $rows->links() }}</div></div></div>
