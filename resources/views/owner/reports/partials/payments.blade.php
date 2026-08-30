@php
    $statusLabels = ['pending' => 'Menunggu', 'paid' => 'Lunas', 'failed' => 'Gagal', 'refunded' => 'Refund', 'cancelled' => 'Dibatalkan', 'expired' => 'Kedaluwarsa'];
    $statusColors = ['pending' => 'warning', 'paid' => 'success', 'failed' => 'danger', 'refunded' => 'info', 'cancelled' => 'danger', 'expired' => 'secondary'];
    $chartConfig = [
        'series' => $methodSummary->values(), 'labels' => $methodSummary->keys(),
        'chart' => ['type' => 'donut', 'height' => 320, 'fontFamily' => 'inherit'],
        'colors' => ['#5D87FF', '#13DEB9', '#FFAE1F', '#49BEFF', '#FA896B', '#A2A5B9'],
        'legend' => ['position' => 'bottom'], 'dataLabels' => ['enabled' => false],
        'noData' => ['text' => 'Belum ada pembayaran lunas'],
    ];
@endphp
<div class="row">
    @foreach ([
        ['Total Transaksi', number_format($metrics['total']), 'ti-receipt', 'primary'],
        ['Transaksi Lunas', number_format($metrics['paid']), 'ti-circle-check', 'success'],
        ['Masih Menunggu', number_format($metrics['pending']), 'ti-clock', 'warning'],
        ['Nominal Lunas', 'Rp'.number_format($metrics['paid_amount'], 0, ',', '.'), 'ti-cash', 'info'],
    ] as [$label, $value, $icon, $color])
        <div class="col-xl-3 col-md-6"><div class="card"><div class="card-body d-flex align-items-center"><span class="round-48 rounded bg-light-{{ $color }} d-flex align-items-center justify-content-center"><i class="ti {{ $icon }} fs-6 text-{{ $color }}"></i></span><div class="ms-3 min-w-0"><span class="text-muted">{{ $label }}</span><h4 class="mb-0 fw-semibold text-truncate">{{ $value }}</h4></div></div></div></div>
    @endforeach
</div>
<div class="row">
    <div class="col-xl-7"><div class="card"><div class="card-body"><h5 class="fw-semibold mb-1">Distribusi Metode Pembayaran</h5><p class="text-muted">Nominal pembayaran lunas per metode.</p><div id="owner-report-chart" data-chart="{{ json_encode($chartConfig) }}"></div></div></div></div>
    <div class="col-xl-5"><div class="card"><div class="card-body"><h5 class="fw-semibold mb-3">Status Transaksi</h5>
        @forelse ($statusSummary as $status => $count)
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom"><span>{{ $statusLabels[$status] ?? str($status)->title() }}</span><span class="badge bg-light-{{ $statusColors[$status] ?? 'secondary' }} text-{{ $statusColors[$status] ?? 'secondary' }}">{{ $count }}</span></div>
        @empty
            <p class="text-muted">Belum ada transaksi pada periode ini.</p>
        @endforelse
    </div></div></div>
</div>
<div class="card"><div class="card-body"><h5 class="fw-semibold mb-3">Riwayat Pembayaran</h5><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Kode</th><th>Reservasi / Tamu</th><th>Metode</th><th>Dibuat</th><th>Status</th><th class="text-end">Jumlah</th></tr></thead><tbody>
    @forelse ($rows as $payment)
        <tr><td class="fw-semibold">{{ $payment->payment_code }}</td><td>{{ $payment->reservation?->booking_code ?? '-' }}<small class="d-block text-muted">{{ $payment->reservation?->guest_name ?? '-' }}</small></td><td>{{ $payment->method?->name ?? '-' }}<small class="d-block text-muted">{{ str($payment->source)->replace('_', ' ')->title() }}</small></td><td>{{ $payment->created_at->translatedFormat('d M Y H:i') }}</td><td><span class="badge bg-light-{{ $statusColors[$payment->status->value] ?? 'secondary' }} text-{{ $statusColors[$payment->status->value] ?? 'secondary' }}">{{ $statusLabels[$payment->status->value] ?? $payment->status->value }}</span></td><td class="text-end fw-semibold">Rp{{ number_format((float) $payment->amount, 0, ',', '.') }}</td></tr>
    @empty
        <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada transaksi pada periode ini.</td></tr>
    @endforelse
</tbody></table></div><div class="no-print">{{ $rows->links() }}</div></div></div>
