@php
    $chartConfig = [
        'series' => [['name' => 'Pendapatan', 'data' => $trend['revenue']]],
        'chart' => ['type' => 'bar', 'height' => 310, 'toolbar' => ['show' => false], 'fontFamily' => 'inherit'],
        'colors' => ['#5D87FF'], 'plotOptions' => ['bar' => ['borderRadius' => 5, 'columnWidth' => '45%']],
        'dataLabels' => ['enabled' => false], 'xaxis' => ['categories' => $trend['labels']],
        'grid' => ['borderColor' => '#edf2f9'],
    ];
@endphp
<div class="row">
    @foreach ([
        ['Pendapatan Kas', 'Rp'.number_format($metrics['total'], 0, ',', '.'), 'ti-cash', 'success'],
        ['Transaksi Lunas', number_format($metrics['transactions']), 'ti-circle-check', 'primary'],
        ['Rata-rata Transaksi', 'Rp'.number_format($metrics['average'], 0, ',', '.'), 'ti-chart-bar', 'info'],
        ['Melalui Midtrans', 'Rp'.number_format($metrics['midtrans'], 0, ',', '.'), 'ti-world-dollar', 'warning'],
    ] as [$label, $value, $icon, $color])
        <div class="col-xl-3 col-md-6"><div class="card"><div class="card-body d-flex align-items-center"><span class="round-48 rounded bg-light-{{ $color }} d-flex align-items-center justify-content-center"><i class="ti {{ $icon }} fs-6 text-{{ $color }}"></i></span><div class="ms-3 min-w-0"><span class="text-muted">{{ $label }}</span><h4 class="mb-0 fw-semibold text-truncate">{{ $value }}</h4></div></div></div></div>
    @endforeach
</div>
<div class="card"><div class="card-body"><h5 class="fw-semibold mb-1">Tren Pendapatan</h5><p class="text-muted">Hanya pembayaran dengan status lunas, dikelompokkan berdasarkan paid_at.</p><div id="owner-report-chart" data-chart="{{ json_encode($chartConfig) }}"></div></div></div>
<div class="card"><div class="card-body"><h5 class="fw-semibold mb-3">Transaksi Pendapatan</h5><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Kode</th><th>Reservasi / Tamu</th><th>Metode</th><th>Sumber</th><th>Lunas Pada</th><th class="text-end">Jumlah</th></tr></thead><tbody>
    @forelse ($rows as $payment)
        <tr><td class="fw-semibold">{{ $payment->payment_code }}</td><td>{{ $payment->reservation?->booking_code ?? '-' }}<small class="d-block text-muted">{{ $payment->reservation?->guest_name ?? '-' }}</small></td><td>{{ $payment->method?->name ?? '-' }}</td><td>{{ str($payment->source)->replace('_', ' ')->title() }}</td><td>{{ $payment->paid_at?->translatedFormat('d M Y H:i') ?? '-' }}</td><td class="text-end fw-semibold">Rp{{ number_format((float) $payment->amount, 0, ',', '.') }}</td></tr>
    @empty
        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada pendapatan pada periode ini.</td></tr>
    @endforelse
</tbody></table></div><div class="no-print">{{ $rows->links() }}</div></div></div>
