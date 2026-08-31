@extends('layouts.guest')

@section('title', 'Layanan Hotel')

@push('styles')
    <style>
        .service-order-page {
            --service-accent: #dfa974;
            --service-accent-dark: #bd8044;
            --service-ink: #1f2328;
            --service-muted: #73777f;
            --service-border: #ece8e2;
            min-height: 80vh;
            padding: 62px 0 90px;
            background: #f7f6f3;
        }

        .service-order-hero {
            position: relative;
            overflow: hidden;
            margin-bottom: 30px;
            padding: 30px 34px;
            border-radius: 22px;
            background: linear-gradient(125deg, #1e2428 0%, #30383d 62%, #4c4338 100%);
            box-shadow: 0 18px 38px rgba(26, 29, 31, .14);
            color: #fff;
        }

        .service-order-hero::after {
            position: absolute;
            top: -90px;
            right: -45px;
            width: 250px;
            height: 250px;
            border: 1px solid rgba(223, 169, 116, .22);
            border-radius: 50%;
            box-shadow: 0 0 0 35px rgba(223, 169, 116, .04), 0 0 0 70px rgba(223, 169, 116, .025);
            content: '';
        }

        .service-hero-content { position: relative; z-index: 1; }
        .service-room-pill { display: inline-flex; align-items: center; gap: 8px; padding: 7px 12px; border: 1px solid rgba(255, 255, 255, .16); border-radius: 999px; background: rgba(255, 255, 255, .09); color: #f2cfaa; font-size: 12px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
        .service-order-hero h1 { margin: 13px 0 7px; color: #fff; font-size: 34px; }
        .service-order-hero p { margin: 0; color: rgba(255, 255, 255, .68); }
        .service-hero-link { display: inline-flex; align-items: center; gap: 7px; padding: 10px 14px; border: 1px solid rgba(255, 255, 255, .16); border-radius: 11px; background: rgba(255, 255, 255, .08); color: #fff; font-size: 13px; font-weight: 600; transition: .2s ease; }
        .service-hero-link:hover { border-color: #fff; background: #fff; color: #272e32; }

        .service-toolbar { margin-bottom: 26px; padding: 18px; border: 1px solid var(--service-border); border-radius: 17px; background: #fff; box-shadow: 0 7px 24px rgba(35, 36, 38, .045); }
        .service-search { position: relative; }
        .service-search i { position: absolute; top: 50%; left: 15px; z-index: 2; color: #9b948c; transform: translateY(-50%); }
        .service-search input { width: 100%; height: 45px; padding: 0 16px 0 42px; border: 1px solid #e8e3dc; border-radius: 12px; background: #faf9f7; color: var(--service-ink); outline: 0; transition: .2s ease; }
        .service-search input:focus { border-color: var(--service-accent); background: #fff; box-shadow: 0 0 0 3px rgba(223, 169, 116, .12); }
        .service-category-filters { display: flex; gap: 8px; overflow-x: auto; padding: 2px 1px 5px; scrollbar-width: thin; }
        .service-category-filter { flex: 0 0 auto; padding: 9px 14px; border: 1px solid #e8e3dc; border-radius: 999px; background: #fff; color: #706c67; cursor: pointer; font-size: 12px; font-weight: 700; transition: .18s ease; }
        .service-category-filter:hover, .service-category-filter.active { border-color: var(--service-accent); background: #fff6ec; color: #ad7136; }

        .service-section-heading { display: flex; align-items: center; gap: 11px; margin: 3px 0 18px; }
        .service-section-heading h3 { margin: 0; color: var(--service-ink); font-size: 23px; }
        .service-section-heading span { padding: 4px 8px; border-radius: 999px; background: #ebe7e1; color: #817b74; font-size: 11px; font-weight: 700; }
        .hotel-service-card { position: relative; height: 100%; overflow: hidden; border: 1px solid var(--service-border); border-radius: 18px; background: #fff; box-shadow: 0 8px 26px rgba(34, 34, 34, .05); cursor: pointer; transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease; }
        .hotel-service-card:hover { transform: translateY(-4px); border-color: #dfc8ae; box-shadow: 0 16px 32px rgba(34, 34, 34, .09); }
        .hotel-service-card.is-selected { border-color: #dca872; box-shadow: 0 12px 30px rgba(190, 132, 71, .15); }
        .service-image-wrap { position: relative; height: 188px; overflow: hidden; background: #eeeae5; }
        .service-image-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform .35s ease; }
        .hotel-service-card:hover .service-image-wrap img { transform: scale(1.035); }
        .service-image-placeholder { display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; background: linear-gradient(145deg, #f1ece6, #e7ded4); color: var(--service-accent-dark); font-size: 44px; }
        .service-category-badge { position: absolute; top: 12px; left: 12px; padding: 6px 9px; border-radius: 999px; background: rgba(255, 255, 255, .92); color: #645d55; font-size: 10px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; backdrop-filter: blur(6px); }
        .service-selected-check { position: absolute; top: 12px; right: 12px; display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 50%; background: var(--service-accent); color: #fff; opacity: 0; transform: scale(.75); transition: .2s ease; }
        .hotel-service-card.is-selected .service-selected-check { opacity: 1; transform: scale(1); }
        .service-card-body { display: flex; flex-direction: column; min-height: 245px; padding: 20px; }
        .service-card-title { margin: 0 0 7px; color: var(--service-ink); font: 700 19px/1.35 "Lora", serif; }
        .service-card-description { display: -webkit-box; min-height: 43px; margin-bottom: 13px; overflow: hidden; color: var(--service-muted); font-size: 13px; line-height: 1.6; -webkit-box-orient: vertical; -webkit-line-clamp: 2; }
        .service-meta { display: flex; flex-wrap: wrap; gap: 7px; margin-bottom: 14px; }
        .service-meta-pill { display: inline-flex; align-items: center; gap: 5px; padding: 5px 8px; border-radius: 8px; background: #f5f2ee; color: #766f68; font-size: 10px; font-weight: 600; }
        .service-card-footer { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: auto; padding-top: 14px; border-top: 1px solid #f0ede8; }
        .service-card-price { color: var(--service-accent-dark); font-size: 17px; font-weight: 800; }
        .service-card-price small { display: block; margin-top: 2px; color: #918a82; font-size: 10px; font-weight: 600; }
        .select-service-button { min-width: 104px; height: 39px; border: 1px solid var(--service-accent); border-radius: 10px; background: #fff8f0; color: #ad7136; cursor: pointer; font-size: 12px; font-weight: 800; transition: .18s ease; }
        .select-service-button:hover, .hotel-service-card.is-selected .select-service-button { background: var(--service-accent); color: #fff; }

        .service-booking-panel { position: sticky; top: 24px; overflow: hidden; border: 1px solid var(--service-border); border-radius: 20px; background: #fff; box-shadow: 0 14px 38px rgba(32, 34, 35, .09); }
        .booking-panel-header { display: flex; align-items: center; gap: 11px; padding: 20px 22px; border-bottom: 1px solid #eeeae5; }
        .booking-panel-icon { display: flex; align-items: center; justify-content: center; width: 41px; height: 41px; border-radius: 12px; background: #fff2e4; color: #b67639; font-size: 18px; }
        .booking-panel-header h4 { margin: 0; color: var(--service-ink); font-size: 19px; }
        .booking-empty { padding: 48px 24px; color: #918c86; text-align: center; }
        .booking-empty i { display: flex; align-items: center; justify-content: center; width: 65px; height: 65px; margin: 0 auto 14px; border-radius: 19px; background: #f5f2ed; color: #c2a98e; font-size: 27px; }
        .booking-empty strong { display: block; margin-bottom: 5px; color: #58544f; }
        .booking-form-body { padding: 21px 22px 23px; }
        .selected-service-summary { padding: 14px; margin-bottom: 18px; border-radius: 13px; background: #f7f4ef; }
        .selected-service-summary small { display: block; margin-bottom: 4px; color: #918a82; font-size: 10px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; }
        .selected-service-summary strong { display: block; color: var(--service-ink); line-height: 1.45; }
        .selected-service-summary span { display: block; margin-top: 4px; color: #a86c32; font-size: 12px; font-weight: 700; }
        .booking-field { margin-bottom: 16px; }
        .booking-label { display: flex; align-items: center; justify-content: space-between; margin-bottom: 7px; color: #514e4a; font-size: 12px; font-weight: 700; }
        .service-quantity-stepper { display: flex; align-items: center; width: 100%; overflow: hidden; border: 1px solid #e4dcd3; border-radius: 11px; background: #fff; }
        .service-quantity-button { width: 46px; height: 43px; border: 0; background: #fff7ee; color: #aa6d34; cursor: pointer; font-size: 19px; font-weight: 700; transition: .15s ease; }
        .service-quantity-button:hover { background: var(--service-accent); color: #fff; }
        .service-quantity-value { flex: 1; color: var(--service-ink); font-size: 14px; font-weight: 800; text-align: center; }
        .booking-input { width: 100%; min-height: 43px; padding: 9px 11px; border: 1px solid #e4dcd3; border-radius: 10px; background: #fff; color: #45423f; font-size: 12px; outline: 0; }
        textarea.booking-input { min-height: 78px; line-height: 1.5; resize: vertical; }
        .booking-input:focus { border-color: var(--service-accent); box-shadow: 0 0 0 3px rgba(223, 169, 116, .11); }
        .schedule-hint { display: block; margin-top: 6px; color: #98918a; font-size: 10px; line-height: 1.45; }
        .booking-total { display: flex; align-items: flex-end; justify-content: space-between; padding: 16px 0; margin-top: 3px; border-top: 1px solid #eee9e3; }
        .booking-total span { color: #7d7872; font-size: 12px; }
        .booking-total strong { color: var(--service-ink); font-size: 21px; }
        .book-service-submit { width: 100%; min-height: 48px; border: 0; border-radius: 12px; background: var(--service-accent); color: #fff; cursor: pointer; font-size: 12px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; transition: .18s ease; }
        .book-service-submit:hover { background: var(--service-accent-dark); transform: translateY(-1px); }
        .booking-folio-note { margin: 11px 0 0; color: #8d8882; font-size: 10px; line-height: 1.5; text-align: center; }
        .service-no-result { display: none; padding: 45px 20px; border: 1px dashed #dcd4cb; border-radius: 16px; background: #fff; color: #847f79; text-align: center; }
        .mobile-service-bar { display: none; }

        @media (max-width: 991.98px) {
            .service-booking-panel { position: static; margin-top: 12px; }
            .mobile-service-bar { position: fixed; right: 12px; bottom: 12px; left: 12px; z-index: 99; display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 14px; border: 1px solid rgba(255, 255, 255, .12); border-radius: 14px; background: #262d31; box-shadow: 0 12px 30px rgba(22, 26, 28, .3); color: #fff; transform: translateY(130%); transition: .25s ease; }
            .mobile-service-bar.show { transform: translateY(0); }
            .mobile-service-bar strong { display: block; max-width: 190px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .mobile-service-bar small { display: block; color: rgba(255, 255, 255, .63); }
            .mobile-service-bar button { flex: 0 0 auto; padding: 9px 12px; border: 0; border-radius: 9px; background: var(--service-accent); color: #fff; font-size: 11px; font-weight: 800; }
        }

        @media (max-width: 575.98px) {
            .service-order-page { padding-top: 38px; }
            .service-order-hero { padding: 24px 20px; border-radius: 17px; }
            .service-order-hero h1 { font-size: 27px; }
            .service-hero-actions { width: 100%; margin-top: 18px; }
            .service-hero-link { flex: 1; justify-content: center; }
            .service-toolbar { padding: 14px; }
        }
    </style>
@endpush

@section('content')
    @php
        $categoryLabels = [
            'massage' => 'Massage', 'spa' => 'Spa', 'laundry' => 'Laundry',
            'transport' => 'Transportasi', 'extra_bed' => 'Extra Bed', 'other' => 'Layanan Lainnya',
        ];
        $categoryIcons = [
            'massage' => 'fa-hand-paper-o', 'spa' => 'fa-leaf', 'laundry' => 'fa-shopping-basket',
            'transport' => 'fa-car', 'extra_bed' => 'fa-bed', 'other' => 'fa-bell',
        ];
        $unitLabels = [
            'per_order' => 'per pesanan', 'per_hour' => 'per jam', 'per_item' => 'per item', 'per_kg' => 'per kg',
        ];
        $quantityLabels = [
            'per_order' => 'Jumlah pesanan', 'per_hour' => 'Durasi layanan', 'per_item' => 'Jumlah item', 'per_kg' => 'Perkiraan berat',
        ];
    @endphp

    <section class="service-order-page">
        <div class="container">
            <div class="service-order-hero">
                <div class="service-hero-content d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <div>
                        <span class="service-room-pill"><i class="fa fa-bed"></i>Kamar {{ $access->room->room_number }}</span>
                        <h1>Layanan Hotel</h1>
                        <p>Nikmati perawatan, kebutuhan kamar, dan transportasi tanpa perlu meninggalkan kamar.</p>
                    </div>
                    <div class="d-flex flex-wrap service-hero-actions" style="gap:8px">
                        <a href="{{ route('room-service.services.orders') }}" class="service-hero-link"><i class="fa fa-list-alt"></i>Pesanan Saya</a>
                        <a href="{{ route('room-service.home') }}" class="service-hero-link"><i class="fa fa-th-large"></i>Portal Kamar</a>
                    </div>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <strong><i class="fa fa-exclamation-circle mr-1"></i>Pesanan belum dapat dikirim.</strong>
                    <ul class="mb-0 mt-2 pl-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            @if ($serviceItems->isNotEmpty())
                <div class="row">
                    <div class="col-lg-8">
                        <div class="service-toolbar">
                            <div class="service-search mb-3">
                                <i class="fa fa-search"></i>
                                <input id="service-search" type="search" placeholder="Cari massage, laundry, transportasi..." autocomplete="off">
                            </div>
                            <div class="service-category-filters" aria-label="Filter kategori layanan">
                                <button type="button" class="service-category-filter active" data-service-filter="all">Semua Layanan</button>
                                @foreach ($services as $category => $items)
                                    @php
                                        $categoryKey = $category ?: 'other';
                                    @endphp
                                    <button type="button" class="service-category-filter" data-service-filter="{{ $categoryKey }}">{{ $categoryLabels[$categoryKey] ?? str($categoryKey)->replace('_', ' ')->title() }}</button>
                                @endforeach
                            </div>
                        </div>

                        <div id="service-catalog">
                            @foreach ($services as $category => $items)
                                @php
                                    $categoryKey = $category ?: 'other';
                                    $categoryLabel = $categoryLabels[$categoryKey] ?? str($categoryKey)->replace('_', ' ')->title();
                                @endphp
                                <section class="service-category-section mb-5" data-service-section="{{ $categoryKey }}">
                                    <div class="service-section-heading"><h3>{{ $categoryLabel }}</h3><span>{{ $items->count() }} layanan</span></div>
                                    <div class="row">
                                        @foreach ($items as $service)
                                            @php
                                                $unitLabel = $unitLabels[$service->price_unit] ?? str($service->price_unit)->replace('_', ' ');
                                                $quantityLabel = $quantityLabels[$service->price_unit] ?? 'Jumlah';
                                                $quantityStep = in_array($service->price_unit, ['per_kg', 'per_hour'], true) ? 0.1 : 1;
                                                $isInitiallySelected = (string) old('hotel_service_id') === (string) $service->id;
                                            @endphp
                                            <div class="col-md-6 mb-4 service-grid-item">
                                                <article
                                                    class="hotel-service-card {{ $isInitiallySelected ? 'is-selected' : '' }}"
                                                    tabindex="0"
                                                    role="button"
                                                    aria-label="Pilih layanan {{ $service->name }}"
                                                    data-service-card
                                                    data-service-id="{{ $service->id }}"
                                                    data-service-name="{{ $service->name }}"
                                                    data-service-price="{{ (int) round((float) $service->price) }}"
                                                    data-service-unit="{{ $unitLabel }}"
                                                    data-service-quantity-label="{{ $quantityLabel }}"
                                                    data-service-step="{{ $quantityStep }}"
                                                    data-service-schedule="{{ $service->requires_schedule ? '1' : '0' }}"
                                                    data-service-category="{{ $categoryKey }}"
                                                >
                                                    <div class="service-image-wrap">
                                                        @if ($service->image_path)
                                                            <img src="{{ Storage::url($service->image_path) }}" alt="{{ $service->name }}" loading="lazy">
                                                        @else
                                                            <div class="service-image-placeholder"><i class="fa {{ $categoryIcons[$categoryKey] ?? 'fa-bell' }}"></i></div>
                                                        @endif
                                                        <span class="service-category-badge">{{ $categoryLabel }}</span>
                                                        <span class="service-selected-check"><i class="fa fa-check"></i></span>
                                                    </div>
                                                    <div class="service-card-body">
                                                        <h4 class="service-card-title">{{ $service->name }}</h4>
                                                        <p class="service-card-description">{{ $service->description ?: 'Layanan pilihan untuk melengkapi kenyamanan selama menginap.' }}</p>
                                                        <div class="service-meta">
                                                            @if ($service->duration_minutes)<span class="service-meta-pill"><i class="fa fa-clock-o"></i>{{ $service->duration_minutes }} menit</span>@endif
                                                            @if ($service->requires_schedule)<span class="service-meta-pill"><i class="fa fa-calendar-check-o"></i>Perlu jadwal</span>@else<span class="service-meta-pill"><i class="fa fa-bolt"></i>Dapat dipesan langsung</span>@endif
                                                        </div>
                                                        <div class="service-card-footer">
                                                            <span class="service-card-price">Rp{{ number_format((float) $service->price, 0, ',', '.') }}<small>{{ $unitLabel }}</small></span>
                                                            <button type="button" class="select-service-button" data-select-service="{{ $service->id }}">{{ $isInitiallySelected ? 'Dipilih' : 'Pilih' }}</button>
                                                        </div>
                                                    </div>
                                                </article>
                                            </div>
                                        @endforeach
                                    </div>
                                </section>
                            @endforeach

                            <div id="service-no-result" class="service-no-result">
                                <i class="fa fa-search mb-2" style="font-size:28px;color:#c1a07d"></i>
                                <h4 class="mb-1">Layanan tidak ditemukan</h4>
                                <p class="mb-0">Coba gunakan kata pencarian atau kategori lain.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <aside id="service-booking-panel" class="service-booking-panel">
                            <div class="booking-panel-header"><span class="booking-panel-icon"><i class="fa fa-calendar-check-o"></i></span><div><h4>Detail Pemesanan</h4><small class="text-muted">Untuk Kamar {{ $access->room->room_number }}</small></div></div>
                            <div id="service-booking-empty" class="booking-empty"><i class="fa fa-hand-pointer-o"></i><strong>Pilih layanan terlebih dahulu</strong><span>Tekan tombol Pilih pada layanan yang Anda butuhkan.</span></div>

                            <form
                                id="service-booking-form"
                                method="POST"
                                action="{{ route('room-service.services.store') }}"
                                data-initial-service="{{ old('hotel_service_id') }}"
                                data-initial-quantity="{{ old('quantity', 1) }}"
                                data-confirm="Pastikan jumlah, jadwal, dan catatan layanan sudah benar."
                                data-confirm-title="Kirim pesanan layanan?"
                                data-confirm-button="Ya, Pesan Layanan"
                                hidden
                            >
                                @csrf
                                <input id="selected-service-id" type="hidden" name="hotel_service_id" value="{{ old('hotel_service_id') }}">
                                <input id="service-quantity-input" type="hidden" name="quantity" value="{{ old('quantity', 1) }}">

                                <div class="booking-form-body">
                                    <div class="selected-service-summary"><small>Layanan terpilih</small><strong id="selected-service-name">-</strong><span id="selected-service-price">Rp0</span></div>
                                    <div class="booking-field">
                                        <label id="service-quantity-label" class="booking-label">Jumlah</label>
                                        <div class="service-quantity-stepper">
                                            <button id="service-quantity-minus" type="button" class="service-quantity-button" aria-label="Kurangi jumlah">&minus;</button>
                                            <span id="service-quantity-value" class="service-quantity-value">1</span>
                                            <button id="service-quantity-plus" type="button" class="service-quantity-button" aria-label="Tambah jumlah">+</button>
                                        </div>
                                    </div>
                                    <div id="service-schedule-field" class="booking-field" hidden>
                                        <label class="booking-label" for="service-scheduled-at">Jadwal layanan <span class="text-danger">Wajib</span></label>
                                        <input id="service-scheduled-at" type="datetime-local" min="{{ now()->addMinutes(15)->format('Y-m-d\TH:i') }}" name="scheduled_at" value="{{ old('scheduled_at') }}" class="booking-input">
                                        <small class="schedule-hint"><i class="fa fa-info-circle mr-1"></i>Pilih waktu minimal 15 menit dari sekarang. Receptionist akan mengonfirmasi ketersediaannya.</small>
                                    </div>
                                    <div class="booking-field">
                                        <label class="booking-label" for="service-notes">Catatan tambahan <span class="text-muted">Opsional</span></label>
                                        <textarea id="service-notes" name="notes" maxlength="1000" class="booking-input" placeholder="Contoh: untuk dua orang, jemput di lobby">{{ old('notes') }}</textarea>
                                    </div>
                                    <div class="booking-total"><span>Perkiraan total<br><small>Harga dikunci saat dipesan</small></span><strong id="service-booking-total">Rp0</strong></div>
                                    <button type="submit" class="book-service-submit"><i class="fa fa-paper-plane mr-1"></i>Pesan Layanan</button>
                                    <p class="booking-folio-note"><i class="fa fa-shield mr-1"></i>Biaya akan masuk ke folio setelah layanan diselesaikan Receptionist.</p>
                                </div>
                            </form>
                        </aside>
                    </div>
                </div>

                <div id="mobile-service-bar" class="mobile-service-bar">
                    <div><strong id="mobile-service-name">Layanan</strong><small id="mobile-service-total">Rp0</small></div>
                    <button id="continue-service-button" type="button"><i class="fa fa-calendar-check-o mr-1"></i>Lanjut</button>
                </div>
            @else
                <div class="auth-card text-center py-5">
                    <i class="fa fa-bell mb-3" style="font-size:42px;color:#dfa974"></i>
                    <h3>Belum ada layanan tersedia</h3>
                    <p class="text-muted">Silakan hubungi Receptionist untuk kebutuhan selama menginap.</p>
                    <a href="{{ route('room-service.home') }}" class="sona-button d-inline-block mt-2">Kembali ke Portal</a>
                </div>
            @endif
        </div>
    </section>
@endsection

@if ($serviceItems->isNotEmpty())
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const cards = Array.from(document.querySelectorAll('[data-service-card]'));
                const form = document.getElementById('service-booking-form');
                const emptyState = document.getElementById('service-booking-empty');
                const serviceIdInput = document.getElementById('selected-service-id');
                const quantityInput = document.getElementById('service-quantity-input');
                const quantityValue = document.getElementById('service-quantity-value');
                const quantityLabel = document.getElementById('service-quantity-label');
                const selectedName = document.getElementById('selected-service-name');
                const selectedPrice = document.getElementById('selected-service-price');
                const bookingTotal = document.getElementById('service-booking-total');
                const scheduleField = document.getElementById('service-schedule-field');
                const scheduleInput = document.getElementById('service-scheduled-at');
                const searchInput = document.getElementById('service-search');
                const filterButtons = Array.from(document.querySelectorAll('[data-service-filter]'));
                const noResult = document.getElementById('service-no-result');
                const mobileBar = document.getElementById('mobile-service-bar');
                const mobileName = document.getElementById('mobile-service-name');
                const mobileTotal = document.getElementById('mobile-service-total');
                let selectedCard = null;
                let activeCategory = 'all';

                const rupiah = new Intl.NumberFormat('id-ID', {
                    style: 'currency', currency: 'IDR', maximumFractionDigits: 0
                });
                const quantityFormatter = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 });

                function normaliseQuantity(value, step) {
                    const rounded = Math.round(Number(value) * 100) / 100;
                    return Math.max(step, Math.min(100, rounded || step));
                }

                function renderBooking() {
                    if (!selectedCard) return;
                    const price = Number(selectedCard.dataset.servicePrice);
                    const step = Number(selectedCard.dataset.serviceStep) || 1;
                    const quantity = normaliseQuantity(quantityInput.value, step);
                    const total = price * quantity;

                    quantityInput.value = quantity;
                    quantityValue.textContent = quantityFormatter.format(quantity);
                    bookingTotal.textContent = rupiah.format(total);
                    mobileTotal.textContent = rupiah.format(total);
                }

                function selectService(card, preserveQuantity) {
                    selectedCard = card;
                    cards.forEach((item) => {
                        const selected = item === card;
                        item.classList.toggle('is-selected', selected);
                        item.querySelector('.select-service-button').textContent = selected ? 'Dipilih' : 'Pilih';
                    });

                    const requiresSchedule = card.dataset.serviceSchedule === '1';
                    const step = Number(card.dataset.serviceStep) || 1;
                    serviceIdInput.value = card.dataset.serviceId;
                    if (!preserveQuantity) quantityInput.value = step < 1 ? 1 : step;
                    selectedName.textContent = card.dataset.serviceName;
                    selectedPrice.textContent = rupiah.format(Number(card.dataset.servicePrice)) + ' ' + card.dataset.serviceUnit;
                    quantityLabel.textContent = card.dataset.serviceQuantityLabel;
                    scheduleField.hidden = !requiresSchedule;
                    scheduleInput.required = requiresSchedule;
                    if (!requiresSchedule) scheduleInput.value = '';
                    emptyState.style.display = 'none';
                    form.hidden = false;
                    mobileName.textContent = card.dataset.serviceName;
                    mobileBar.classList.add('show');
                    renderBooking();
                }

                function changeQuantity(direction) {
                    if (!selectedCard) return;
                    const step = Number(selectedCard.dataset.serviceStep) || 1;
                    setTimeout(function () {
                        quantityInput.value = normaliseQuantity(Number(quantityInput.value) + (direction * step), step);
                        renderBooking();
                    }, 0);
                }

                function filterServices() {
                    const query = searchInput.value.trim().toLocaleLowerCase('id-ID');
                    let visibleCards = 0;

                    document.querySelectorAll('[data-service-section]').forEach((section) => {
                        let visibleInSection = 0;
                        section.querySelectorAll('[data-service-card]').forEach((card) => {
                            const matchesCategory = activeCategory === 'all' || card.dataset.serviceCategory === activeCategory;
                            const matchesSearch = !query || card.dataset.serviceName.toLocaleLowerCase('id-ID').includes(query);
                            const gridItem = card.closest('.service-grid-item');
                            const visible = matchesCategory && matchesSearch;
                            gridItem.style.display = visible ? '' : 'none';
                            if (visible) visibleInSection++;
                        });
                        section.style.display = visibleInSection ? '' : 'none';
                        visibleCards += visibleInSection;
                    });

                    noResult.style.display = visibleCards ? 'none' : 'block';
                }

                cards.forEach((card) => {
                    card.addEventListener('click', function (event) {
                        if (event.target.closest('a')) return;
                        selectService(card, selectedCard === card);
                    });
                    card.addEventListener('keydown', function (event) {
                        if (event.key === 'Enter' || event.key === ' ') {
                            event.preventDefault();
                            selectService(card, selectedCard === card);
                        }
                    });
                });

                document.getElementById('service-quantity-minus').addEventListener('click', () => changeQuantity(-1));
                document.getElementById('service-quantity-plus').addEventListener('click', () => changeQuantity(1));
                document.getElementById('continue-service-button').addEventListener('click', function () {
                    document.getElementById('service-booking-panel').scrollIntoView({ behavior: 'smooth', block: 'start' });
                });

                filterButtons.forEach((button) => {
                    button.addEventListener('click', function () {
                        activeCategory = button.dataset.serviceFilter;
                        filterButtons.forEach((item) => item.classList.toggle('active', item === button));
                        filterServices();
                    });
                });
                searchInput.addEventListener('input', filterServices);

                const initialCard = cards.find((card) => card.dataset.serviceId === form.dataset.initialService);
                if (initialCard) {
                    quantityInput.value = form.dataset.initialQuantity || 1;
                    selectService(initialCard, true);
                }

                filterServices();
            });
        </script>
    @endpush
@endif
