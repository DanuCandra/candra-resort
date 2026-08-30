@php
    $chartConfig = [
        'series' => [['name' => 'Okupansi', 'data' => collect($occupancy['daily'])->pluck('rate')->values()]],
        'chart' => ['type' => 'area', 'height' => 310, 'toolbar' => ['show' => false], 'fontFamily' => 'inherit'],
        'colors' => ['#13DEB9'], 'stroke' => ['curve' => 'smooth', 'width' => 3],
        'fill' => ['type' => 'gradient', 'gradient' => ['opacityFrom' => .3, 'opacityTo' => .04]],
        'dataLabels' => ['enabled' => false],
        'xaxis' => ['categories' => collect($occupancy['daily'])->map(fn ($day) => $day['date']->translatedFormat('d M'))->values()],
        'yaxis' => ['min' => 0, 'max' => 100], 'grid' => ['borderColor' => '#edf2f9'],
    ];
@endphp

<div class="row">
    @foreach ([
        ['Okupansi', $occupancy['rate'].'%', 'ti-chart-pie', 'primary'],
        ['Room-night Terisi', number_format($occupancy['occupied_room_nights']), 'ti-bed', 'success'],
        ['Kapasitas Room-night', number_format($occupancy['capacity_room_nights']), 'ti-building', 'info'],
        ['Room-night Kosong', number_format(max(0, $occupancy['capacity_room_nights'] - $occupancy['occupied_room_nights'])), 'ti-door', 'warning'],
    ] as [$label, $value, $icon, $color])
        <div class="col-xl-3 col-md-6"><div class="card"><div class="card-body d-flex align-items-center"><span class="round-48 rounded bg-light-{{ $color }} d-flex align-items-center justify-content-center"><i class="ti {{ $icon }} fs-6 text-{{ $color }}"></i></span><div class="ms-3"><span class="text-muted">{{ $label }}</span><h4 class="mb-0 fw-semibold">{{ $value }}</h4></div></div></div></div>
    @endforeach
</div>

<div class="card"><div class="card-body"><h5 class="fw-semibold mb-1">Okupansi Harian</h5><p class="text-muted">Persentase kamar yang benar-benar ditempati berdasarkan data check-in.</p><div id="owner-report-chart" data-chart="{{ json_encode($chartConfig) }}"></div></div></div>
<div class="card"><div class="card-body"><h5 class="fw-semibold mb-3">Rincian Harian</h5><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Tanggal</th><th class="text-end">Kamar Terisi</th><th class="text-end">Kapasitas</th><th class="text-end">Okupansi</th></tr></thead><tbody>
    @forelse ($rows as $day)
        <tr><td>{{ $day['date']->translatedFormat('l, d F Y') }}</td><td class="text-end">{{ $day['occupied'] }}</td><td class="text-end">{{ $day['capacity'] }}</td><td class="text-end"><span class="badge bg-light-{{ $day['rate'] >= 70 ? 'success' : ($day['rate'] >= 40 ? 'warning' : 'secondary') }} text-{{ $day['rate'] >= 70 ? 'success' : ($day['rate'] >= 40 ? 'warning' : 'secondary') }}">{{ $day['rate'] }}%</span></td></tr>
    @empty
        <tr><td colspan="4" class="text-center text-muted py-4">Belum ada periode okupansi yang dapat dihitung.</td></tr>
    @endforelse
</tbody></table></div>@if($rows->hasPages())<div class="mt-3">{{ $rows->links() }}</div>@endif<small class="text-muted">Catatan: kapasitas historis menggunakan jumlah kamar aktif saat laporan dibuka karena schema belum menyimpan riwayat aktivasi kamar.</small></div></div>
