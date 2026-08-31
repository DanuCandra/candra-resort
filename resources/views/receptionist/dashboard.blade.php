@extends('layouts.main')

@section('title', 'Dashboard Receptionist')

@push('styles')
    <style>
        .reception-dashboard { --dash-primary:#5d87ff; --dash-ink:#17233c; --dash-muted:#6b778c; --dash-border:#e8edf5; padding-bottom:12px; font-family:var(--bs-body-font-family); font-size:var(--bs-body-font-size); }
        .command-hero { position:relative; overflow:hidden; margin-bottom:24px; border:0; border-radius:24px; background:linear-gradient(128deg,#294eae 0%,#4f79e8 48%,#79a3ff 100%); box-shadow:0 20px 48px rgba(66,105,213,.25); color:#fff; }
        .command-hero::before,.command-hero::after { position:absolute; border:1px solid rgba(255,255,255,.14); border-radius:50%; content:''; }
        .command-hero::before { top:-185px; right:-90px; width:340px; height:340px; box-shadow:0 0 0 48px rgba(255,255,255,.025); }
        .command-hero::after { right:29%; bottom:-145px; width:220px; height:220px; background:rgba(255,255,255,.025); }
        .command-hero .card-body { position:relative; z-index:1; padding:34px 38px; }
        .command-grid { display:grid; grid-template-columns:minmax(0,1.35fr) minmax(330px,.65fr); align-items:center; gap:32px; }
        .command-date { display:inline-flex; align-items:center; gap:8px; margin-bottom:15px; padding:7px 12px; border:1px solid rgba(255,255,255,.2); border-radius:999px; background:rgba(255,255,255,.1); color:rgba(255,255,255,.92); font-size:12px; font-weight:600; backdrop-filter:blur(7px); }
        .command-title { margin:0 0 8px; color:#fff; font-size:31px; font-weight:800; letter-spacing:-.025em; }
        .command-copy { max-width:650px; margin:0; color:rgba(255,255,255,.7); font-size:var(--bs-body-font-size); line-height:1.65; }
        .command-actions { display:flex; flex-wrap:wrap; gap:9px; margin-top:22px; }
        .command-action { display:inline-flex; align-items:center; justify-content:center; gap:8px; min-height:41px; padding:9px 14px; border:1px solid rgba(255,255,255,.2); border-radius:11px; background:rgba(255,255,255,.1); color:#fff; font-size:13px; font-weight:700; transition:.18s ease; }
        .command-action:hover { transform:translateY(-2px); border-color:#fff; background:#fff; color:#416bd5; }
        .command-action.is-primary { border-color:#fff; background:#fff; color:#416bd5; }
        .shift-card { padding:20px; border:1px solid rgba(255,255,255,.15); border-radius:19px; background:rgba(17,42,105,.19); box-shadow:inset 0 1px 0 rgba(255,255,255,.06); backdrop-filter:blur(9px); }
        .shift-clock-row { display:flex; align-items:center; justify-content:space-between; gap:16px; padding-bottom:15px; border-bottom:1px solid rgba(255,255,255,.12); }
        .shift-clock-row small { display:block; margin-bottom:2px; color:rgba(255,255,255,.58); font-size:11px; font-weight:700; letter-spacing:.07em; text-transform:uppercase; }
        .shift-clock { color:#fff; font-size:27px; font-weight:800; letter-spacing:.04em; }
        .online-pill { display:inline-flex; align-items:center; gap:6px; padding:6px 9px; border-radius:999px; background:rgba(79,205,147,.17); color:#bce9cf; font-size:11px; font-weight:700; }
        .online-pill::before { width:6px; height:6px; border-radius:50%; background:#71d5a3; box-shadow:0 0 0 4px rgba(113,213,163,.1); content:''; }
        .shift-stats { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:9px; margin-top:16px; }
        .shift-stat { padding:10px; border-radius:11px; background:rgba(255,255,255,.075); }
        .shift-stat small { display:block; margin-bottom:3px; color:rgba(255,255,255,.53); font-size:11px; font-weight:700; text-transform:uppercase; }
        .shift-stat strong { display:block; overflow:hidden; color:#fff; font-size:14px; text-overflow:ellipsis; white-space:nowrap; }
        .metric-card { --metric-color:#5d87ff; position:relative; display:block; height:calc(100% - 20px); min-height:156px; margin-bottom:20px; overflow:hidden; border:1px solid var(--dash-border); border-radius:18px; background:#fff; color:inherit; box-shadow:0 8px 25px rgba(24,45,89,.05); transition:transform .2s ease,border-color .2s ease,box-shadow .2s ease; }
        .metric-card::after { position:absolute; right:-32px; bottom:-42px; width:105px; height:105px; border-radius:50%; background:color-mix(in srgb,var(--metric-color) 7%,transparent); content:''; }
        .metric-card:hover { transform:translateY(-4px); border-color:color-mix(in srgb,var(--metric-color) 28%,var(--dash-border)); color:inherit; box-shadow:0 15px 34px rgba(24,45,89,.1); }
        .metric-card-body { position:relative; z-index:1; padding:20px; }
        .metric-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:17px; }
        .metric-icon { display:inline-flex; align-items:center; justify-content:center; width:43px; height:43px; border-radius:13px; background:color-mix(in srgb,var(--metric-color) 12%,white); color:var(--metric-color); font-size:20px; }
        .metric-tag { padding:5px 8px; border-radius:999px; background:#f4f7fb; color:#7b879a; font-size:11px; font-weight:800; letter-spacing:.05em; text-transform:uppercase; }
        .metric-content { display:flex; align-items:flex-end; justify-content:space-between; gap:12px; }
        .metric-content small { display:block; margin-bottom:3px; color:#8a96a8; font-size:12px; }
        .metric-content h3 { margin:0; color:var(--dash-ink); font-size:15px; font-weight:700; }
        .metric-value { color:var(--metric-color); font-size:30px; font-weight:800; line-height:1; }
        .attention-bar { display:flex; align-items:center; justify-content:space-between; gap:18px; margin:2px 0 24px; padding:14px 16px; border:1px solid #e3e9f4; border-radius:15px; background:linear-gradient(100deg,#f7f9fd,#fff); box-shadow:0 6px 20px rgba(31,50,91,.035); }
        .attention-main { display:flex; align-items:center; gap:11px; min-width:0; }
        .attention-icon { display:inline-flex; align-items:center; justify-content:center; width:39px; height:39px; border-radius:12px; background:#eaf1ff; color:#4d77df; font-size:18px; flex:0 0 auto; }
        .attention-bar.has-urgent { border-color:#f5d8d2; background:linear-gradient(100deg,#fff6f4,#fff); }
        .attention-bar.has-urgent .attention-icon { background:#fee9e5; color:#d75237; }
        .attention-copy strong { display:block; margin-bottom:2px; color:var(--dash-ink); font-size:14px; }
        .attention-copy small { display:block; color:#7e899a; font-size:12px; line-height:1.5; }
        .attention-chips { display:flex; flex-wrap:wrap; justify-content:flex-end; gap:7px; }
        .attention-chip { display:inline-flex; align-items:center; gap:6px; padding:7px 9px; border-radius:9px; background:#fff; color:#647187; font-size:12px; font-weight:700; box-shadow:0 3px 10px rgba(30,48,85,.05); }
        .attention-chip b { color:var(--dash-ink); }
        .ops-card { height:100%; overflow:hidden; border:1px solid var(--dash-border); border-radius:19px; background:#fff; box-shadow:0 8px 28px rgba(17,38,85,.045); }
        .ops-card-header { display:flex; align-items:center; justify-content:space-between; gap:14px; min-height:72px; padding:18px 20px; border-bottom:1px solid var(--dash-border); }
        .section-eyebrow { display:block; margin-bottom:3px; color:var(--dash-primary); font-size:11px; font-weight:800; letter-spacing:.09em; text-transform:uppercase; }
        .ops-card-header h4 { margin:0; color:var(--dash-ink); font-size:16px; font-weight:700; }
        .section-link { display:inline-flex; align-items:center; gap:5px; padding:7px 10px; border-radius:9px; background:#eef3ff; color:#4c76dc; font-size:12px; font-weight:800; white-space:nowrap; }
        .section-link:hover { background:#5d87ff; color:#fff; }
        .occupancy-wrap { display:flex; align-items:center; gap:22px; padding:20px 20px 15px; }
        .occupancy-ring { display:grid; place-items:center; width:138px; height:138px; border-radius:50%; background:conic-gradient(var(--dash-primary) calc(var(--occupancy) * 1%),#edf2f9 0); box-shadow:0 10px 28px rgba(93,135,255,.14); flex:0 0 auto; }
        .occupancy-ring-inner { display:grid; place-items:center; width:104px; height:104px; border-radius:50%; background:#fff; text-align:center; }
        .occupancy-ring-inner strong { display:block; color:var(--dash-ink); font-size:25px; line-height:1.1; }
        .occupancy-ring-inner small { color:#8b96a7; font-size:12px; }
        .occupancy-copy h5 { margin:0 0 4px; color:var(--dash-ink); font-size:16px; }
        .occupancy-copy p { margin:0; color:#8a95a7; font-size:12px; line-height:1.55; }
        .room-status-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:8px; padding:0 20px 20px; }
        .room-status { display:grid; grid-template-columns:auto minmax(0,1fr) auto; align-items:center; gap:8px; padding:10px; border:1px solid #edf1f6; border-radius:11px; color:#536076; font-size:12px; transition:.16s ease; }
        .room-status:hover { transform:translateY(-1px); border-color:#cedbfa; background:#f9fbff; color:#416bd5; }
        .status-dot { width:8px; height:8px; border-radius:50%; }
        .room-status strong { color:var(--dash-ink); font-size:14px; }
        .modern-tabs { gap:5px; padding:4px; border:0; border-radius:11px; background:#f3f6fa; }
        .modern-tabs .nav-link { border:0!important; border-radius:8px; color:#69768a; font-size:12px; font-weight:700; padding:7px 10px; }
        .modern-tabs .nav-link.active { background:#fff; color:var(--dash-primary); box-shadow:0 3px 12px rgba(24,45,85,.08); }
        .queue-monitor-header { flex-wrap:nowrap; }
        .queue-monitor-header > div:first-child { min-width:122px; flex:1 1 auto; }
        .queue-monitor-tabs { max-width:270px; margin-left:auto; overflow-x:auto; flex:0 1 auto; flex-wrap:nowrap; scrollbar-width:none; }
        .queue-monitor-tabs::-webkit-scrollbar { display:none; }
        .queue-monitor-tabs .nav-item { flex:0 0 auto; }
        .queue-monitor-tabs .nav-link { display:flex; align-items:center; gap:3px; padding:7px 8px; white-space:nowrap; }
        .agenda-list,.queue-list { padding:2px 20px; }
        .agenda-item,.queue-item { display:flex; align-items:center; gap:12px; padding:14px 0; border-bottom:1px solid #edf1f6; color:inherit; }
        .agenda-item:last-child,.queue-item:last-child { border-bottom:0; }
        .guest-avatar,.queue-icon { display:inline-flex; align-items:center; justify-content:center; width:42px; height:42px; border-radius:13px; background:linear-gradient(135deg,#e8efff,#f5f8ff); color:#426fdc; font-size:14px; font-weight:800; flex:0 0 auto; }
        .guest-info,.queue-info { min-width:0; flex:1; }
        .guest-info strong,.queue-info strong { display:block; overflow:hidden; margin-bottom:3px; color:var(--dash-ink); font-size:14px; text-overflow:ellipsis; white-space:nowrap; }
        .guest-info small,.queue-info small { display:block; overflow:hidden; color:#8792a3; font-size:12px; text-overflow:ellipsis; white-space:nowrap; }
        .agenda-meta { display:flex; align-items:center; gap:6px; flex:0 0 auto; }
        .room-chip { display:inline-flex; align-items:center; gap:5px; padding:6px 8px; border-radius:8px; background:#f3f6fa; color:#59677c; font-size:12px; font-weight:700; }
        .item-action { display:inline-flex; align-items:center; justify-content:center; width:31px; height:31px; border-radius:9px; background:#edf3ff; color:#4d77df; flex:0 0 auto; }
        .item-action:hover { background:#5d87ff; color:#fff; }
        .queue-priority { display:inline-flex; align-items:center; justify-content:center; min-width:58px; padding:5px 8px; border-radius:999px; font-size:11px; font-weight:800; }
        .queue-status { flex:0 0 auto; font-size:11px; }
        .queue-amount { color:#e05f45; font-size:13px; font-weight:800; white-space:nowrap; }
        .empty-ops { padding:38px 18px; color:#8994a5; text-align:center; }
        .empty-ops i { display:inline-flex; align-items:center; justify-content:center; width:48px; height:48px; margin-bottom:9px; border-radius:15px; background:#f1f5fa; color:#a7b2c2; font-size:23px; }
        .empty-ops strong { display:block; color:#5e6a7c; font-size:14px; }
        .empty-ops small { display:block; margin-top:3px; font-size:12px; }
        .list-footer { padding:12px 20px; border-top:1px solid var(--dash-border); text-align:center; }
        .list-footer a { color:#4e77de; font-size:12px; font-weight:800; }
        .work-dock { display:grid; grid-template-columns:repeat(6,minmax(0,1fr)); gap:10px; padding:18px 20px 20px; }
        .dock-action { display:flex; min-height:112px; flex-direction:column; align-items:flex-start; padding:14px; border:1px solid #e9edf4; border-radius:14px; color:var(--dash-ink); transition:.18s ease; }
        .dock-action:hover { transform:translateY(-3px); border-color:#ccdafb; background:#f9fbff; color:#416bd5; box-shadow:0 9px 22px rgba(42,72,137,.07); }
        .dock-icon { display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; margin-bottom:11px; border-radius:11px; background:#edf3ff; color:#4e78df; font-size:17px; }
        .dock-action strong { display:block; margin-bottom:3px; font-size:14px; }
        .dock-action small { color:#8994a5; font-size:12px; line-height:1.45; }
        @media (max-width:1199.98px) { .command-grid { grid-template-columns:1fr; } .work-dock { grid-template-columns:repeat(3,minmax(0,1fr)); } }
        @media (max-width:991.98px) { .attention-bar { align-items:flex-start; flex-direction:column; } .attention-chips { justify-content:flex-start; } .occupancy-wrap { justify-content:center; } }
        @media (max-width:1399.98px) { .queue-monitor-tabs .badge { display:none; } }
        @media (max-width:767.98px) { .command-hero .card-body { padding:25px 22px; } .command-title { font-size:26px; } .command-actions { width:100%; } .command-action { flex:1; } .shift-stats { grid-template-columns:1fr; } .metric-card { min-height:145px; } .ops-card-header { align-items:flex-start; flex-direction:column; } .modern-tabs { width:100%; overflow-x:auto; flex-wrap:nowrap; } .queue-monitor-header { align-items:center; flex-direction:row; } .queue-monitor-tabs { width:auto; max-width:62%; } .agenda-item,.queue-item { align-items:flex-start; } .agenda-meta { align-items:flex-end; flex-direction:column; } .room-chip:first-child { display:none; } .queue-status { margin-left:auto; } }
        @media (max-width:575.98px) { .attention-chips { display:grid; width:100%; grid-template-columns:1fr 1fr; } .occupancy-wrap { align-items:flex-start; flex-direction:column; } .occupancy-ring { margin:0 auto; } .room-status-grid { grid-template-columns:1fr; } .work-dock { grid-template-columns:repeat(2,minmax(0,1fr)); } .queue-priority { display:none; } }
    </style>
@endpush

@section('content')
    @php
        $priorityCards = [
            ['Kedatangan Hari Ini', $metrics['arrivals'], 'Menunggu proses check-in', 'ti-plane-arrival', '#5d87ff', 'Check-in', route('receptionist.checkin.index')],
            ['Keberangkatan Hari Ini', $metrics['departures'], 'Perlu penyelesaian folio', 'ti-plane-departure', '#ffae1f', 'Check-out', route('receptionist.checkout.index')],
            ['Permintaan Aktif', $metrics['guest_requests'], $metrics['urgent_requests'].' permintaan mendesak', 'ti-bell-ringing', '#13a989', 'Layanan', route('receptionist.guest-requests.index')],
            ['Pembayaran Pending', $metrics['pending_payments'], 'Rp'.number_format($metrics['pending_payment_amount'], 0, ',', '.').' tertunda', 'ti-credit-card', '#ea7358', 'Keuangan', route('receptionist.payments.index', ['status' => 'pending'])],
        ];
    @endphp

    <div id="reception-command-center" class="reception-dashboard">
        <section class="card command-hero">
            <div class="card-body">
                <div class="command-grid">
                    <div>
                        <span class="command-date"><i class="ti ti-calendar-event"></i>{{ now()->translatedFormat('l, d F Y') }}</span>
                        <h1 class="command-title">Dashboard Operasional</h1>
                        <p class="command-copy">Selamat bertugas, {{ auth()->user()->name }}. Pantau kedatangan, kondisi kamar, layanan tamu, dan transaksi penting dalam satu pusat kendali.</p>
                        <div class="command-actions">
                            <a href="{{ route('receptionist.reservations.walk-in.create') }}" class="command-action is-primary"><i class="ti ti-user-plus"></i>Reservasi Walk-in</a>
                            <a href="{{ route('receptionist.checkin.index') }}" class="command-action"><i class="ti ti-login"></i>Proses Check-in</a>
                            <a href="{{ route('receptionist.rooms.index') }}" class="command-action"><i class="ti ti-bed"></i>Lihat Kamar</a>
                        </div>
                    </div>
                    <aside class="shift-card" aria-label="Ringkasan shift hari ini">
                        <div class="shift-clock-row"><div><small>Waktu hotel</small><strong id="dashboard-live-clock" class="shift-clock">{{ now()->format('H:i:s') }}</strong></div><span class="online-pill">Operasional aktif</span></div>
                        <div class="shift-stats">
                            <div class="shift-stat"><small>Check-in selesai</small><strong>{{ $metrics['checked_in_today'] }} tamu</strong></div>
                            <div class="shift-stat"><small>Check-out selesai</small><strong>{{ $metrics['checked_out_today'] }} tamu</strong></div>
                            <div class="shift-stat"><small>Dana diterima</small><strong>Rp{{ number_format($metrics['paid_today'], 0, ',', '.') }}</strong></div>
                        </div>
                    </aside>
                </div>
            </div>
        </section>

        <div class="row">
            @foreach ($priorityCards as [$label, $value, $helper, $icon, $color, $tag, $url])
                <div class="col-xl-3 col-md-6">
                    <a href="{{ $url }}" class="metric-card" style="--metric-color:{{ $color }}">
                        <div class="metric-card-body">
                            <div class="metric-head"><span class="metric-icon"><i class="ti {{ $icon }}"></i></span><span class="metric-tag">{{ $tag }}</span></div>
                            <div class="metric-content"><div><small>{{ $helper }}</small><h3>{{ $label }}</h3></div><strong class="metric-value">{{ $value }}</strong></div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="attention-bar {{ $metrics['urgent_requests'] > 0 ? 'has-urgent' : '' }}">
            <div class="attention-main">
                <span class="attention-icon"><i class="ti {{ $metrics['urgent_requests'] > 0 ? 'ti-alert-triangle' : 'ti-circle-check' }}"></i></span>
                <div class="attention-copy"><strong>{{ $metrics['urgent_requests'] > 0 ? 'Ada prioritas yang perlu segera ditangani' : 'Operasional terkendali' }}</strong><small>{{ $metrics['urgent_requests'] > 0 ? 'Dahulukan permintaan mendesak dan pastikan tamu mendapatkan pembaruan status.' : 'Tetap pantau antrean baru dan kesiapan kamar sepanjang shift.' }}</small></div>
            </div>
            <div class="attention-chips">
                <a href="{{ route('receptionist.guest-requests.index') }}" class="attention-chip"><i class="ti ti-bell-ringing"></i><b>{{ $metrics['urgent_requests'] }}</b> mendesak</a>
                <a href="{{ route('receptionist.rooms.index', ['status' => 'cleaning']) }}" class="attention-chip"><i class="ti ti-spray"></i><b>{{ $metrics['cleaning'] }}</b> dibersihkan</a>
                <a href="{{ route('receptionist.service-orders.index') }}" class="attention-chip"><i class="ti ti-bell"></i><b>{{ $metrics['service_orders'] }}</b> layanan</a>
                <a href="{{ route('receptionist.food-orders.index') }}" class="attention-chip"><i class="ti ti-tools-kitchen-2"></i><b>{{ $metrics['food_orders'] }}</b> F&amp;B</a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-4">
                <section class="ops-card">
                    <header class="ops-card-header"><div><span class="section-eyebrow">Kondisi real-time</span><h4>Status Kamar</h4></div><a href="{{ route('receptionist.rooms.index') }}" class="section-link">Kelola <i class="ti ti-arrow-right"></i></a></header>
                    <div class="occupancy-wrap">
                        <div class="occupancy-ring" style="--occupancy:{{ min(100, $metrics['occupancy_rate']) }}"><div class="occupancy-ring-inner"><div><strong>{{ $metrics['occupancy_rate'] }}%</strong><small>Okupansi</small></div></div></div>
                        <div class="occupancy-copy"><h5>{{ $metrics['occupied'] }} dari {{ $metrics['active_rooms'] }} kamar terisi</h5><p>Status kamar diperbarui dari proses reservasi, check-in, check-out, dan housekeeping.</p></div>
                    </div>
                    <div class="room-status-grid">
                        @foreach ([
                            ['Tersedia', $metrics['available'], '#13deb9', 'available'], ['Terisi', $metrics['occupied'], '#5d87ff', 'occupied'],
                            ['Dipesan', $metrics['reserved'], '#49beff', 'reserved'], ['Dibersihkan', $metrics['cleaning'], '#ffae1f', 'cleaning'],
                            ['Maintenance', $metrics['maintenance'], '#fa896b', 'maintenance'], ['Tidak tersedia', $metrics['unavailable'], '#7c8fac', 'unavailable'],
                        ] as [$label, $count, $color, $status])
                            <a href="{{ route('receptionist.rooms.index', ['status' => $status]) }}" class="room-status"><span class="status-dot" style="background:{{ $color }}"></span><span>{{ $label }}</span><strong>{{ $count }}</strong></a>
                        @endforeach
                    </div>
                </section>
            </div>

            <div class="col-xl-8">
                <section class="ops-card">
                    <header class="ops-card-header">
                        <div><span class="section-eyebrow">Pergerakan tamu</span><h4>Agenda Hari Ini</h4></div>
                        <ul class="nav nav-tabs modern-tabs" role="tablist">
                            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#arrival-pane" type="button">Kedatangan <span class="badge bg-light-primary text-primary ms-1">{{ $metrics['arrivals'] }}</span></button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#departure-pane" type="button">Keberangkatan <span class="badge bg-light-warning text-warning ms-1">{{ $metrics['departures'] }}</span></button></li>
                        </ul>
                    </header>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="arrival-pane">
                            @if ($arrivals->isNotEmpty())
                                <div class="agenda-list">
                                    @foreach ($arrivals as $reservation)
                                        <div class="agenda-item">
                                            <span class="guest-avatar">{{ str($reservation->guest_name)->substr(0, 1)->upper() }}</span>
                                            <div class="guest-info"><a href="{{ route('receptionist.reservations.show', $reservation) }}"><strong>{{ $reservation->guest_name }}</strong></a><small>{{ $reservation->booking_code }} &middot; {{ $reservation->roomType?->name ?? 'Tipe kamar' }}</small></div>
                                            <div class="agenda-meta">
                                                @if ($reservation->estimated_arrival_time)<span class="room-chip"><i class="ti ti-clock"></i>{{ str($reservation->estimated_arrival_time)->substr(0, 5) }}</span>@endif
                                                <span class="room-chip"><i class="ti ti-door"></i>{{ $reservation->room?->room_number ?? 'Belum dipilih' }}</span>
                                            </div>
                                            <a href="{{ route('receptionist.checkin.create', $reservation) }}" class="item-action" title="Proses check-in"><i class="ti ti-login"></i></a>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="list-footer"><a href="{{ route('receptionist.checkin.index') }}">Lihat semua antrean check-in <i class="ti ti-arrow-right ms-1"></i></a></div>
                            @else
                                <div class="empty-ops"><i class="ti ti-calendar-event"></i><strong>Tidak ada kedatangan hari ini</strong><small>Agenda check-in sudah kosong.</small></div>
                            @endif
                        </div>
                        <div class="tab-pane fade" id="departure-pane">
                            @if ($departures->isNotEmpty())
                                <div class="agenda-list">
                                    @foreach ($departures as $reservation)
                                        <div class="agenda-item">
                                            <span class="guest-avatar" style="background:#fff4e1;color:#d98700">{{ str($reservation->guest_name)->substr(0, 1)->upper() }}</span>
                                            <div class="guest-info"><a href="{{ route('receptionist.reservations.show', $reservation) }}"><strong>{{ $reservation->guest_name }}</strong></a><small>{{ $reservation->booking_code }} &middot; {{ $reservation->roomType?->name ?? 'Tipe kamar' }}</small></div>
                                            <span class="room-chip"><i class="ti ti-door"></i>Kamar {{ $reservation->room?->room_number ?? '-' }}</span>
                                            <a href="{{ route('receptionist.checkout.index') }}" class="item-action" style="background:#fff4e1;color:#d98700" title="Proses check-out"><i class="ti ti-logout"></i></a>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="list-footer"><a href="{{ route('receptionist.checkout.index') }}">Lihat semua antrean check-out <i class="ti ti-arrow-right ms-1"></i></a></div>
                            @else
                                <div class="empty-ops"><i class="ti ti-calendar-event"></i><strong>Tidak ada keberangkatan hari ini</strong><small>Belum ada check-out yang perlu diproses.</small></div>
                            @endif
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <div class="row g-4 mt-1">
            <div class="col-xl-7">
                <section class="ops-card">
                    <header class="ops-card-header"><div><span class="section-eyebrow">Antrean prioritas</span><h4>Permintaan Tamu Aktif</h4></div><a href="{{ route('receptionist.guest-requests.index') }}" class="section-link">Lihat Semua <i class="ti ti-arrow-right"></i></a></header>
                    @if ($recentRequests->isNotEmpty())
                        <div class="queue-list">
                            @foreach ($recentRequests as $guestRequest)
                                @php
                                    $priorityStyle = match ($guestRequest->priority) {
                                        'urgent' => ['Mendesak', '#fee9e5', '#d75237'],
                                        'high' => ['Tinggi', '#fff2dd', '#c37800'],
                                        'normal' => ['Normal', '#e8f7f3', '#087f67'],
                                        default => ['Rendah', '#edf1f7', '#65738a'],
                                    };
                                @endphp
                                <a href="{{ route('receptionist.guest-requests.show', $guestRequest) }}" class="queue-item">
                                    <span class="queue-icon"><i class="ti ti-bell-ringing"></i></span>
                                    <div class="queue-info"><strong>{{ $guestRequest->title }}</strong><small>{{ $guestRequest->stay?->guest_name ?? 'Tamu' }} &middot; Kamar {{ $guestRequest->room?->room_number ?? '-' }} &middot; {{ $guestRequest->requested_at?->diffForHumans() ?? '-' }}</small></div>
                                    <span class="queue-priority" style="background:{{ $priorityStyle[1] }};color:{{ $priorityStyle[2] }}">{{ $priorityStyle[0] }}</span>
                                    <span class="badge bg-light-{{ $guestRequest->status->badgeClass() }} text-{{ $guestRequest->status->badgeClass() }} queue-status">{{ $guestRequest->status->label() }}</span>
                                    <i class="ti ti-chevron-right text-muted"></i>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-ops"><i class="ti ti-circle-check"></i><strong>Semua permintaan sudah tertangani</strong><small>Tidak ada antrean layanan tamu aktif.</small></div>
                    @endif
                </section>
            </div>

            <div class="col-xl-5">
                <section class="ops-card">
                    <header class="ops-card-header queue-monitor-header">
                        <div><span class="section-eyebrow">Monitoring layanan</span><h4>Antrean Operasional</h4></div>
                        <ul class="nav nav-tabs modern-tabs queue-monitor-tabs" role="tablist">
                            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#food-queue-pane" type="button">F&amp;B <span class="badge bg-light-info text-info ms-1">{{ $metrics['food_orders'] }}</span></button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#service-queue-pane" type="button">Layanan <span class="badge bg-light-primary text-primary ms-1">{{ $metrics['service_orders'] }}</span></button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#payment-queue-pane" type="button">Bayar <span class="badge bg-light-danger text-danger ms-1">{{ $metrics['pending_payments'] }}</span></button></li>
                        </ul>
                    </header>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="food-queue-pane">
                            @if ($recentFoodOrders->isNotEmpty())
                                <div class="queue-list">
                                    @foreach ($recentFoodOrders as $order)
                                        <a href="{{ route('receptionist.food-orders.show', $order) }}" class="queue-item"><span class="queue-icon" style="background:#eaf8ff;color:#1287bd"><i class="ti ti-tools-kitchen-2"></i></span><div class="queue-info"><strong>{{ $order->order_code }}</strong><small>Kamar {{ $order->room?->room_number ?? '-' }} &middot; {{ $order->items_count }} item &middot; {{ $order->ordered_at?->diffForHumans() ?? '-' }}</small></div><span class="badge bg-light-{{ $order->status->badgeClass() }} text-{{ $order->status->badgeClass() }} queue-status">{{ $order->status->label() }}</span></a>
                                    @endforeach
                                </div>
                                <div class="list-footer"><a href="{{ route('receptionist.food-orders.index') }}">Buka semua pesanan F&amp;B <i class="ti ti-arrow-right ms-1"></i></a></div>
                            @else
                                <div class="empty-ops"><i class="ti ti-tools-kitchen-2"></i><strong>Tidak ada pesanan F&amp;B aktif</strong><small>Pesanan baru akan muncul di sini.</small></div>
                            @endif
                        </div>
                        <div class="tab-pane fade" id="service-queue-pane">
                            @if ($recentServiceOrders->isNotEmpty())
                                <div class="queue-list">
                                    @foreach ($recentServiceOrders as $order)
                                        <a href="{{ route('receptionist.service-orders.show', $order) }}" class="queue-item"><span class="queue-icon" style="background:#eef1ff;color:#6557c7"><i class="ti ti-bell"></i></span><div class="queue-info"><strong>{{ $order->service?->name ?? 'Layanan hotel' }}</strong><small>Kamar {{ $order->room?->room_number ?? '-' }} &middot; {{ $order->order_code }} &middot; {{ $order->scheduled_at?->translatedFormat('d M, H:i') ?? $order->created_at->diffForHumans() }}</small></div><span class="badge bg-light-{{ $order->status->badgeClass() }} text-{{ $order->status->badgeClass() }} queue-status">{{ $order->status->label() }}</span></a>
                                    @endforeach
                                </div>
                                <div class="list-footer"><a href="{{ route('receptionist.service-orders.index') }}">Buka semua pesanan layanan <i class="ti ti-arrow-right ms-1"></i></a></div>
                            @else
                                <div class="empty-ops"><i class="ti ti-circle-check"></i><strong>Tidak ada layanan aktif</strong><small>Pesanan layanan baru akan muncul di sini.</small></div>
                            @endif
                        </div>
                        <div class="tab-pane fade" id="payment-queue-pane">
                            @if ($pendingPayments->isNotEmpty())
                                <div class="queue-list">
                                    @foreach ($pendingPayments as $payment)
                                        <a href="{{ route('receptionist.payments.show', $payment) }}" class="queue-item"><span class="queue-icon" style="background:#fff0ec;color:#d65f46"><i class="ti ti-credit-card"></i></span><div class="queue-info"><strong>{{ $payment->reservation?->guest_name ?? $payment->payment_code }}</strong><small>{{ $payment->reservation?->booking_code ?? $payment->payment_code }} &middot; {{ $payment->method?->name ?? 'Midtrans' }}</small></div><strong class="queue-amount">Rp{{ number_format((float) $payment->amount, 0, ',', '.') }}</strong></a>
                                    @endforeach
                                </div>
                                <div class="list-footer"><a href="{{ route('receptionist.payments.index', ['status' => 'pending']) }}">Buka pembayaran pending <i class="ti ti-arrow-right ms-1"></i></a></div>
                            @else
                                <div class="empty-ops"><i class="ti ti-credit-card-off"></i><strong>Tidak ada pembayaran pending</strong><small>Semua transaksi sudah tersinkronisasi.</small></div>
                            @endif
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <section class="ops-card mt-4">
            <header class="ops-card-header"><div><span class="section-eyebrow">Akses cepat</span><h4>Pusat Kerja Receptionist</h4></div><span class="text-muted fs-2">Akses fungsi yang paling sering digunakan</span></header>
            <div class="work-dock">
                @foreach ([
                    ['Reservasi Walk-in', 'Buat booking tamu langsung', 'ti-user-plus', route('receptionist.reservations.walk-in.create')],
                    ['Daftar Reservasi', 'Cari dan kelola reservasi', 'ti-calendar-event', route('receptionist.reservations.index')],
                    ['Direktori Tamu', 'Profil dan riwayat kunjungan', 'ti-users', route('receptionist.guests.index')],
                    ['Status Kamar', 'Kesiapan seluruh kamar', 'ti-bed', route('receptionist.rooms.index')],
                    ['Folio Tamu', 'Tagihan masa inap aktif', 'ti-receipt-2', route('receptionist.folios.index')],
                    ['Pembayaran', 'Pantau seluruh transaksi', 'ti-credit-card', route('receptionist.payments.index')],
                ] as [$title, $description, $icon, $url])
                    <a href="{{ $url }}" class="dock-action"><span class="dock-icon"><i class="ti {{ $icon }}"></i></span><strong>{{ $title }}</strong><small>{{ $description }}</small></a>
                @endforeach
            </div>
        </section>
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
