@php
    $chartConfig = [
        'series' => [['name' => 'Nilai Pesanan', 'data' => $serviceSummary->pluck('amount')->values()]],
        'chart' => ['type' => 'bar', 'height' => 320, 'toolbar' => ['show' => false], 'fontFamily' => 'inherit'],
        'colors' => ['#49BEFF'], 'plotOptions' => ['bar' => ['borderRadius' => 5, 'horizontal' => true]],
        'dataLabels' => ['enabled' => false], 'xaxis' => ['categories' => $serviceSummary->pluck('label')->values()],
        'grid' => ['borderColor' => '#edf2f9'],
    ];
@endphp
<div class="row">
    @foreach ([
        ['Pesanan F&B Selesai', number_format($metrics['food_orders']), 'ti-tools-kitchen-2', 'primary'],
        ['Nilai F&B', 'Rp'.number_format($metrics['food_amount'], 0, ',', '.'), 'ti-cash', 'success'],
        ['Pesanan Layanan Selesai', number_format($metrics['service_orders']), 'ti-bell', 'info'],
        ['Nilai Layanan', 'Rp'.number_format($metrics['service_amount'], 0, ',', '.'), 'ti-receipt', 'warning'],
    ] as [$label, $value, $icon, $color])
        <div class="col-xl-3 col-md-6"><div class="card"><div class="card-body d-flex align-items-center"><span class="round-48 rounded bg-light-{{ $color }} d-flex align-items-center justify-content-center"><i class="ti {{ $icon }} fs-6 text-{{ $color }}"></i></span><div class="ms-3 min-w-0"><span class="text-muted">{{ $label }}</span><h4 class="mb-0 fw-semibold text-truncate">{{ $value }}</h4></div></div></div></div>
    @endforeach
</div>
<div class="row">
    <div class="col-xl-8"><div class="card"><div class="card-body"><h5 class="fw-semibold mb-1">Kontribusi Layanan</h5><p class="text-muted">Nilai snapshot transaksi selesai; bukan perubahan harga master saat ini.</p><div id="owner-report-chart" data-chart="{{ json_encode($chartConfig) }}"></div></div></div></div>
    <div class="col-xl-4"><div class="card"><div class="card-body"><h5 class="fw-semibold mb-3">Ringkasan</h5>
        @forelse ($serviceSummary as $summary)
            <div class="py-2 border-bottom"><div class="d-flex justify-content-between"><span>{{ $summary['label'] }}</span><strong>Rp{{ number_format($summary['amount'], 0, ',', '.') }}</strong></div><small class="text-muted">{{ $summary['count'] }} pesanan selesai</small></div>
        @empty
            <p class="text-muted">Belum ada layanan selesai.</p>
        @endforelse
    </div></div></div>
</div>
<div class="card"><div class="card-body"><h5 class="fw-semibold mb-3">Pesanan Selesai Terbaru</h5><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Jenis</th><th>Kode</th><th>Layanan</th><th>Kamar</th><th>Selesai Pada</th><th class="text-end">Jumlah</th></tr></thead><tbody>
    @foreach ($foodOrders as $order)
        <tr><td><span class="badge bg-light-primary text-primary">F&B</span></td><td class="fw-semibold">{{ $order->order_code }}</td><td>Makanan & Minuman</td><td>{{ $order->room?->room_number ?? '-' }}</td><td>{{ $order->completed_at?->translatedFormat('d M Y H:i') ?? '-' }}</td><td class="text-end fw-semibold">Rp{{ number_format((float) $order->total_amount, 0, ',', '.') }}</td></tr>
    @endforeach
    @foreach ($serviceOrders as $order)
        <tr><td><span class="badge bg-light-info text-info">Layanan</span></td><td class="fw-semibold">{{ $order->order_code }}</td><td>{{ $order->service?->name ?? '-' }}</td><td>{{ $order->room?->room_number ?? '-' }}</td><td>{{ $order->completed_at?->translatedFormat('d M Y H:i') ?? '-' }}</td><td class="text-end fw-semibold">Rp{{ number_format((float) $order->total_amount, 0, ',', '.') }}</td></tr>
    @endforeach
    @if ($foodOrders->isEmpty() && $serviceOrders->isEmpty())
        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada pesanan selesai pada periode ini.</td></tr>
    @endif
</tbody></table></div>
@if($foodOrders->hasPages())<div class="mt-3"><small class="text-muted d-block mb-2">Halaman pesanan F&amp;B</small>{{ $foodOrders->links() }}</div>@endif
@if($serviceOrders->hasPages())<div class="mt-3"><small class="text-muted d-block mb-2">Halaman pesanan layanan</small>{{ $serviceOrders->links() }}</div>@endif
</div></div>
