@extends('layouts.main')

@section('title', 'Dashboard Owner')

@push('styles')
    <style>
        .owner-dashboard { --owner-primary: #5d87ff; --owner-dark: #17223b; --owner-muted: #6c788e; }
        .owner-hero { position: relative; overflow: hidden; border: 0; border-radius: 24px; background: linear-gradient(120deg, #17223b 0%, #263d71 52%, #426ed6 100%); box-shadow: 0 22px 50px rgba(32, 59, 116, .2); color: #fff; }
        .owner-hero::before { position: absolute; width: 310px; height: 310px; right: -90px; top: -190px; border: 1px solid rgba(255,255,255,.13); border-radius: 50%; content: ''; }
        .owner-hero::after { position: absolute; width: 190px; height: 190px; right: 120px; bottom: -145px; border: 35px solid rgba(255,255,255,.045); border-radius: 50%; content: ''; }
        .owner-hero-content { position: relative; z-index: 2; }
        .owner-hero-label { display: inline-flex; align-items: center; gap: 8px; padding: 7px 12px; border: 1px solid rgba(255,255,255,.18); border-radius: 999px; background: rgba(255,255,255,.08); font-size: 12px; font-weight: 700; letter-spacing: .04em; }
        .hero-stat { min-width: 205px; padding: 15px 18px; border: 1px solid rgba(255,255,255,.14); border-radius: 16px; background: rgba(255,255,255,.08); backdrop-filter: blur(8px); }
        .hero-button { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 42px; padding: 9px 15px; border: 1px solid rgba(255,255,255,.18); border-radius: 12px; background: rgba(255,255,255,.09); color: #fff; font-weight: 600; transition: .2s ease; }
        .hero-button:hover { transform: translateY(-2px); background: #fff; color: #315fc9; }
        .hero-button-primary { background: #fff; color: #315fc9; }
        .period-toolbar { border: 1px solid #ebeff6; border-radius: 18px; box-shadow: 0 8px 28px rgba(17,38,85,.045); }
        .period-pills { display: flex; flex-wrap: wrap; gap: 7px; }
        .period-pill { padding: 8px 13px; border: 1px solid #e7ecf4; border-radius: 10px; background: #fff; color: #637087; font-size: 13px; font-weight: 600; transition: .2s ease; }
        .period-pill:hover, .period-pill.active { border-color: #5d87ff; background: #edf3ff; color: #426fdc; }
        .owner-kpi { position: relative; display: block; height: calc(100% - 24px); margin-bottom: 24px; overflow: hidden; border: 1px solid #edf1f7; border-radius: 18px; background: #fff; color: inherit; box-shadow: 0 8px 28px rgba(17,38,85,.045); transition: .2s ease; }
        .owner-kpi:hover { transform: translateY(-4px); box-shadow: 0 17px 38px rgba(17,38,85,.1); }
        .owner-kpi::after { position: absolute; width: 90px; height: 90px; right: -38px; top: -38px; border-radius: 50%; background: color-mix(in srgb, var(--kpi-color) 9%, white); content: ''; }
        .kpi-icon { display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; border-radius: 15px; background: color-mix(in srgb, var(--kpi-color) 12%, white); color: var(--kpi-color); }
        .trend-chip { display: inline-flex; align-items: center; gap: 3px; padding: 4px 8px; border-radius: 999px; font-size: 11px; font-weight: 700; }
        .trend-up { background: #e8f7f3; color: #07836a; }
        .trend-down { background: #feeee9; color: #d0583f; }
        .trend-flat { background: #eef2f7; color: #66758b; }
        .mini-stat { display: flex; align-items: center; gap: 13px; height: 100%; padding: 17px; border: 1px solid #edf1f7; border-radius: 15px; background: #fff; }
        .mini-stat-icon { display: inline-flex; flex: 0 0 auto; align-items: center; justify-content: center; width: 42px; height: 42px; border-radius: 13px; background: #f1f5ff; color: #4e78df; }
        .owner-card { overflow: hidden; border: 1px solid #edf1f7; border-radius: 18px; box-shadow: 0 8px 28px rgba(17,38,85,.045); }
        .owner-card .card-header { border-bottom: 1px solid #edf1f7; background: #fff; padding: 20px 22px; }
        .owner-eyebrow { color: #5d87ff; font-size: 11px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; }
        .chart-toggle { display: flex; gap: 5px; padding: 4px; border-radius: 11px; background: #f3f6fa; }
        .chart-toggle button { padding: 7px 11px; border: 0; border-radius: 8px; background: transparent; color: #6a778d; font-size: 12px; font-weight: 600; }
        .chart-toggle button.active { background: #fff; color: #4d76dc; box-shadow: 0 4px 12px rgba(23,48,98,.09); }
        .occupancy-gauge { display: grid; place-items: center; width: 170px; height: 170px; margin: 6px auto 24px; border-radius: 50%; background: conic-gradient(#13deb9 calc(var(--occupancy) * 1%), #edf2f9 0); box-shadow: 0 12px 30px rgba(19,222,185,.14); }
        .occupancy-gauge-inner { display: grid; place-items: center; width: 130px; height: 130px; border-radius: 50%; background: #fff; text-align: center; }
        .source-row { padding: 11px 0; border-bottom: 1px dashed #e7ecf3; }
        .source-row:last-child { border-bottom: 0; }
        .source-bar { height: 7px; overflow: hidden; border-radius: 999px; background: #edf2f7; }
        .source-bar span { display: block; height: 100%; border-radius: inherit; background: linear-gradient(90deg, #5d87ff, #49beff); }
        .payment-method-row { display: grid; grid-template-columns: 40px minmax(0,1fr) auto; align-items: center; gap: 12px; padding: 13px 0; border-bottom: 1px solid #edf1f7; }
        .payment-method-row:last-child { border-bottom: 0; }
        .method-icon { display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 12px; background: #edf3ff; color: #4f79df; }
        .service-chip { display: flex; align-items: center; justify-content: space-between; gap: 15px; padding: 12px 0; border-bottom: 1px solid #edf1f7; }
        .service-chip:last-child { border-bottom: 0; }
        .payment-table tbody tr { transition: background .2s ease; }
        .payment-table tbody tr:hover { background: #f8faff; }
        .report-link { display: flex; align-items: center; gap: 12px; height: 100%; padding: 15px; border: 1px solid #edf1f7; border-radius: 14px; color: #17223b; transition: .2s ease; }
        .report-link:hover { transform: translateY(-2px); border-color: #cfdbfb; background: #f8faff; color: #426fdc; }
        .report-link-icon { display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px; border-radius: 13px; background: #edf3ff; color: #4d77df; }
        .owner-empty { padding: 32px 15px; text-align: center; color: #7c889c; }
        .owner-empty i { display: block; margin-bottom: 8px; color: #b3bed0; font-size: 34px; }
        @media (max-width: 767.98px) {
            .owner-hero .card-body { padding: 25px !important; }
            .hero-stat { width: 100%; }
            .hero-actions { width: 100%; }
            .hero-button { flex: 1 1 auto; }
            .owner-card .card-header { padding: 18px; }
        }
    </style>
@endpush

@section('content')
    @php
        $percentageChange = function (float|int $current, float|int $previous): float {
            if ((float) $previous === 0.0) return (float) $current > 0 ? 100 : 0;
            return round((($current - $previous) / abs($previous)) * 100, 1);
        };
        $kpis = [
            ['Pendapatan Kas', 'Rp'.number_format($metrics['revenue'], 0, ',', '.'), $percentageChange($metrics['revenue'], $previousMetrics['revenue']), 'ti-wallet', '#13deb9', route('owner.reports.revenue', request()->only('period', 'start_date', 'end_date'))],
            ['Reservasi Dibuat', number_format($metrics['reservations']), $percentageChange($metrics['reservations'], $previousMetrics['reservations']), 'ti-calendar-stats', '#5d87ff', route('owner.reports.reservations', request()->only('period', 'start_date', 'end_date'))],
            ['Okupansi Aktual', $metrics['occupancy_rate'].'%', round($metrics['occupancy_rate'] - $previousMetrics['occupancy_rate'], 1), 'ti-chart-donut-4', '#49beff', route('owner.reports.occupancy', request()->only('period', 'start_date', 'end_date'))],
            ['Nilai Reservasi', 'Rp'.number_format($metrics['booking_value'], 0, ',', '.'), $percentageChange($metrics['booking_value'], $previousMetrics['booking_value']), 'ti-receipt-2', '#ffae1f', route('owner.reports.reservations', request()->only('period', 'start_date', 'end_date'))],
        ];
        $totalReservationSources = max(1, (int) $reservationSources->sum());
        $totalPaymentMethodAmount = max(1, (float) $paymentMethodSummary->sum('total_amount'));
        $totalServiceAmount = (float) $serviceSummary->sum('amount');
    @endphp

    <div class="owner-dashboard">
        <div class="card owner-hero mb-4">
            <div class="card-body p-4 p-lg-5 owner-hero-content">
                <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between gap-4">
                    <div>
                        <span class="owner-hero-label mb-3"><i class="ti ti-building-resort"></i>OWNER COMMAND CENTER</span>
                        <h2 class="text-white fw-bolder mb-2">Business Overview</h2>
                        <p class="text-white opacity-75 mb-0">Pantau kesehatan bisnis Candra Resort dan ambil keputusan dari data transaksi aktual.</p>
                    </div>
                    <div class="d-flex flex-column align-items-xl-end gap-3">
                        <div class="hero-stat">
                            <small class="d-block text-white opacity-75 mb-1">Periode aktif</small>
                            <strong class="text-white">{{ $period->label() }}</strong>
                            <small class="d-block text-white opacity-50 mt-1">Dibandingkan {{ $previousPeriod->label() }}</small>
                        </div>
                        <div class="d-flex flex-wrap gap-2 hero-actions">
                            <a href="{{ route('owner.receptionists.index') }}" class="hero-button"><i class="ti ti-users"></i>Kelola Receptionist</a>
                            <a href="{{ route('owner.reports.monthly', ['period' => 'this_year']) }}" class="hero-button hero-button-primary"><i class="ti ti-report-analytics"></i>Laporan Bulanan</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card period-toolbar mb-4">
            <div class="card-body p-3 p-lg-4">
                <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between gap-3">
                    <div>
                        <span class="owner-eyebrow">Filter data</span>
                        <h6 class="fw-semibold mb-0 mt-1">Pilih Periode Analisis</h6>
                    </div>
                    <div class="period-pills">
                        @foreach (collect($periodOptions)->except('custom') as $value => $label)
                            <a href="{{ route('owner.dashboard', ['period' => $value]) }}" class="period-pill {{ $period->preset === $value ? 'active' : '' }}">{{ $label }}</a>
                        @endforeach
                        <button class="period-pill {{ $period->preset === 'custom' ? 'active' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#owner-custom-period"><i class="ti ti-calendar-search me-1"></i>Kustom</button>
                    </div>
                </div>
                <div class="collapse {{ $period->preset === 'custom' ? 'show' : '' }}" id="owner-custom-period">
                    <form action="{{ route('owner.dashboard') }}" method="GET" class="row g-3 align-items-end pt-3 mt-3 border-top">
                        <input type="hidden" name="period" value="custom">
                        <div class="col-md-4"><label class="form-label">Tanggal Mulai</label><input type="date" name="start_date" class="form-control" value="{{ old('start_date', $period->start->format('Y-m-d')) }}" required></div>
                        <div class="col-md-4"><label class="form-label">Tanggal Akhir</label><input type="date" name="end_date" class="form-control" value="{{ old('end_date', $period->end->format('Y-m-d')) }}" required></div>
                        <div class="col-md-4"><button class="btn btn-primary w-100"><i class="ti ti-filter me-1"></i>Terapkan Rentang Tanggal</button></div>
                    </form>
                </div>
                @error('end_date')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="row">
            @foreach ($kpis as [$label, $value, $change, $icon, $color, $url])
                @php $trendClass = $change > 0 ? 'trend-up' : ($change < 0 ? 'trend-down' : 'trend-flat'); @endphp
                <div class="col-xl-3 col-md-6">
                    <a href="{{ $url }}" class="owner-kpi" style="--kpi-color:{{ $color }}">
                        <div class="card-body p-4 position-relative" style="z-index:1">
                            <div class="d-flex align-items-start justify-content-between mb-4"><span class="kpi-icon"><i class="ti {{ $icon }} fs-6"></i></span><span class="trend-chip {{ $trendClass }}"><i class="ti {{ $change > 0 ? 'ti-trending-up' : ($change < 0 ? 'ti-trending-down' : 'ti-minus') }}"></i>{{ $change > 0 ? '+' : '' }}{{ $change }}{{ $label === 'Okupansi Aktual' ? ' pt' : '%' }}</span></div>
                            <span class="text-muted d-block mb-1">{{ $label }}</span>
                            <h4 class="fw-bolder mb-2 text-truncate">{{ $value }}</h4>
                            <small class="text-muted">dibanding periode sebelumnya</small>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="row g-3 mb-4">
            @foreach ([
                ['Room-night Terisi', number_format($metrics['occupied_room_nights']), 'ti-bed'],
                ['Rata-rata Menginap', $metrics['average_stay'].' malam', 'ti-moon'],
                ['Pembatalan', number_format($metrics['cancelled']), 'ti-calendar-off'],
                ['Kamar Aktif', number_format($metrics['active_rooms']), 'ti-door'],
            ] as [$label, $value, $icon])
                <div class="col-xl-3 col-md-6"><div class="mini-stat"><span class="mini-stat-icon"><i class="ti {{ $icon }} fs-6"></i></span><div><small class="text-muted d-block">{{ $label }}</small><h5 class="fw-semibold mb-0">{{ $value }}</h5></div></div></div>
            @endforeach
        </div>

        <div class="row">
            <div class="col-xl-8">
                <div class="card owner-card h-100">
                    <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div><span class="owner-eyebrow">Analisis periode</span><h5 class="fw-semibold mb-1 mt-1">Tren Bisnis</h5><small class="text-muted">Pendapatan lunas dan reservasi yang dibuat.</small></div>
                        <div class="chart-toggle" id="owner-chart-toggle"><button type="button" data-view="revenue">Pendapatan</button><button type="button" data-view="reservations">Reservasi</button><button type="button" data-view="combined" class="active">Gabungan</button></div>
                    </div>
                    <div class="card-body p-4"><div id="owner-business-chart" style="min-height:350px"></div></div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card owner-card h-100">
                    <div class="card-header"><span class="owner-eyebrow">Efisiensi aset</span><h5 class="fw-semibold mb-1 mt-1">Kapasitas & Sumber Tamu</h5><small class="text-muted">Berdasarkan stay yang benar-benar aktif.</small></div>
                    <div class="card-body p-4">
                        <div class="occupancy-gauge" style="--occupancy:{{ min(100, $metrics['occupancy_rate']) }}"><div class="occupancy-gauge-inner"><div><h2 class="fw-bolder mb-0">{{ $metrics['occupancy_rate'] }}%</h2><small class="text-muted">Okupansi</small></div></div></div>
                        <div class="d-flex justify-content-between mb-3"><span class="text-muted">Terisi</span><strong>{{ number_format($metrics['occupied_room_nights']) }} room-night</strong></div>
                        <div class="d-flex justify-content-between mb-4"><span class="text-muted">Tersedia</span><strong>{{ number_format($metrics['available_room_nights']) }} room-night</strong></div>
                        @foreach ([['Online', (int) $reservationSources->get('online', 0)], ['Walk-in', (int) $reservationSources->get('walk_in', 0)]] as [$source, $count])
                            @php $sourcePercent = round(($count / $totalReservationSources) * 100); @endphp
                            <div class="source-row"><div class="d-flex justify-content-between mb-2"><span>{{ $source }}</span><strong>{{ $count }} <small class="text-muted fw-normal">({{ $sourcePercent }}%)</small></strong></div><div class="source-bar"><span style="width:{{ $sourcePercent }}%"></span></div></div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-xl-6">
                <div class="card owner-card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center"><div><span class="owner-eyebrow">Arus pembayaran</span><h5 class="fw-semibold mb-0 mt-1">Metode Pembayaran Teratas</h5></div><a href="{{ route('owner.reports.payments', request()->only('period', 'start_date', 'end_date')) }}" class="btn btn-sm btn-light-primary text-primary">Detail</a></div>
                    <div class="card-body px-4 py-2">
                        @forelse ($paymentMethodSummary as $method)
                            @php $methodPercent = round(((float) $method->total_amount / $totalPaymentMethodAmount) * 100); @endphp
                            <div class="payment-method-row"><span class="method-icon"><i class="ti ti-credit-card"></i></span><div class="min-w-0"><div class="d-flex justify-content-between gap-2 mb-1"><strong class="text-truncate">{{ $method->method?->name ?? 'Tidak diketahui' }}</strong><small class="text-muted">{{ $method->transaction_count }} transaksi</small></div><div class="source-bar"><span style="width:{{ $methodPercent }}%"></span></div></div><strong>Rp{{ number_format((float) $method->total_amount, 0, ',', '.') }}</strong></div>
                        @empty
                            <div class="owner-empty"><i class="ti ti-credit-card-off"></i><strong>Belum ada pembayaran lunas</strong></div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card owner-card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center"><div><span class="owner-eyebrow">Pendapatan tambahan</span><h5 class="fw-semibold mb-0 mt-1">Kontribusi Layanan</h5></div><a href="{{ route('owner.reports.services', request()->only('period', 'start_date', 'end_date')) }}" class="btn btn-sm btn-light-primary text-primary">Detail</a></div>
                    <div class="card-body px-4 py-2">
                        @forelse ($serviceSummary->sortByDesc('amount')->take(5) as $service)
                            <div class="service-chip"><div><strong>{{ $service['label'] }}</strong><small class="d-block text-muted">{{ $service['count'] }} pesanan selesai</small></div><strong>Rp{{ number_format($service['amount'], 0, ',', '.') }}</strong></div>
                        @empty
                            <div class="owner-empty"><i class="ti ti-bell-off"></i><strong>Belum ada layanan selesai</strong></div>
                        @endforelse
                        <div class="d-flex justify-content-between align-items-center py-3 mt-1 border-top"><span class="text-muted">Total kontribusi layanan</span><h5 class="fw-bolder mb-0 text-primary">Rp{{ number_format($totalServiceAmount, 0, ',', '.') }}</h5></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card owner-card mt-4">
            <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2"><div><span class="owner-eyebrow">Transaksi terbaru</span><h5 class="fw-semibold mb-0 mt-1">Pembayaran Berhasil</h5></div><a href="{{ route('owner.reports.revenue') }}" class="btn btn-sm btn-light-primary text-primary">Lihat Semua</a></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 payment-table">
                        <thead><tr><th class="ps-4">Transaksi</th><th>Tamu / Reservasi</th><th>Metode</th><th>Waktu</th><th class="text-end pe-4">Jumlah</th></tr></thead>
                        <tbody>
                            @forelse ($recentPayments as $payment)
                                <tr><td class="ps-4"><div class="d-flex align-items-center gap-2"><span class="method-icon"><i class="ti ti-check"></i></span><strong>{{ $payment->payment_code }}</strong></div></td><td>{{ $payment->reservation?->guest_name ?? '-' }}<small class="d-block text-muted">{{ $payment->reservation?->booking_code ?? '-' }}</small></td><td><span class="badge bg-light-primary text-primary">{{ $payment->method?->name ?? 'Midtrans' }}</span></td><td>{{ $payment->paid_at?->translatedFormat('d M Y H:i') ?? '-' }}</td><td class="text-end pe-4 fw-bolder text-success">Rp{{ number_format((float) $payment->amount, 0, ',', '.') }}</td></tr>
                            @empty
                                <tr><td colspan="5"><div class="owner-empty"><i class="ti ti-receipt-off"></i><strong>Belum ada pembayaran berhasil</strong></div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card owner-card mt-4">
            <div class="card-header"><span class="owner-eyebrow">Akses cepat</span><h5 class="fw-semibold mb-0 mt-1">Pusat Laporan</h5></div>
            <div class="card-body p-4">
                <div class="row g-3">
                    @foreach ([
                        ['Reservasi', 'Performa pemesanan', 'ti-calendar-stats', 'owner.reports.reservations'], ['Okupansi', 'Pemakaian kamar', 'ti-chart-pie', 'owner.reports.occupancy'],
                        ['Pendapatan', 'Arus kas masuk', 'ti-chart-line', 'owner.reports.revenue'], ['Layanan', 'F&B dan layanan hotel', 'ti-report-analytics', 'owner.reports.services'],
                    ] as [$title, $description, $icon, $routeName])
                        <div class="col-xl-3 col-md-6"><a href="{{ route($routeName) }}" class="report-link"><span class="report-link-icon"><i class="ti {{ $icon }} fs-6"></i></span><span><strong class="d-block">{{ $title }}</strong><small class="text-muted">{{ $description }}</small></span><i class="ti ti-arrow-up-right ms-auto text-muted"></i></a></div>
                    @endforeach
                </div>
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

            const allSeries = [
                { name: 'Pendapatan', type: 'area', data: @json($trend['revenue']) },
                { name: 'Reservasi', type: 'line', data: @json($trend['reservations']) }
            ];
            const revenueAxis = { labels: { formatter: value => 'Rp' + new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(value) } };
            const reservationAxis = { opposite: true, decimalsInFloat: 0, labels: { formatter: value => Math.round(value) } };
            const chart = new ApexCharts(chartTarget, {
                series: allSeries,
                chart: { height: 350, type: 'line', toolbar: { show: false }, fontFamily: 'inherit', animations: { enabled: true, speed: 500 } },
                colors: ['#5D87FF', '#13DEB9'], stroke: { curve: 'smooth', width: [3, 3] },
                fill: { type: 'gradient', gradient: { opacityFrom: .28, opacityTo: .03 } }, dataLabels: { enabled: false },
                xaxis: { categories: @json($trend['labels']), labels: { style: { colors: '#7c889c' } } },
                yaxis: [revenueAxis, reservationAxis],
                tooltip: { shared: true, y: [{ formatter: value => 'Rp' + new Intl.NumberFormat('id-ID').format(value) }, { formatter: value => Math.round(value) + ' reservasi' }] },
                grid: { borderColor: '#edf2f9', strokeDashArray: 4 }, legend: { position: 'top', horizontalAlign: 'right' }
            });
            chart.render();

            document.querySelectorAll('#owner-chart-toggle button').forEach(button => button.addEventListener('click', function () {
                document.querySelectorAll('#owner-chart-toggle button').forEach(item => item.classList.remove('active'));
                this.classList.add('active');
                const view = this.dataset.view;
                chart.updateOptions({
                    series: view === 'revenue' ? [allSeries[0]] : (view === 'reservations' ? [allSeries[1]] : allSeries),
                    yaxis: view === 'revenue' ? [revenueAxis] : (view === 'reservations' ? [{ ...reservationAxis, opposite: false }] : [revenueAxis, reservationAxis])
                }, false, true);
            }));
        });
    </script>
@endpush
