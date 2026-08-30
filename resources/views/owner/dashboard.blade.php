@extends('layouts.main')

@section('title', 'Dashboard Owner')

@section('content')
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-semibold mb-1">Business Overview</h4>
            <p class="text-muted mb-0">Ringkasan performa Candra Resort untuk {{ $period->label() }}.</p>
        </div>
        <a href="{{ route('owner.reports.monthly', ['period' => 'this_year']) }}" class="btn btn-primary">
            <i class="ti ti-report-analytics me-1"></i>Lihat Laporan
        </a>
    </div>

    @include('owner.reports.partials.filter', ['filterRoute' => route('owner.dashboard'), 'showActions' => false])

    @php
        $cards = [
            ['Reservasi Dibuat', number_format($metrics['reservations']), 'ti-calendar-stats', 'primary'],
            ['Pendapatan Kas', 'Rp'.number_format($metrics['revenue'], 0, ',', '.'), 'ti-cash', 'success'],
            ['Okupansi Aktual', $metrics['occupancy_rate'].'%', 'ti-chart-pie', 'secondary'],
            ['Room-night Terisi', number_format($metrics['occupied_room_nights']), 'ti-bed', 'warning'],
            ['Nilai Reservasi', 'Rp'.number_format($metrics['booking_value'], 0, ',', '.'), 'ti-receipt', 'info'],
            ['Rata-rata Menginap', $metrics['average_stay'].' malam', 'ti-moon', 'primary'],
            ['Pembatalan', number_format($metrics['cancelled']), 'ti-calendar-x', 'danger'],
            ['Kamar Aktif', number_format($metrics['active_rooms']), 'ti-door', 'success'],
        ];
    @endphp

    <div class="row">
        @foreach ($cards as [$label, $value, $icon, $color])
            <div class="col-xl-3 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <span class="round-48 rounded bg-light-{{ $color }} d-flex align-items-center justify-content-center flex-shrink-0">
                                <i class="ti {{ $icon }} fs-6 text-{{ $color }}"></i>
                            </span>
                            <div class="ms-3 min-w-0">
                                <span class="text-muted d-block">{{ $label }}</span>
                                <h4 class="fw-semibold mb-0 text-truncate">{{ $value }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-1">Tren Bisnis</h5>
                    <p class="text-muted mb-3">Pendapatan lunas dan reservasi yang dibuat.</p>
                    <div id="owner-business-chart" style="min-height: 330px"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-1">Kapasitas Periode</h5>
                    <p class="text-muted mb-4">Dihitung sampai hari ini untuk periode berjalan.</p>
                    <div class="d-flex justify-content-between mb-2"><span>Room-night terisi</span><strong>{{ number_format($metrics['occupied_room_nights']) }}</strong></div>
                    <div class="d-flex justify-content-between mb-2"><span>Room-night tersedia</span><strong>{{ number_format($metrics['available_room_nights']) }}</strong></div>
                    <div class="progress mt-4" style="height: 10px">
                        <div class="progress-bar" role="progressbar" style="width: {{ min(100, $metrics['occupancy_rate']) }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-2"><small class="text-muted">Tingkat okupansi</small><span class="fw-semibold text-primary">{{ $metrics['occupancy_rate'] }}%</span></div>
                    <hr class="my-4">
                    <small class="text-muted">Okupansi memakai data stay/check-in aktual. Kamar yang baru dipesan tetapi belum check-in tidak dihitung sebagai kamar terisi.</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h5 class="card-title fw-semibold mb-0">Pembayaran Terbaru</h5>
                <a href="{{ route('owner.reports.payments') }}" class="btn btn-sm btn-light-primary text-primary">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Kode</th><th>Reservasi</th><th>Metode</th><th>Waktu</th><th class="text-end">Jumlah</th></tr></thead>
                    <tbody>
                        @forelse ($recentPayments as $payment)
                            <tr>
                                <td class="fw-semibold">{{ $payment->payment_code }}</td>
                                <td>{{ $payment->reservation?->booking_code ?? '-' }}</td>
                                <td>{{ $payment->method?->name ?? '-' }}</td>
                                <td>{{ $payment->paid_at?->translatedFormat('d M Y H:i') ?? '-' }}</td>
                                <td class="text-end fw-semibold">Rp{{ number_format((float) $payment->amount, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada pembayaran berhasil.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('dashboard/assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartTarget = document.querySelector('#owner-business-chart');
            if (!chartTarget || typeof ApexCharts === 'undefined') return;

            new ApexCharts(chartTarget, {
                series: [
                    { name: 'Pendapatan', type: 'area', data: @json($trend['revenue']) },
                    { name: 'Reservasi', type: 'line', data: @json($trend['reservations']) }
                ],
                chart: { height: 330, type: 'line', toolbar: { show: false }, fontFamily: 'inherit' },
                colors: ['#5D87FF', '#49BEFF'],
                stroke: { curve: 'smooth', width: [3, 3] },
                fill: { type: 'gradient', gradient: { opacityFrom: 0.25, opacityTo: 0.03 } },
                dataLabels: { enabled: false },
                xaxis: { categories: @json($trend['labels']) },
                yaxis: [
                    { labels: { formatter: value => 'Rp' + new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(value) } },
                    { opposite: true, decimalsInFloat: 0 }
                ],
                tooltip: { shared: true, y: [
                    { formatter: value => 'Rp' + new Intl.NumberFormat('id-ID').format(value) },
                    { formatter: value => Math.round(value) + ' reservasi' }
                ] },
                grid: { borderColor: '#edf2f9' },
                legend: { position: 'top', horizontalAlign: 'right' }
            }).render();
        });
    </script>
@endpush
