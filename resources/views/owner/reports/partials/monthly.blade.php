@php
    $chartConfig = [
        'series' => [
            ['name' => 'Pendapatan', 'type' => 'column', 'data' => $rows->pluck('revenue')->values()],
            ['name' => 'Okupansi', 'type' => 'line', 'data' => $rows->pluck('occupancy_rate')->values()],
        ],
        'chart' => ['type' => 'line', 'height' => 340, 'toolbar' => ['show' => false], 'fontFamily' => 'inherit'],
        'colors' => ['#5D87FF', '#13DEB9'], 'stroke' => ['width' => [0, 3], 'curve' => 'smooth'],
        'dataLabels' => ['enabled' => false], 'xaxis' => ['categories' => $rows->pluck('month')->values()],
        'yaxis' => [['labels' => ['show' => true]], ['opposite' => true, 'min' => 0, 'max' => 100]],
        'legend' => ['position' => 'top', 'horizontalAlign' => 'right'], 'grid' => ['borderColor' => '#edf2f9'],
    ];
    $totals = [
        'reservations' => $rows->sum('reservations'), 'cancelled' => $rows->sum('cancelled'),
        'revenue' => $rows->sum('revenue'), 'occupied' => $rows->sum('occupied_room_nights'),
        'capacity' => $rows->sum('capacity_room_nights'),
    ];
    $overallOccupancy = $totals['capacity'] > 0 ? round(($totals['occupied'] / $totals['capacity']) * 100, 1) : 0;
@endphp
<div class="row">
    @foreach ([
        ['Total Reservasi', number_format($totals['reservations']), 'ti-calendar-stats', 'primary'],
        ['Pendapatan Kas', 'Rp'.number_format($totals['revenue'], 0, ',', '.'), 'ti-cash', 'success'],
        ['Okupansi', $overallOccupancy.'%', 'ti-chart-pie', 'info'],
        ['Pembatalan', number_format($totals['cancelled']), 'ti-calendar-x', 'danger'],
    ] as [$label, $value, $icon, $color])
        <div class="col-xl-3 col-md-6"><div class="card"><div class="card-body d-flex align-items-center"><span class="round-48 rounded bg-light-{{ $color }} d-flex align-items-center justify-content-center"><i class="ti {{ $icon }} fs-6 text-{{ $color }}"></i></span><div class="ms-3 min-w-0"><span class="text-muted">{{ $label }}</span><h4 class="mb-0 fw-semibold text-truncate">{{ $value }}</h4></div></div></div></div>
    @endforeach
</div>
<div class="card"><div class="card-body"><h5 class="fw-semibold mb-1">Perbandingan Bulanan</h5><p class="text-muted">Pendapatan kas dibandingkan dengan tingkat okupansi aktual.</p><div id="owner-report-chart" data-chart="{{ json_encode($chartConfig) }}"></div></div></div>
<div class="card"><div class="card-body"><h5 class="fw-semibold mb-3">Ringkasan per Bulan</h5><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Bulan</th><th class="text-end">Reservasi</th><th class="text-end">Dibatalkan</th><th class="text-end">Pendapatan</th><th class="text-end">Room-night</th><th class="text-end">Okupansi</th></tr></thead><tbody>
    @forelse ($rows as $row)
        <tr><td class="fw-semibold">{{ $row['month'] }}</td><td class="text-end">{{ number_format($row['reservations']) }}</td><td class="text-end">{{ number_format($row['cancelled']) }}</td><td class="text-end fw-semibold">Rp{{ number_format($row['revenue'], 0, ',', '.') }}</td><td class="text-end">{{ $row['occupied_room_nights'] }} / {{ $row['capacity_room_nights'] }}</td><td class="text-end"><span class="badge bg-light-primary text-primary">{{ $row['occupancy_rate'] }}%</span></td></tr>
    @empty
        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data bulanan.</td></tr>
    @endforelse
</tbody></table></div></div></div>
