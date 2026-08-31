@extends('layouts.guest')

@section('title', 'Tagihan Berjalan')

@push('styles')
    <style>
        .folio-page { --folio-accent:#dfa974; --folio-ink:#20252a; --folio-muted:#7d817f; --folio-border:#ebe7e1; min-height:80vh; padding:62px 0 90px; background:#f7f6f3; }
        .folio-hero { position:relative; overflow:hidden; margin-bottom:24px; padding:30px 34px; border-radius:22px; background:linear-gradient(125deg,#1e2428 0%,#30383d 62%,#4c4338 100%); box-shadow:0 18px 38px rgba(26,29,31,.14); color:#fff; }
        .folio-hero::after { position:absolute; top:-95px; right:-45px; width:250px; height:250px; border:1px solid rgba(223,169,116,.22); border-radius:50%; box-shadow:0 0 0 35px rgba(223,169,116,.04),0 0 0 70px rgba(223,169,116,.025); content:''; }
        .folio-hero-content { position:relative; z-index:1; }
        .folio-room-pill { display:inline-flex; align-items:center; gap:8px; padding:7px 12px; border:1px solid rgba(255,255,255,.16); border-radius:999px; background:rgba(255,255,255,.09); color:#f2cfaa; font-size:12px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; }
        .folio-hero h1 { margin:13px 0 7px; color:#fff; font-size:34px; }
        .folio-hero p { max-width:620px; margin:0; color:rgba(255,255,255,.68); }
        .folio-hero-link { position:relative; z-index:1; display:inline-flex; align-items:center; justify-content:center; gap:7px; min-height:42px; padding:10px 14px; border:1px solid rgba(255,255,255,.16); border-radius:11px; background:rgba(255,255,255,.08); color:#fff; font-size:12px; font-weight:700; transition:.2s ease; }
        .folio-hero-link:hover { border-color:#fff; background:#fff; color:#272e32; }
        .folio-summary-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:14px; margin-bottom:24px; }
        .folio-stat { display:flex; align-items:center; gap:13px; padding:18px; border:1px solid var(--folio-border); border-radius:16px; background:#fff; box-shadow:0 7px 22px rgba(32,34,35,.045); }
        .folio-stat-icon { display:flex; align-items:center; justify-content:center; width:45px; height:45px; border-radius:14px; background:#fff1e2; color:#b57437; font-size:18px; flex:0 0 auto; }
        .folio-stat.is-paid .folio-stat-icon { background:#e9f6ef; color:#428060; }
        .folio-stat.is-balance .folio-stat-icon { background:#fff2e4; color:#b96835; }
        .folio-stat small { display:block; margin-bottom:3px; color:#96918b; font-size:10px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; }
        .folio-stat strong { display:block; color:var(--folio-ink); font:700 21px/1.2 "Lora",serif; }
        .folio-panel { overflow:hidden; border:1px solid var(--folio-border); border-radius:20px; background:#fff; box-shadow:0 9px 30px rgba(32,34,35,.055); }
        .folio-panel + .folio-panel { margin-top:18px; }
        .folio-panel-header { display:flex; align-items:center; justify-content:space-between; gap:15px; padding:20px 22px; border-bottom:1px solid #eeeae5; }
        .folio-panel-eyebrow { display:block; margin-bottom:3px; color:#ad7136; font-size:10px; font-weight:800; letter-spacing:.08em; text-transform:uppercase; }
        .folio-panel-header h2 { margin:0; color:var(--folio-ink); font-size:22px; }
        .folio-count { padding:6px 10px; border-radius:999px; background:#f3efe9; color:#777169; font-size:10px; font-weight:700; white-space:nowrap; }
        .folio-category-list { display:flex; gap:8px; overflow-x:auto; padding:14px 22px; border-bottom:1px solid #f0ede8; scrollbar-width:thin; }
        .folio-category { display:inline-flex; align-items:center; gap:7px; padding:8px 10px; border-radius:10px; background:#f7f4ef; color:#736d66; font-size:10px; font-weight:700; white-space:nowrap; }
        .folio-category i { color:#b47840; }
        .folio-items { padding:2px 22px; }
        .folio-item { display:grid; grid-template-columns:auto minmax(0,1fr) auto; align-items:center; gap:13px; padding:17px 0; border-bottom:1px solid #f0ede8; }
        .folio-item:last-child { border-bottom:0; }
        .folio-item-icon { display:flex; align-items:center; justify-content:center; width:44px; height:44px; border-radius:13px; background:#fff1e2; color:#b57437; font-size:17px; }
        .folio-item-detail { min-width:0; }
        .folio-item-detail strong { display:block; margin-bottom:4px; overflow:hidden; color:var(--folio-ink); font-size:13px; text-overflow:ellipsis; white-space:nowrap; }
        .folio-item-detail small { display:flex; flex-wrap:wrap; gap:3px 6px; color:#918b84; font-size:10px; line-height:1.5; }
        .folio-item-type { color:#ad7136; font-weight:800; text-transform:capitalize; }
        .folio-item-amount { color:var(--folio-ink); font-size:13px; font-weight:800; white-space:nowrap; }
        .folio-empty { padding:48px 22px; color:#908a84; text-align:center; }
        .folio-empty i { display:block; margin-bottom:10px; color:#c6a27d; font-size:30px; }
        .folio-panel-footer { padding:14px 22px; border-top:1px solid #eeeae5; }
        .payment-list { padding:2px 22px; }
        .payment-item { display:grid; grid-template-columns:auto minmax(0,1fr) auto; align-items:center; gap:13px; padding:16px 0; border-bottom:1px solid #f0ede8; }
        .payment-item:last-child { border-bottom:0; }
        .payment-icon { display:flex; align-items:center; justify-content:center; width:42px; height:42px; border-radius:13px; background:#eaf6ef; color:#438060; font-size:16px; }
        .payment-detail { min-width:0; }
        .payment-detail strong { display:block; margin-bottom:3px; color:var(--folio-ink); font-size:12px; }
        .payment-detail small { display:block; overflow:hidden; color:#918b84; font-size:10px; text-overflow:ellipsis; white-space:nowrap; }
        .payment-amount { text-align:right; }
        .payment-amount strong { display:block; margin-bottom:4px; color:#41805f; font-size:13px; }
        .payment-amount .badge { font-size:9px; }
        .folio-ledger { position:sticky; top:24px; overflow:hidden; border-radius:20px; background:linear-gradient(145deg,#20262a,#343c40 68%,#4c4338); box-shadow:0 16px 38px rgba(27,31,33,.2); color:#fff; }
        .folio-ledger-top { padding:23px 23px 18px; border-bottom:1px solid rgba(255,255,255,.1); }
        .folio-ledger-label { display:flex; align-items:center; justify-content:space-between; gap:10px; margin-bottom:6px; color:rgba(255,255,255,.58); font-size:9px; font-weight:700; letter-spacing:.07em; text-transform:uppercase; }
        .folio-live-status { display:inline-flex; align-items:center; gap:5px; padding:5px 8px; border-radius:999px; background:rgba(101,174,132,.17); color:#a8dfbf; font-size:9px; }
        .folio-live-status::before { width:6px; height:6px; border-radius:50%; background:#72c294; content:''; }
        .folio-ledger h3 { margin:0; overflow:hidden; color:#fff; font-size:19px; text-overflow:ellipsis; white-space:nowrap; }
        .folio-ledger-body { padding:20px 23px 23px; }
        .folio-progress-copy { display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; color:rgba(255,255,255,.62); font-size:10px; }
        .folio-progress { height:7px; overflow:hidden; margin-bottom:21px; border-radius:999px; background:rgba(255,255,255,.12); }
        .folio-progress span { display:block; height:100%; border-radius:inherit; background:linear-gradient(90deg,#dfa974,#f0c79f); }
        .ledger-row { display:flex; align-items:center; justify-content:space-between; gap:14px; padding:7px 0; color:rgba(255,255,255,.66); font-size:11px; }
        .ledger-row strong { color:#fff; font-size:12px; }
        .ledger-total { display:flex; align-items:flex-end; justify-content:space-between; gap:12px; margin-top:14px; padding-top:18px; border-top:1px solid rgba(255,255,255,.12); }
        .ledger-total span { color:#f2d0ad; font-size:11px; font-weight:700; }
        .ledger-total strong { color:#fff; font:700 23px/1.2 "Lora",serif; text-align:right; }
        .ledger-note { display:flex; gap:9px; margin-top:20px; padding:12px; border-radius:11px; background:rgba(255,255,255,.07); color:rgba(255,255,255,.62); font-size:10px; line-height:1.55; }
        .ledger-note i { margin-top:2px; color:#edc49c; }
        .folio-help { margin-top:14px; padding:16px 18px; border:1px solid #e8e2db; border-radius:15px; background:#fff; color:#7c7771; font-size:10px; line-height:1.55; text-align:center; }
        .folio-help i { color:#b9793e; }
        .folio-unavailable { padding:65px 25px; border:1px dashed #ddd5cb; border-radius:20px; background:#fff; color:#8e8882; text-align:center; }
        .folio-unavailable-icon { display:flex; align-items:center; justify-content:center; width:70px; height:70px; margin:0 auto 15px; border-radius:21px; background:#f5f1eb; color:#bd9369; font-size:28px; }
        .folio-unavailable h2 { margin:0 0 8px; color:var(--folio-ink); font-size:24px; }
        @media (max-width:991.98px) { .folio-ledger { position:static; margin-top:18px; } }
        @media (max-width:767.98px) { .folio-summary-grid { grid-template-columns:1fr; gap:9px; } }
        @media (max-width:575.98px) { .folio-page { padding-top:38px; } .folio-hero { padding:24px 20px; border-radius:17px; } .folio-hero h1 { font-size:27px; } .folio-hero-link { width:100%; margin-top:18px; } .folio-stat { padding:14px; } .folio-stat strong { font-size:18px; } .folio-panel-header,.folio-category-list,.folio-items,.payment-list,.folio-panel-footer { padding-right:16px; padding-left:16px; } .folio-panel-header { align-items:flex-start; } .folio-item,.payment-item { align-items:flex-start; gap:10px; } .folio-item-icon,.payment-icon { width:38px; height:38px; border-radius:11px; } .folio-item-detail strong { white-space:normal; } .folio-item-amount,.payment-amount strong { font-size:11px; } }
    </style>
@endpush

@section('content')
    @php
        $categoryDetails = [
            'room' => ['Kamar', 'fa-bed'],
            'food' => ['Makanan & Minuman', 'fa-cutlery'],
            'service' => ['Layanan Hotel', 'fa-bell'],
        ];
        $paidPercentage = $folio && (float) $folio->total_amount > 0
            ? min(100, round(((float) $folio->paid_amount / (float) $folio->total_amount) * 100))
            : 0;
    @endphp

    <section id="folio-dashboard" class="folio-page">
        <div class="container">
            <div class="folio-hero">
                <div class="folio-hero-content d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <div>
                        <span class="folio-room-pill"><i class="fa fa-bed"></i>Kamar {{ $access->room->room_number }}</span>
                        <h1>Tagihan Berjalan</h1>
                        <p>Pantau seluruh biaya kamar, pesanan, layanan, dan pembayaran selama masa menginap Anda.</p>
                    </div>
                    <a href="{{ route('room-service.home') }}" class="folio-hero-link"><i class="fa fa-th-large"></i>Portal Kamar</a>
                </div>
            </div>

            @if ($folio)
                <div class="folio-summary-grid">
                    <div class="folio-stat"><span class="folio-stat-icon"><i class="fa fa-file-text-o"></i></span><div><small>Total tagihan</small><strong>Rp{{ number_format((float) $folio->total_amount, 0, ',', '.') }}</strong></div></div>
                    <div class="folio-stat is-paid"><span class="folio-stat-icon"><i class="fa fa-check"></i></span><div><small>Sudah dibayar</small><strong>Rp{{ number_format((float) $folio->paid_amount, 0, ',', '.') }}</strong></div></div>
                    <div class="folio-stat is-balance"><span class="folio-stat-icon"><i class="fa fa-credit-card"></i></span><div><small>Sisa tagihan</small><strong>Rp{{ number_format((float) $folio->balance_amount, 0, ',', '.') }}</strong></div></div>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <section class="folio-panel">
                            <header class="folio-panel-header"><div><span class="folio-panel-eyebrow">Transaksi masa inap</span><h2>Rincian Tagihan</h2></div><span class="folio-count">{{ $items->total() }} transaksi</span></header>
                            @if ($categoryTotals->isNotEmpty())
                                <div class="folio-category-list">
                                    @foreach ($categoryTotals as $type => $total)
                                        @php([$categoryLabel, $categoryIcon] = $categoryDetails[$type] ?? [str($type)->replace('_', ' ')->title(), 'fa-file-o'])
                                        <span class="folio-category"><i class="fa {{ $categoryIcon }}"></i>{{ $categoryLabel }}: Rp{{ number_format((float) $total, 0, ',', '.') }}</span>
                                    @endforeach
                                </div>
                            @endif
                            @if ($items->isNotEmpty())
                                <div class="folio-items">
                                    @foreach ($items as $item)
                                        @php([$itemTypeLabel, $itemTypeIcon] = $categoryDetails[$item->item_type] ?? [str($item->item_type)->replace('_', ' ')->title(), 'fa-file-o'])
                                        <div class="folio-item">
                                            <span class="folio-item-icon"><i class="fa {{ $itemTypeIcon }}"></i></span>
                                            <div class="folio-item-detail">
                                                <strong>{{ $item->description }}</strong>
                                                <small><span class="folio-item-type">{{ $itemTypeLabel }}</span><span>&middot;</span><span>{{ $item->posted_at?->translatedFormat('d M Y, H:i') ?? '-' }}</span><span>&middot;</span><span>{{ (float) $item->quantity }}&times; Rp{{ number_format((float) $item->unit_price, 0, ',', '.') }}</span></small>
                                            </div>
                                            <strong class="folio-item-amount">Rp{{ number_format((float) $item->amount, 0, ',', '.') }}</strong>
                                        </div>
                                    @endforeach
                                </div>
                                @if ($items->hasPages())<footer class="folio-panel-footer">{{ $items->links() }}</footer>@endif
                            @else
                                <div class="folio-empty"><i class="fa fa-file-text-o"></i><strong>Belum ada rincian biaya</strong><p class="mb-0 mt-1">Transaksi yang telah diposting akan muncul di sini.</p></div>
                            @endif
                        </section>

                        <section class="folio-panel">
                            <header class="folio-panel-header"><div><span class="folio-panel-eyebrow">Dana yang diterima</span><h2>Riwayat Pembayaran</h2></div><span class="folio-count">{{ $payments->total() }} pembayaran</span></header>
                            @if ($payments->isNotEmpty())
                                <div class="payment-list">
                                    @foreach ($payments as $payment)
                                        <div class="payment-item">
                                            <span class="payment-icon"><i class="fa fa-credit-card"></i></span>
                                            <div class="payment-detail"><strong>{{ $payment->method?->name ?? 'Metode pembayaran' }}</strong><small>{{ $payment->payment_code }} &middot; {{ $payment->paid_at?->translatedFormat('d M Y, H:i') ?? $payment->created_at->translatedFormat('d M Y, H:i') }}</small></div>
                                            <div class="payment-amount"><strong>Rp{{ number_format((float) $payment->amount, 0, ',', '.') }}</strong><span class="badge badge-{{ $payment->status->badgeClass() }}">{{ $payment->status->label() }}</span></div>
                                        </div>
                                    @endforeach
                                </div>
                                @if ($payments->hasPages())<footer class="folio-panel-footer">{{ $payments->links() }}</footer>@endif
                            @else
                                <div class="folio-empty"><i class="fa fa-credit-card"></i><strong>Belum ada pembayaran</strong><p class="mb-0 mt-1">Pembayaran yang berhasil dicatat akan tampil di bagian ini.</p></div>
                            @endif
                        </section>
                    </div>

                    <div class="col-lg-4">
                        <aside class="folio-ledger">
                            <div class="folio-ledger-top"><div class="folio-ledger-label"><span>Nomor folio</span><span class="folio-live-status">{{ str($folio->status)->replace('_', ' ')->title() }}</span></div><h3>{{ $folio->folio_number }}</h3></div>
                            <div class="folio-ledger-body">
                                <div class="folio-progress-copy"><span>Progres pembayaran</span><strong>{{ $paidPercentage }}%</strong></div>
                                <div class="folio-progress" role="progressbar" aria-valuenow="{{ $paidPercentage }}" aria-valuemin="0" aria-valuemax="100"><span style="width:{{ $paidPercentage }}%"></span></div>
                                <div class="ledger-row"><span>Subtotal</span><strong>Rp{{ number_format((float) $folio->subtotal, 0, ',', '.') }}</strong></div>
                                @if ((float) $folio->discount_amount > 0)<div class="ledger-row"><span>Diskon</span><strong>-Rp{{ number_format((float) $folio->discount_amount, 0, ',', '.') }}</strong></div>@endif
                                @if ((float) $folio->service_charge_amount > 0)<div class="ledger-row"><span>Biaya layanan</span><strong>Rp{{ number_format((float) $folio->service_charge_amount, 0, ',', '.') }}</strong></div>@endif
                                @if ((float) $folio->tax_amount > 0)<div class="ledger-row"><span>Pajak</span><strong>Rp{{ number_format((float) $folio->tax_amount, 0, ',', '.') }}</strong></div>@endif
                                <div class="ledger-row"><span>Sudah dibayar</span><strong>Rp{{ number_format((float) $folio->paid_amount, 0, ',', '.') }}</strong></div>
                                <div class="ledger-total"><span>Saldo Berjalan</span><strong>Rp{{ number_format((float) $folio->balance_amount, 0, ',', '.') }}</strong></div>
                                <div class="ledger-note"><i class="fa {{ (float) $folio->balance_amount > 0 ? 'fa-info-circle' : 'fa-check-circle' }}"></i><span>{{ (float) $folio->balance_amount > 0 ? 'Pembayaran akhir diproses oleh Receptionist saat atau sebelum check-out.' : 'Tagihan Anda saat ini telah lunas. Biaya baru tetap dapat ditambahkan selama masa inap aktif.' }}</span></div>
                            </div>
                        </aside>
                        <div class="folio-help"><i class="fa fa-phone mr-1"></i>Ada rincian yang ingin ditanyakan? Hubungi Receptionist melalui menu bantuan di Portal Kamar.</div>
                    </div>
                </div>
            @else
                <div class="folio-unavailable"><span class="folio-unavailable-icon"><i class="fa fa-file-text-o"></i></span><h2>Folio belum tersedia</h2><p>Tagihan masa inap belum dibuat. Silakan hubungi Receptionist jika Anda memerlukan bantuan.</p><a href="{{ route('room-service.home') }}" class="sona-button">Kembali ke Portal</a></div>
            @endif
        </div>
    </section>
@endsection
