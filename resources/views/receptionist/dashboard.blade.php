@extends('layouts.main')

@section('title', 'Dashboard Receptionist')

@push('styles')
    <style>
        .reception-dashboard { --dashboard-primary: #5d87ff; --dashboard-ink: #17233c; --dashboard-muted: #6b778c; }
        .operations-hero { position: relative; overflow: hidden; border: 0; border-radius: 22px; background: linear-gradient(125deg, #365fcf 0%, #5d87ff 54%, #77a1ff 100%); box-shadow: 0 18px 45px rgba(93, 135, 255, .22); color: #fff; }
        .operations-hero::before, .operations-hero::after { position: absolute; border: 1px solid rgba(255, 255, 255, .16); border-radius: 50%; content: ''; }
        .operations-hero::before { width: 290px; height: 290px; right: -85px; top: -170px; }
        .operations-hero::after { width: 190px; height: 190px; right: 105px; bottom: -135px; }
        .hero-content { position: relative; z-index: 2; }
        .hero-date-pill { display: inline-flex; align-items: center; gap: 8px; padding: 7px 12px; border: 1px solid rgba(255, 255, 255, .22); border-radius: 999px; background: rgba(255, 255, 255, .12); font-size: 12px; font-weight: 600; backdrop-filter: blur(8px); }
        .hero-clock { min-width: 150px; padding: 12px 16px; border: 1px solid rgba(255, 255, 255, .18); border-radius: 16px; background: rgba(20, 43, 104, .2); text-align: center; backdrop-filter: blur(8px); }
        .hero-clock strong { font-size: 25px; letter-spacing: 1px; }
        .hero-action { display: inline-flex; align-items: center; gap: 8px; min-height: 42px; padding: 9px 15px; border: 1px solid rgba(255, 255, 255, .22); border-radius: 12px; background: rgba(255, 255, 255, .13); color: #fff; font-weight: 600; transition: .2s ease; }
        .hero-action:hover { transform: translateY(-2px); background: #fff; color: #416cd9; }
        .hero-action-primary { background: #fff; color: #416cd9; }
        .operation-metric { position: relative; display: block; height: calc(100% - 24px); margin-bottom: 24px; overflow: hidden; border: 1px solid #edf1f7; border-radius: 18px; background: #fff; color: inherit; box-shadow: 0 8px 24px rgba(17, 38, 85, .045); transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease; }
        .operation-metric:hover { transform: translateY(-4px); border-color: color-mix(in srgb, var(--metric-color) 32%, #edf1f7); box-shadow: 0 16px 32px rgba(17, 38, 85, .1); }
        .metric-line { position: absolute; inset: 0 auto 0 0; width: 4px; background: var(--metric-color); }
        .metric-icon { display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; border-radius: 15px; background: color-mix(in srgb, var(--metric-color) 12%, white); color: var(--metric-color); }
        .metric-arrow { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 50%; background: #f5f7fb; color: #7c889c; transition: .2s ease; }
        .operation-metric:hover .metric-arrow { background: var(--metric-color); color: #fff; }
        .dashboard-card { overflow: hidden; border: 1px solid #edf1f7; border-radius: 18px; box-shadow: 0 8px 28px rgba(17, 38, 85, .045); }
        .dashboard-card .card-header { border-bottom: 1px solid #edf1f7; background: #fff; padding: 20px 22px; }
        .section-eyebrow { color: var(--dashboard-primary); font-size: 11px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; }
        .occupancy-ring { display: grid; place-items: center; width: 164px; height: 164px; margin: 4px auto 24px; border-radius: 50%; background: conic-gradient(var(--dashboard-primary) calc(var(--occupancy) * 1%), #edf2f9 0); box-shadow: 0 12px 30px rgba(93, 135, 255, .15); }
        .occupancy-ring-inner { display: grid; place-items: center; width: 126px; height: 126px; border-radius: 50%; background: #fff; text-align: center; }
        .room-status-row { display: grid; grid-template-columns: 12px 1fr auto; align-items: center; gap: 10px; padding: 9px 0; border-bottom: 1px dashed #e9edf4; }
        .room-status-row:last-child { border-bottom: 0; }
        .status-dot { width: 9px; height: 9px; border-radius: 50%; }
        .modern-tabs { gap: 6px; padding: 4px; border: 0; border-radius: 12px; background: #f4f7fb; }
        .modern-tabs .nav-link { border: 0; border-radius: 9px; color: #66738a; font-size: 13px; font-weight: 600; padding: 8px 13px; }
        .modern-tabs .nav-link.active { background: #fff; color: var(--dashboard-primary); box-shadow: 0 4px 14px rgba(20, 45, 90, .08); }
        .guest-flow-item, .queue-item { display: flex; align-items: center; gap: 13px; padding: 14px 0; border-bottom: 1px solid #edf1f7; }
        .guest-flow-item:last-child, .queue-item:last-child { border-bottom: 0; }
        .guest-avatar { display: inline-flex; flex: 0 0 auto; align-items: center; justify-content: center; width: 42px; height: 42px; border-radius: 14px; background: linear-gradient(135deg, #e8efff, #f4f7ff); color: #426fdc; font-weight: 700; }
        .room-chip { display: inline-flex; align-items: center; gap: 5px; padding: 5px 9px; border-radius: 8px; background: #f3f6fa; color: #56647a; font-size: 12px; font-weight: 600; }
        .queue-priority { display: inline-flex; align-items: center; justify-content: center; min-width: 68px; padding: 5px 9px; border-radius: 999px; font-size: 11px; font-weight: 700; }
        .quick-action { display: flex; align-items: center; gap: 12px; padding: 13px; border: 1px solid #edf1f7; border-radius: 14px; color: var(--dashboard-ink); transition: .2s ease; }
        .quick-action:hover { transform: translateX(3px); border-color: #cfdcff; background: #f8faff; color: #416cd9; }
        .quick-action-icon { display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 12px; background: #edf3ff; color: #4d79e5; }
        .empty-dashboard-state { padding: 34px 15px; text-align: center; color: var(--dashboard-muted); }
        .empty-dashboard-state i { display: block; margin-bottom: 8px; color: #b2bdd0; font-size: 34px; }
        @media (max-width: 767.98px) {
            .operations-hero .card-body { padding: 24px !important; }
            .hero-clock { width: 100%; margin-top: 16px; }
            .hero-actions { width: 100%; }
            .hero-action { flex: 1 1 auto; justify-content: center; }
            .dashboard-card .card-header { padding: 18px; }
        }
    </style>
@endpush

@section('content')
    <div class="reception-dashboard">
        <div class="card operations-hero mb-4">
            <div class="card-body p-4 p-lg-5 hero-content">
                <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between gap-4">
                    <div>
                        <span class="hero-date-pill mb-3"><i class="ti ti-calendar-event"></i>{{ now()->translatedFormat('l, d F Y') }}</span>
                        <h2 class="text-white fw-bolder mb-2">Dashboard Operasional</h2>
                        <p class="mb-0 text-white opacity-75">Selamat bertugas, {{ auth()->user()->name }}. Berikut prioritas layanan Candra Resort hari ini.</p>
                    </div>
                    <div class="d-flex flex-column align-items-xl-end gap-3">
                        <div class="hero-clock"><small class="d-block text-white opacity-75 mb-1">Waktu hotel</small><strong class="d-block text-white" id="dashboard-live-clock">{{ now()->format('H:i:s') }}</strong></div>
                        <div class="d-flex flex-wrap gap-2 hero-actions">
                            <a href="{{ route('receptionist.reservations.walk-in.create') }}" class="hero-action hero-action-primary"><i class="ti ti-user-plus"></i>Reservasi Walk-in</a>
                            <a href="{{ route('receptionist.checkin.index') }}" class="hero-action"><i class="ti ti-login"></i>Proses Check-in</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $priorityCards = [
                ['Kedatangan Hari Ini', $metrics['arrivals'], 'Menunggu proses check-in', 'ti-plane-arrival', '#5d87ff', route('receptionist.checkin.index')],
                ['Keberangkatan Hari Ini', $metrics['departures'], 'Perlu penyelesaian folio', 'ti-plane-departure', '#ffae1f', route('receptionist.checkout.index')],
                ['Permintaan Aktif', $metrics['guest_requests'], 'Perlu ditindaklanjuti', 'ti-message-circle-bolt', '#13deb9', route('receptionist.guest-requests.index')],
                ['Pembayaran Pending', $metrics['pending_payments'], 'Rp'.number_format($metrics['pending_payment_amount'], 0, ',', '.').' tertunda', 'ti-credit-card', '#fa896b', route('receptionist.payments.index', ['status' => 'pending'])],
            ];
        @endphp

        <div class="row">
            @foreach ($priorityCards as [$label, $value, $helper, $icon, $color, $url])
                <div class="col-xl-3 col-md-6">
                    <a href="{{ $url }}" class="operation-metric" style="--metric-color: {{ $color }}">
                        <span class="metric-line"></span>
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start justify-content-between mb-3"><span class="metric-icon"><i class="ti {{ $icon }} fs-6"></i></span><span class="metric-arrow"><i class="ti ti-arrow-up-right"></i></span></div>
                            <div class="d-flex align-items-end justify-content-between gap-2">
                                <div><span class="text-muted d-block mb-1">{{ $label }}</span><small class="text-muted">{{ $helper }}</small></div>
                                <h2 class="fw-bolder mb-0" style="color: var(--metric-color)">{{ $value }}</h2>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="row">
            <div class="col-xl-4">
                <div class="card dashboard-card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div><span class="section-eyebrow">Kondisi real-time</span><h5 class="fw-semibold mb-0 mt-1">Status Kamar</h5></div>
                        <a href="{{ route('receptionist.rooms.index') }}" class="btn btn-sm btn-light-primary text-primary">Kelola</a>
                    </div>
                    <div class="card-body p-4">
                        <div class="occupancy-ring" style="--occupancy: {{ min(100, $metrics['occupancy_rate']) }}"><div class="occupancy-ring-inner"><div><h2 class="fw-bolder mb-0">{{ $metrics['occupancy_rate'] }}%</h2><small class="text-muted">Okupansi</small></div></div></div>
                        @foreach ([
                            ['Tersedia', $metrics['available'], '#13deb9', 'available'], ['Terisi', $metrics['occupied'], '#5d87ff', 'occupied'],
                            ['Dipesan', $metrics['reserved'], '#49beff', 'reserved'], ['Dibersihkan', $metrics['cleaning'], '#ffae1f', 'cleaning'],
                            ['Maintenance', $metrics['maintenance'], '#fa896b', 'maintenance'], ['Tidak tersedia', $metrics['unavailable'], '#7c8fac', 'unavailable'],
                        ] as [$label, $count, $color, $status])
                            <a href="{{ route('receptionist.rooms.index', ['status' => $status]) }}" class="room-status-row text-dark"><span class="status-dot" style="background: {{ $color }}"></span><span>{{ $label }}</span><strong>{{ $count }}</strong></a>
                        @endforeach
                        <div class="d-flex justify-content-between align-items-center pt-3 mt-2 border-top"><span class="text-muted">Total kamar aktif</span><strong>{{ $metrics['active_rooms'] }} kamar</strong></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-8">
                <div class="card dashboard-card h-100">
                    <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div><span class="section-eyebrow">Pergerakan tamu</span><h5 class="fw-semibold mb-0 mt-1">Agenda Hari Ini</h5></div>
                        <ul class="nav nav-tabs modern-tabs" role="tablist">
                            <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#arrival-pane" type="button" role="tab">Kedatangan <span class="badge bg-light-primary text-primary ms-1">{{ $metrics['arrivals'] }}</span></button></li>
                            <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#departure-pane" type="button" role="tab">Keberangkatan <span class="badge bg-light-warning text-warning ms-1">{{ $metrics['departures'] }}</span></button></li>
                        </ul>
                    </div>
                    <div class="card-body px-4 py-2">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="arrival-pane" role="tabpanel">
                                @forelse ($arrivals as $reservation)
                                    <div class="guest-flow-item">
                                        <span class="guest-avatar">{{ str($reservation->guest_name)->substr(0, 1)->upper() }}</span>
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-1">
                                                <div><a href="{{ route('receptionist.reservations.show', $reservation) }}" class="fw-semibold text-dark">{{ $reservation->guest_name }}</a><small class="d-block text-muted">{{ $reservation->booking_code }} · {{ $reservation->roomType?->name }}</small></div>
                                                <div class="d-flex align-items-center gap-2">
                                                    @if ($reservation->estimated_arrival_time)<span class="room-chip"><i class="ti ti-clock"></i>{{ str($reservation->estimated_arrival_time)->substr(0, 5) }}</span>@endif
                                                    <span class="room-chip"><i class="ti ti-door"></i>{{ $reservation->room?->room_number ?? 'Belum dipilih' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="{{ route('receptionist.checkin.create', $reservation) }}" class="btn btn-sm btn-light-primary text-primary" title="Proses check-in"><i class="ti ti-login"></i></a>
                                    </div>
                                @empty
                                    <div class="empty-dashboard-state"><i class="ti ti-calendar-check"></i><strong>Tidak ada kedatangan hari ini</strong><small class="d-block mt-1">Agenda check-in sudah kosong.</small></div>
                                @endforelse
                                @if ($arrivals->isNotEmpty())<div class="text-center py-3"><a href="{{ route('receptionist.checkin.index') }}" class="fw-semibold">Lihat semua antrean check-in <i class="ti ti-arrow-right ms-1"></i></a></div>@endif
                            </div>
                            <div class="tab-pane fade" id="departure-pane" role="tabpanel">
                                @forelse ($departures as $reservation)
                                    <div class="guest-flow-item">
                                        <span class="guest-avatar" style="background:#fff4e1;color:#d98700">{{ str($reservation->guest_name)->substr(0, 1)->upper() }}</span>
                                        <div class="flex-grow-1 min-w-0"><a href="{{ route('receptionist.reservations.show', $reservation) }}" class="fw-semibold text-dark">{{ $reservation->guest_name }}</a><small class="d-block text-muted">{{ $reservation->booking_code }} · {{ $reservation->roomType?->name }}</small></div>
                                        <span class="room-chip"><i class="ti ti-door"></i>Kamar {{ $reservation->room?->room_number ?? '-' }}</span>
                                        <a href="{{ route('receptionist.checkout.index') }}" class="btn btn-sm btn-light-warning text-warning" title="Buka check-out"><i class="ti ti-logout"></i></a>
                                    </div>
                                @empty
                                    <div class="empty-dashboard-state"><i class="ti ti-calendar-check"></i><strong>Tidak ada keberangkatan hari ini</strong><small class="d-block mt-1">Belum ada check-out yang perlu diproses.</small></div>
                                @endforelse
                                @if ($departures->isNotEmpty())<div class="text-center py-3"><a href="{{ route('receptionist.checkout.index') }}" class="fw-semibold">Lihat semua antrean check-out <i class="ti ti-arrow-right ms-1"></i></a></div>@endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-xl-7">
                <div class="card dashboard-card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between"><div><span class="section-eyebrow">Antrean layanan</span><h5 class="fw-semibold mb-0 mt-1">Permintaan Tamu Aktif</h5></div><a href="{{ route('receptionist.guest-requests.index') }}" class="btn btn-sm btn-light-primary text-primary">Lihat Semua</a></div>
                    <div class="card-body px-4 py-2">
                        @forelse ($recentRequests as $guestRequest)
                            @php
                                $priorityStyle = match ($guestRequest->priority) {
                                    'urgent' => ['Mendesak', '#fee9e5', '#d75237'], 'high' => ['Tinggi', '#fff2dd', '#c37800'],
                                    'normal' => ['Normal', '#e8f7f3', '#087f67'], default => ['Rendah', '#edf1f7', '#65738a'],
                                };
                            @endphp
                            <a href="{{ route('receptionist.guest-requests.show', $guestRequest) }}" class="queue-item text-dark">
                                <span class="guest-avatar"><i class="ti ti-bell-ringing"></i></span>
                                <div class="flex-grow-1 min-w-0"><div class="d-flex align-items-center gap-2 mb-1"><strong class="text-truncate">{{ $guestRequest->title }}</strong><span class="queue-priority" style="background:{{ $priorityStyle[1] }};color:{{ $priorityStyle[2] }}">{{ $priorityStyle[0] }}</span></div><small class="text-muted">{{ $guestRequest->stay?->guest_name ?? 'Tamu' }} · Kamar {{ $guestRequest->room?->room_number ?? '-' }} · {{ $guestRequest->requested_at->diffForHumans() }}</small></div>
                                <span class="badge bg-light-{{ $guestRequest->status->badgeClass() }} text-{{ $guestRequest->status->badgeClass() }}">{{ $guestRequest->status->label() }}</span><i class="ti ti-chevron-right text-muted"></i>
                            </a>
                        @empty
                            <div class="empty-dashboard-state"><i class="ti ti-bell-check"></i><strong>Semua permintaan sudah tertangani</strong><small class="d-block mt-1">Tidak ada antrean layanan aktif.</small></div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-xl-5">
                <div class="card dashboard-card h-100">
                    <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div><span class="section-eyebrow">Monitoring</span><h5 class="fw-semibold mb-0 mt-1">Antrean Lainnya</h5></div>
                        <ul class="nav nav-tabs modern-tabs" role="tablist"><li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#food-queue-pane" type="button">F&amp;B</button></li><li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#payment-queue-pane" type="button">Bayar</button></li></ul>
                    </div>
                    <div class="card-body px-4 py-2">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="food-queue-pane">
                                @forelse ($recentFoodOrders as $order)
                                    <a href="{{ route('receptionist.food-orders.show', $order) }}" class="queue-item text-dark">
                                        <span class="guest-avatar" style="background:#eaf8ff;color:#1287bd"><i class="ti ti-tools-kitchen-2"></i></span>
                                        <div class="flex-grow-1 min-w-0"><strong>{{ $order->order_code }}</strong><small class="d-block text-muted text-truncate">Kamar {{ $order->room?->room_number ?? '-' }} · {{ $order->items_count }} item · {{ $order->ordered_at->diffForHumans() }}</small></div>
                                        <span class="badge bg-light-{{ $order->status->badgeClass() }} text-{{ $order->status->badgeClass() }}">{{ $order->status->label() }}</span>
                                    </a>
                                @empty
                                    <div class="empty-dashboard-state"><i class="ti ti-tools-kitchen-2"></i><strong>Tidak ada pesanan aktif</strong></div>
                                @endforelse
                                @if ($recentFoodOrders->isNotEmpty())<div class="text-center py-3"><a href="{{ route('receptionist.food-orders.index') }}" class="fw-semibold">Buka pesanan F&amp;B <i class="ti ti-arrow-right ms-1"></i></a></div>@endif
                            </div>
                            <div class="tab-pane fade" id="payment-queue-pane">
                                @forelse ($pendingPayments as $payment)
                                    <a href="{{ route('receptionist.payments.show', $payment) }}" class="queue-item text-dark">
                                        <span class="guest-avatar" style="background:#fff0ec;color:#d65f46"><i class="ti ti-credit-card"></i></span>
                                        <div class="flex-grow-1 min-w-0"><strong>{{ $payment->reservation?->guest_name ?? $payment->payment_code }}</strong><small class="d-block text-muted text-truncate">{{ $payment->reservation?->booking_code ?? $payment->payment_code }} · {{ $payment->method?->name ?? 'Midtrans' }}</small></div>
                                        <strong class="text-danger">Rp{{ number_format((float) $payment->amount, 0, ',', '.') }}</strong>
                                    </a>
                                @empty
                                    <div class="empty-dashboard-state"><i class="ti ti-credit-card-off"></i><strong>Tidak ada pembayaran pending</strong></div>
                                @endforelse
                                @if ($pendingPayments->isNotEmpty())<div class="text-center py-3"><a href="{{ route('receptionist.payments.index', ['status' => 'pending']) }}" class="fw-semibold">Buka pembayaran pending <i class="ti ti-arrow-right ms-1"></i></a></div>@endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card dashboard-card mt-4">
            <div class="card-header"><span class="section-eyebrow">Akses cepat</span><h5 class="fw-semibold mb-0 mt-1">Pekerjaan Receptionist</h5></div>
            <div class="card-body p-4">
                <div class="row g-3">
                    @foreach ([
                        ['Reservasi Baru', 'Buat reservasi tamu walk-in', 'ti-calendar-plus', route('receptionist.reservations.walk-in.create')],
                        ['Data Tamu', 'Lihat profil dan histori menginap', 'ti-users', route('receptionist.guests.index')],
                        ['Status Kamar', 'Periksa kesiapan seluruh kamar', 'ti-bed', route('receptionist.rooms.index')],
                        ['Folio Tamu', 'Pantau tagihan berjalan', 'ti-receipt-2', route('receptionist.folios.index')],
                    ] as [$title, $description, $icon, $url])
                        <div class="col-xl-3 col-md-6"><a href="{{ $url }}" class="quick-action h-100"><span class="quick-action-icon"><i class="ti {{ $icon }} fs-6"></i></span><span><strong class="d-block">{{ $title }}</strong><small class="text-muted">{{ $description }}</small></span><i class="ti ti-chevron-right ms-auto text-muted"></i></a></div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const clock = document.getElementById('dashboard-live-clock');
            if (!clock) return;

            const formatter = new Intl.DateTimeFormat('id-ID', {
                timeZone: 'Asia/Jakarta', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false
            });
            const updateClock = () => { clock.textContent = formatter.format(new Date()).replaceAll('.', ':'); };
            updateClock();
            window.setInterval(updateClock, 1000);
        });
    </script>
@endpush
