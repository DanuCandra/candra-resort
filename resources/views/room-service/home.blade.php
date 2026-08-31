@extends('layouts.guest')

@section('title', 'Portal Kamar '.$access->room->room_number)

@push('styles')
    <style>
        .room-portal { --portal-accent:#dfa974; --portal-accent-dark:#b77537; --portal-ink:#20252a; --portal-muted:#7b7f84; --portal-border:#ebe7e1; min-height:80vh; padding:62px 0 90px; background:#f7f6f3; }
        .portal-hero { position:relative; overflow:hidden; margin-bottom:18px; padding:34px; border-radius:24px; background:linear-gradient(125deg,#1d2428 0%,#30393d 57%,#4d4439 100%); box-shadow:0 20px 42px rgba(26,29,31,.17); color:#fff; }
        .portal-hero::before { position:absolute; top:-140px; right:-65px; width:330px; height:330px; border:1px solid rgba(223,169,116,.2); border-radius:50%; box-shadow:0 0 0 45px rgba(223,169,116,.04),0 0 0 90px rgba(223,169,116,.025); content:''; }
        .portal-hero::after { position:absolute; right:27%; bottom:-135px; width:220px; height:220px; border-radius:50%; background:rgba(223,169,116,.055); content:''; }
        .portal-hero-content { position:relative; z-index:1; display:grid; grid-template-columns:minmax(0,1.35fr) minmax(310px,.65fr); align-items:center; gap:32px; }
        .portal-room-pill { display:inline-flex; align-items:center; gap:8px; padding:7px 12px; border:1px solid rgba(255,255,255,.16); border-radius:999px; background:rgba(255,255,255,.09); color:#f2cfaa; font-size:11px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; }
        .portal-hero h1 { max-width:670px; margin:14px 0 8px; color:#fff; font-size:36px; line-height:1.2; }
        .portal-hero-copy { max-width:620px; margin:0; color:rgba(255,255,255,.68); font-size:14px; line-height:1.65; }
        .portal-room-meta { display:flex; flex-wrap:wrap; gap:8px; margin-top:20px; }
        .portal-meta-chip { display:inline-flex; align-items:center; gap:7px; padding:8px 10px; border-radius:10px; background:rgba(255,255,255,.075); color:rgba(255,255,255,.78); font-size:10px; font-weight:600; }
        .portal-meta-chip i { color:#e3b98f; }
        .stay-card { padding:20px; border:1px solid rgba(255,255,255,.14); border-radius:18px; background:rgba(255,255,255,.08); box-shadow:inset 0 1px 0 rgba(255,255,255,.06); backdrop-filter:blur(8px); }
        .stay-card-heading { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:17px; }
        .stay-card-heading span:first-child { color:rgba(255,255,255,.57); font-size:9px; font-weight:800; letter-spacing:.08em; text-transform:uppercase; }
        .stay-active { display:inline-flex; align-items:center; gap:6px; padding:5px 8px; border-radius:999px; background:rgba(104,190,142,.16); color:#b5e4c9; font-size:9px; font-weight:700; }
        .stay-active::before { width:6px; height:6px; border-radius:50%; background:#72c294; box-shadow:0 0 0 4px rgba(114,194,148,.11); content:''; }
        .stay-date-grid { display:grid; grid-template-columns:1fr auto 1fr; align-items:center; gap:12px; }
        .stay-date small { display:block; margin-bottom:4px; color:rgba(255,255,255,.48); font-size:9px; font-weight:700; letter-spacing:.05em; text-transform:uppercase; }
        .stay-date strong { display:block; color:#fff; font:700 14px/1.35 "Lora",serif; }
        .stay-date.is-right { text-align:right; }
        .stay-date-arrow { color:#d8b28d; font-size:13px; }
        .stay-card-footer { display:flex; align-items:center; gap:8px; margin-top:17px; padding-top:14px; border-top:1px solid rgba(255,255,255,.1); color:rgba(255,255,255,.57); font-size:9px; line-height:1.5; }
        .stay-card-footer i { color:#e3b98f; }
        .portal-overview { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; margin-bottom:32px; }
        .portal-overview-card { display:flex; align-items:center; gap:12px; min-width:0; padding:15px 16px; border:1px solid var(--portal-border); border-radius:15px; background:#fff; box-shadow:0 7px 22px rgba(32,34,35,.04); }
        .overview-icon { display:flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:12px; background:#fff1e2; color:#b57437; font-size:16px; flex:0 0 auto; }
        .portal-overview-card:nth-child(2) .overview-icon { background:#edf2f7; color:#56728b; }
        .portal-overview-card:nth-child(3) .overview-icon { background:#eef5ee; color:#5a805f; }
        .portal-overview-card:nth-child(4) .overview-icon { background:#f5edf7; color:#7c5e82; }
        .portal-overview-card small { display:block; margin-bottom:2px; color:#96918b; font-size:9px; font-weight:700; letter-spacing:.055em; text-transform:uppercase; }
        .portal-overview-card strong { display:block; overflow:hidden; color:var(--portal-ink); font:700 16px/1.25 "Lora",serif; text-overflow:ellipsis; white-space:nowrap; }
        .portal-section-heading { display:flex; align-items:flex-end; justify-content:space-between; gap:20px; margin-bottom:17px; }
        .portal-eyebrow { display:block; margin-bottom:4px; color:#ad7136; font-size:10px; font-weight:800; letter-spacing:.09em; text-transform:uppercase; }
        .portal-section-heading h2 { margin:0 0 4px; color:var(--portal-ink); font-size:25px; }
        .portal-section-heading p { margin:0; color:var(--portal-muted); font-size:12px; }
        .portal-action-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:15px; margin-bottom:36px; }
        .portal-action { position:relative; display:flex; min-height:235px; overflow:hidden; padding:23px; border:1px solid var(--portal-border); border-radius:19px; background:#fff; color:inherit; box-shadow:0 9px 28px rgba(32,34,35,.055); transition:transform .22s ease,box-shadow .22s ease,border-color .22s ease; }
        .portal-action::after { position:absolute; right:-45px; bottom:-65px; width:155px; height:155px; border-radius:50%; background:rgba(223,169,116,.08); content:''; transition:transform .25s ease; }
        .portal-action:hover { transform:translateY(-4px); border-color:#dec3a7; color:inherit; box-shadow:0 16px 36px rgba(32,34,35,.095); }
        .portal-action:hover::after { transform:scale(1.16); }
        .portal-action.is-featured { border-color:#31383c; background:linear-gradient(145deg,#252c30,#394146); color:#fff; }
        .portal-action.is-featured::after { background:rgba(223,169,116,.1); }
        .portal-action-body { position:relative; z-index:1; display:flex; flex:1; flex-direction:column; align-items:flex-start; }
        .portal-action-icon { display:flex; align-items:center; justify-content:center; width:49px; height:49px; margin-bottom:19px; border-radius:15px; background:#fff1e2; color:#b57437; font-size:20px; }
        .portal-action.is-featured .portal-action-icon { background:rgba(223,169,116,.16); color:#f0bf8e; }
        .portal-action-tag { position:absolute; top:22px; right:22px; padding:5px 8px; border-radius:999px; background:#f3efe9; color:#8a7968; font-size:8px; font-weight:800; letter-spacing:.06em; text-transform:uppercase; }
        .portal-action.is-featured .portal-action-tag { background:rgba(255,255,255,.09); color:#e8c39e; }
        .portal-action h3 { margin:0 0 7px; color:var(--portal-ink); font-size:19px; }
        .portal-action.is-featured h3 { color:#fff; }
        .portal-action p { max-width:290px; margin:0 0 18px; color:#837e78; font-size:11px; line-height:1.65; }
        .portal-action.is-featured p { color:rgba(255,255,255,.62); }
        .portal-action-link { display:inline-flex; align-items:center; gap:7px; margin-top:auto; color:#a6652b; font-size:10px; font-weight:800; letter-spacing:.04em; text-transform:uppercase; }
        .portal-action.is-featured .portal-action-link { color:#e8b681; }
        .portal-action-link i { transition:transform .18s ease; }
        .portal-action:hover .portal-action-link i { transform:translateX(4px); }
        .activity-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:14px; margin-bottom:22px; }
        .activity-card { display:block; overflow:hidden; border:1px solid var(--portal-border); border-radius:17px; background:#fff; color:inherit; box-shadow:0 7px 24px rgba(32,34,35,.045); transition:transform .18s ease,border-color .18s ease,box-shadow .18s ease; }
        .activity-card:hover { transform:translateY(-2px); border-color:#dec3a7; color:inherit; box-shadow:0 12px 29px rgba(32,34,35,.075); }
        .activity-card-top { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:15px 17px; border-bottom:1px solid #f0ede8; }
        .activity-kind { display:flex; align-items:center; gap:9px; color:#5e5a55; font-size:10px; font-weight:800; letter-spacing:.04em; text-transform:uppercase; }
        .activity-kind-icon { display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:10px; background:#fff1e2; color:#b57437; font-size:13px; }
        .activity-count { min-width:24px; padding:5px 7px; border-radius:999px; background:#fff4de; color:#a57624; font-size:9px; font-weight:800; text-align:center; }
        .activity-body { min-height:121px; padding:17px; }
        .activity-body h3 { margin:0 0 6px; overflow:hidden; color:var(--portal-ink); font-size:14px; text-overflow:ellipsis; white-space:nowrap; }
        .activity-body p { margin:0; color:#918b84; font-size:10px; line-height:1.55; }
        .activity-status { display:flex; align-items:center; justify-content:space-between; gap:10px; margin-top:13px; }
        .activity-status .badge { font-size:9px; }
        .activity-status span:last-child { color:#a7662b; font-size:9px; font-weight:800; }
        .activity-empty { display:flex; min-height:88px; align-items:center; gap:11px; color:#918b84; }
        .activity-empty i { display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; background:#f5f1eb; color:#bfa080; font-size:15px; flex:0 0 auto; }
        .activity-empty strong { display:block; margin-bottom:2px; color:#68635e; font-size:11px; }
        .bill-spotlight { position:relative; display:grid; grid-template-columns:auto minmax(0,1fr) auto; align-items:center; gap:18px; overflow:hidden; margin-bottom:22px; padding:22px 24px; border-radius:18px; background:linear-gradient(110deg,#ede1d4,#f8f4ee 65%,#fff); color:inherit; box-shadow:0 9px 28px rgba(72,57,42,.07); }
        .bill-spotlight::after { position:absolute; top:-75px; right:13%; width:180px; height:180px; border:1px solid rgba(180,112,52,.11); border-radius:50%; content:''; }
        .bill-spotlight:hover { color:inherit; box-shadow:0 13px 32px rgba(72,57,42,.11); }
        .bill-icon { position:relative; z-index:1; display:flex; align-items:center; justify-content:center; width:52px; height:52px; border-radius:15px; background:#fff; color:#ae6c30; font-size:21px; box-shadow:0 7px 18px rgba(105,77,51,.08); }
        .bill-copy { position:relative; z-index:1; }
        .bill-copy small { display:block; margin-bottom:3px; color:#ad7136; font-size:9px; font-weight:800; letter-spacing:.07em; text-transform:uppercase; }
        .bill-copy h3 { margin:0 0 4px; color:var(--portal-ink); font-size:18px; }
        .bill-copy p { margin:0; color:#81786f; font-size:10px; }
        .bill-balance { position:relative; z-index:1; text-align:right; }
        .bill-balance small { display:block; margin-bottom:3px; color:#8b8177; font-size:9px; font-weight:700; text-transform:uppercase; }
        .bill-balance strong { display:block; color:#2c302e; font:700 20px/1.2 "Lora",serif; }
        .bill-balance span { display:inline-flex; align-items:center; gap:6px; margin-top:7px; color:#a76528; font-size:9px; font-weight:800; text-transform:uppercase; }
        .portal-security { display:flex; align-items:center; justify-content:space-between; gap:20px; padding:16px 18px; border:1px solid #e6e1da; border-radius:15px; background:#fff; }
        .security-copy { display:flex; align-items:center; gap:11px; min-width:0; }
        .security-icon { display:flex; align-items:center; justify-content:center; width:39px; height:39px; border-radius:12px; background:#edf5f0; color:#548069; font-size:15px; flex:0 0 auto; }
        .security-copy strong { display:block; margin-bottom:2px; color:#55514d; font-size:11px; }
        .security-copy small { display:block; color:#908a84; font-size:9px; line-height:1.5; }
        .portal-exit { display:inline-flex; align-items:center; gap:7px; padding:9px 12px; border:1px solid #ded7cf; border-radius:10px; background:#fff; color:#77716b; cursor:pointer; font-size:9px; font-weight:800; text-transform:uppercase; transition:.17s ease; }
        .portal-exit:hover { border-color:#c8766e; color:#b2534b; }
        @media (max-width:991.98px) { .portal-hero-content { grid-template-columns:1fr; } .portal-overview { grid-template-columns:repeat(2,minmax(0,1fr)); } .portal-action-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } .portal-action:last-child { grid-column:1/-1; min-height:205px; } .activity-grid { grid-template-columns:1fr; } }
        @media (max-width:575.98px) { .room-portal { padding-top:38px; } .portal-hero { padding:24px 20px; border-radius:18px; } .portal-hero h1 { font-size:28px; } .stay-card { padding:17px; } .portal-overview { gap:8px; } .portal-overview-card { gap:9px; padding:12px 10px; } .overview-icon { width:35px; height:35px; border-radius:10px; font-size:14px; } .portal-overview-card strong { font-size:13px; } .portal-action-grid { grid-template-columns:1fr; } .portal-action,.portal-action:last-child { grid-column:auto; min-height:215px; } .portal-section-heading { align-items:flex-start; flex-direction:column; gap:5px; } .bill-spotlight { grid-template-columns:auto minmax(0,1fr); padding:18px; } .bill-balance { grid-column:1/-1; padding-top:13px; border-top:1px solid rgba(148,112,77,.15); text-align:left; } .portal-security { align-items:flex-start; flex-direction:column; } .portal-exit { width:100%; justify-content:center; } }
    </style>
@endpush

@section('content')
    @php
        $stay = $access->stay;
        $reservation = $stay->reservation;
        $totalActive = $portalSummary['active_food_orders'] + $portalSummary['active_service_orders'] + $portalSummary['active_requests'];
    @endphp

    <section id="room-service-portal" class="room-portal">
        <div class="container">
            <header class="portal-hero">
                <div class="portal-hero-content">
                    <div>
                        <span class="portal-room-pill"><i class="fa fa-bed"></i>In-Stay Portal &middot; Kamar {{ $access->room->room_number }}</span>
                        <h1>Selamat datang, {{ $stay->guest_name }}</h1>
                        <p class="portal-hero-copy">Nikmati layanan Candra Resort langsung dari kamar. Pesan makanan, atur layanan, minta bantuan, dan pantau tagihan dalam satu tempat.</p>
                        <div class="portal-room-meta">
                            <span class="portal-meta-chip"><i class="fa fa-home"></i>{{ $access->room->roomType?->name ?? 'Kamar Resort' }}</span>
                            @if ($access->room->floor)<span class="portal-meta-chip"><i class="fa fa-building-o"></i>Lantai {{ $access->room->floor }}</span>@endif
                            <span class="portal-meta-chip"><i class="fa fa-shield"></i>Akses terverifikasi</span>
                        </div>
                    </div>
                    <div class="stay-card">
                        <div class="stay-card-heading"><span>Informasi masa inap</span><span class="stay-active">Sedang menginap</span></div>
                        <div class="stay-date-grid">
                            <div class="stay-date"><small>Check-in</small><strong>{{ $stay->check_in_at?->translatedFormat('d M Y') ?? '-' }}</strong></div>
                            <i class="fa fa-long-arrow-right stay-date-arrow"></i>
                            <div class="stay-date is-right"><small>Rencana check-out</small><strong>{{ $reservation?->check_out_date?->translatedFormat('d M Y') ?? '-' }}</strong></div>
                        </div>
                        <div class="stay-card-footer"><i class="fa fa-clock-o"></i><span>Akses portal tetap aktif selama masa inap belum diselesaikan oleh Receptionist.</span></div>
                    </div>
                </div>
            </header>

            <div class="portal-overview" aria-label="Ringkasan aktivitas kamar">
                <div class="portal-overview-card"><span class="overview-icon"><i class="fa fa-cutlery"></i></span><div><small>Pesanan makanan aktif</small><strong>{{ $portalSummary['active_food_orders'] }}</strong></div></div>
                <div class="portal-overview-card"><span class="overview-icon"><i class="fa fa-bell"></i></span><div><small>Layanan aktif</small><strong>{{ $portalSummary['active_service_orders'] }}</strong></div></div>
                <div class="portal-overview-card"><span class="overview-icon"><i class="fa fa-comments"></i></span><div><small>Permintaan aktif</small><strong>{{ $portalSummary['active_requests'] }}</strong></div></div>
                <div class="portal-overview-card"><span class="overview-icon"><i class="fa fa-file-text-o"></i></span><div><small>Saldo berjalan</small><strong>Rp{{ number_format($portalSummary['balance'], 0, ',', '.') }}</strong></div></div>
            </div>

            <section aria-labelledby="portal-services-title">
                <div class="portal-section-heading"><div><span class="portal-eyebrow">Layanan untuk Anda</span><h2 id="portal-services-title">Apa yang Anda butuhkan?</h2><p>Pilih kebutuhan Anda dan kami akan mengantarkannya langsung ke kamar.</p></div></div>
                <div class="portal-action-grid">
                    <a href="{{ route('room-service.food.index') }}" class="portal-action is-featured">
                        <span class="portal-action-tag">Pesan cepat</span>
                        <div class="portal-action-body"><span class="portal-action-icon"><i class="fa fa-cutlery"></i></span><h3>Makanan &amp; Minuman</h3><p>Pilih menu favorit, atur jumlah dengan mudah, lalu kirim pesanan langsung ke dapur resort.</p><span class="portal-action-link">Buka menu <i class="fa fa-arrow-right"></i></span></div>
                    </a>
                    <a href="{{ route('room-service.services.index') }}" class="portal-action">
                        <span class="portal-action-tag">Wellness &amp; lainnya</span>
                        <div class="portal-action-body"><span class="portal-action-icon"><i class="fa fa-bell"></i></span><h3>Layanan Hotel</h3><p>Pesan massage, spa, laundry, transportasi, dan layanan tambahan sesuai waktu Anda.</p><span class="portal-action-link">Lihat layanan <i class="fa fa-arrow-right"></i></span></div>
                    </a>
                    <a href="{{ route('room-service.requests.index') }}" class="portal-action">
                        <span class="portal-action-tag">Bantuan kamar</span>
                        <div class="portal-action-body"><span class="portal-action-icon"><i class="fa fa-comments"></i></span><h3>Housekeeping &amp; Bantuan</h3><p>Minta pembersihan kamar, handuk, air minum, amenity, atau bantuan Receptionist.</p><span class="portal-action-link">Buat permintaan <i class="fa fa-arrow-right"></i></span></div>
                    </a>
                </div>
            </section>

            <section aria-labelledby="portal-activity-title">
                <div class="portal-section-heading"><div><span class="portal-eyebrow">Status terbaru</span><h2 id="portal-activity-title">Pantau Aktivitas</h2><p>{{ $totalActive > 0 ? $totalActive.' aktivitas sedang ditangani oleh tim kami.' : 'Belum ada aktivitas yang sedang diproses.' }}</p></div></div>
                <div class="activity-grid">
                    <a href="{{ route('room-service.food.orders') }}" class="activity-card">
                        <div class="activity-card-top"><span class="activity-kind"><span class="activity-kind-icon"><i class="fa fa-cutlery"></i></span>Pesanan makanan</span><span class="activity-count">{{ $portalSummary['active_food_orders'] }} aktif</span></div>
                        <div class="activity-body">
                            @if ($recentActivity['food'])
                                <h3>{{ $recentActivity['food']->order_code }}</h3><p>{{ (float) $recentActivity['food']->items->sum('quantity') }} item &middot; {{ $recentActivity['food']->ordered_at?->diffForHumans() ?? $recentActivity['food']->created_at->diffForHumans() }}</p><div class="activity-status"><span class="badge badge-{{ $recentActivity['food']->status->badgeClass() }}">{{ $recentActivity['food']->status->label() }}</span><span>Lihat semua <i class="fa fa-arrow-right"></i></span></div>
                            @else
                                <div class="activity-empty"><i class="fa fa-check"></i><div><strong>Belum ada pesanan</strong><span>Pesanan makanan akan tampil di sini.</span></div></div>
                            @endif
                        </div>
                    </a>
                    <a href="{{ route('room-service.services.orders') }}" class="activity-card">
                        <div class="activity-card-top"><span class="activity-kind"><span class="activity-kind-icon"><i class="fa fa-bell"></i></span>Layanan hotel</span><span class="activity-count">{{ $portalSummary['active_service_orders'] }} aktif</span></div>
                        <div class="activity-body">
                            @if ($recentActivity['service'])
                                <h3>{{ $recentActivity['service']->service?->name ?? 'Layanan hotel' }}</h3><p>{{ $recentActivity['service']->order_code }} &middot; {{ $recentActivity['service']->scheduled_at?->translatedFormat('d M, H:i') ?? $recentActivity['service']->created_at->diffForHumans() }}</p><div class="activity-status"><span class="badge badge-{{ $recentActivity['service']->status->badgeClass() }}">{{ $recentActivity['service']->status->label() }}</span><span>Lihat semua <i class="fa fa-arrow-right"></i></span></div>
                            @else
                                <div class="activity-empty"><i class="fa fa-check"></i><div><strong>Belum ada layanan</strong><span>Pesanan layanan akan tampil di sini.</span></div></div>
                            @endif
                        </div>
                    </a>
                    <a href="{{ route('room-service.requests.index') }}" class="activity-card">
                        <div class="activity-card-top"><span class="activity-kind"><span class="activity-kind-icon"><i class="fa fa-comments"></i></span>Bantuan kamar</span><span class="activity-count">{{ $portalSummary['active_requests'] }} aktif</span></div>
                        <div class="activity-body">
                            @if ($recentActivity['request'])
                                <h3>{{ $recentActivity['request']->title }}</h3><p>{{ $recentActivity['request']->request_code }} &middot; {{ $recentActivity['request']->requested_at?->diffForHumans() ?? $recentActivity['request']->created_at->diffForHumans() }}</p><div class="activity-status"><span class="badge badge-{{ $recentActivity['request']->status->badgeClass() }}">{{ $recentActivity['request']->status->label() }}</span><span>Lihat riwayat <i class="fa fa-arrow-right"></i></span></div>
                            @else
                                <div class="activity-empty"><i class="fa fa-check"></i><div><strong>Belum ada permintaan</strong><span>Permintaan bantuan akan tampil di sini.</span></div></div>
                            @endif
                        </div>
                    </a>
                </div>
            </section>

            <a href="{{ route('room-service.bill.show') }}" class="bill-spotlight">
                <span class="bill-icon"><i class="fa fa-file-text-o"></i></span>
                <div class="bill-copy"><small>Folio masa inap</small><h3>Tagihan &amp; Pembayaran</h3><p>Lihat rincian kamar, pesanan makanan, layanan, dan pembayaran yang sudah tercatat.</p></div>
                <div class="bill-balance"><small>Saldo berjalan</small><strong>Rp{{ number_format($portalSummary['balance'], 0, ',', '.') }}</strong><span>Lihat rincian <i class="fa fa-arrow-right"></i></span></div>
            </a>

            <div class="portal-security">
                <div class="security-copy"><span class="security-icon"><i class="fa fa-shield"></i></span><div><strong>Sesi kamar Anda terlindungi</strong><small>Akses ini terikat pada masa inap aktif Kamar {{ $access->room->room_number }} dan otomatis dicabut setelah check-out.</small></div></div>
                <form method="POST" action="{{ route('room-service.logout') }}" data-confirm="Anda perlu memindai QR dan memverifikasi nomor telepon lagi untuk masuk." data-confirm-title="Tutup sesi layanan kamar?" data-confirm-button="Ya, Keluar dari Portal">
                    @csrf
                    <button type="submit" class="portal-exit"><i class="fa fa-sign-out"></i>Keluar dari portal</button>
                </form>
            </div>
        </div>
    </section>
@endsection
