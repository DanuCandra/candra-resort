@extends('layouts.guest')

@section('title', 'Pesan Makanan & Minuman')

@push('styles')
    <style>
        .food-order-page {
            --food-accent: #dfa974;
            --food-accent-dark: #bf8447;
            --food-ink: #1f2328;
            --food-muted: #73777f;
            --food-surface: #fff;
            --food-border: #ece8e2;
            background: #f7f6f3;
            min-height: 80vh;
            padding: 62px 0 90px;
        }

        .food-order-hero {
            position: relative;
            overflow: hidden;
            margin-bottom: 30px;
            padding: 30px 34px;
            border-radius: 22px;
            background: linear-gradient(125deg, #1e2428 0%, #30383d 62%, #4c4338 100%);
            box-shadow: 0 18px 38px rgba(26, 29, 31, .14);
            color: #fff;
        }

        .food-order-hero::after {
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

        .food-order-hero-content { position: relative; z-index: 1; }
        .room-pill { display: inline-flex; align-items: center; gap: 8px; padding: 7px 12px; border: 1px solid rgba(255, 255, 255, .16); border-radius: 999px; background: rgba(255, 255, 255, .09); color: #f2cfaa; font-size: 12px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
        .food-order-hero h1 { margin: 13px 0 7px; color: #fff; font-size: 34px; }
        .food-order-hero p { margin: 0; color: rgba(255, 255, 255, .68); }
        .hero-link { display: inline-flex; align-items: center; gap: 7px; padding: 10px 14px; border: 1px solid rgba(255, 255, 255, .16); border-radius: 11px; background: rgba(255, 255, 255, .08); color: #fff; font-size: 13px; font-weight: 600; transition: .2s ease; }
        .hero-link:hover { border-color: #fff; background: #fff; color: #272e32; }

        .menu-toolbar { margin-bottom: 26px; padding: 18px; border: 1px solid var(--food-border); border-radius: 17px; background: #fff; box-shadow: 0 7px 24px rgba(35, 36, 38, .045); }
        .menu-search { position: relative; }
        .menu-search i { position: absolute; top: 50%; left: 15px; z-index: 2; color: #9b948c; transform: translateY(-50%); }
        .menu-search input { width: 100%; height: 45px; padding: 0 16px 0 42px; border: 1px solid #e8e3dc; border-radius: 12px; background: #faf9f7; color: var(--food-ink); outline: 0; transition: .2s ease; }
        .menu-search input:focus { border-color: var(--food-accent); background: #fff; box-shadow: 0 0 0 3px rgba(223, 169, 116, .12); }
        .category-filters { display: flex; gap: 8px; overflow-x: auto; padding: 2px 1px 5px; scrollbar-width: thin; }
        .category-filter { flex: 0 0 auto; padding: 9px 14px; border: 1px solid #e8e3dc; border-radius: 999px; background: #fff; color: #706c67; cursor: pointer; font-size: 12px; font-weight: 700; transition: .18s ease; }
        .category-filter:hover, .category-filter.active { border-color: var(--food-accent); background: #fff6ec; color: #ad7136; }

        .menu-section-title { display: flex; align-items: center; gap: 11px; margin: 3px 0 18px; }
        .menu-section-title h3 { margin: 0; color: var(--food-ink); font-size: 23px; }
        .menu-section-title span { padding: 4px 8px; border-radius: 999px; background: #ebe7e1; color: #817b74; font-size: 11px; font-weight: 700; }
        .food-menu-card { position: relative; height: 100%; overflow: hidden; border: 1px solid var(--food-border); border-radius: 18px; background: #fff; box-shadow: 0 8px 26px rgba(34, 34, 34, .05); transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease; }
        .food-menu-card:hover { transform: translateY(-4px); border-color: #dfc8ae; box-shadow: 0 16px 32px rgba(34, 34, 34, .09); }
        .food-menu-card.is-selected { border-color: #ddb486; box-shadow: 0 12px 30px rgba(190, 132, 71, .13); }
        .menu-image-wrap { position: relative; height: 178px; overflow: hidden; background: #eeeae5; }
        .menu-image-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform .35s ease; }
        .food-menu-card:hover .menu-image-wrap img { transform: scale(1.035); }
        .menu-image-placeholder { display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; background: linear-gradient(145deg, #f1ece6, #e7ded4); color: var(--food-accent-dark); font-size: 42px; }
        .menu-category-badge { position: absolute; top: 12px; left: 12px; padding: 6px 9px; border-radius: 999px; background: rgba(255, 255, 255, .92); color: #645d55; font-size: 10px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; backdrop-filter: blur(6px); }
        .selected-check { position: absolute; top: 12px; right: 12px; display: flex; align-items: center; justify-content: center; width: 31px; height: 31px; border-radius: 50%; background: var(--food-accent); color: #fff; opacity: 0; transform: scale(.75); transition: .2s ease; }
        .food-menu-card.is-selected .selected-check { opacity: 1; transform: scale(1); }
        .food-menu-body { display: flex; flex-direction: column; min-height: 223px; padding: 19px; }
        .food-menu-title { margin: 0 0 6px; color: var(--food-ink); font: 700 19px/1.35 "Lora", serif; }
        .food-menu-description { display: -webkit-box; min-height: 42px; margin-bottom: 12px; overflow: hidden; color: var(--food-muted); font-size: 13px; line-height: 1.6; -webkit-box-orient: vertical; -webkit-line-clamp: 2; }
        .prep-time { display: inline-flex; align-items: center; gap: 5px; color: #8c867f; font-size: 11px; }
        .menu-card-footer { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: auto; padding-top: 15px; border-top: 1px solid #f0ede8; }
        .menu-price { color: var(--food-accent-dark); font-size: 17px; font-weight: 800; white-space: nowrap; }
        .add-menu-button { min-width: 92px; height: 38px; border: 1px solid var(--food-accent); border-radius: 10px; background: #fff8f0; color: #ad7136; cursor: pointer; font-size: 12px; font-weight: 800; transition: .18s ease; }
        .add-menu-button:hover { background: var(--food-accent); color: #fff; }
        .quantity-stepper { display: inline-flex; align-items: center; overflow: hidden; border: 1px solid #dfc3a4; border-radius: 10px; background: #fff; }
        .quantity-stepper[hidden] { display: none !important; }
        .quantity-button { display: flex; align-items: center; justify-content: center; width: 35px; height: 36px; border: 0; background: #fff8f0; color: #a86c32; cursor: pointer; font-size: 18px; font-weight: 700; transition: .15s ease; }
        .quantity-button:hover { background: var(--food-accent); color: #fff; }
        .quantity-number { min-width: 32px; color: var(--food-ink); font-size: 13px; font-weight: 800; text-align: center; }

        .cart-panel { position: sticky; top: 24px; overflow: hidden; border: 1px solid var(--food-border); border-radius: 20px; background: #fff; box-shadow: 0 14px 38px rgba(32, 34, 35, .09); }
        .cart-header { display: flex; align-items: center; justify-content: space-between; padding: 20px 22px; border-bottom: 1px solid #eeeae5; }
        .cart-heading { display: flex; align-items: center; gap: 10px; }
        .cart-icon { display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 12px; background: #fff2e4; color: #b67639; font-size: 17px; }
        .cart-heading h4 { margin: 0; color: var(--food-ink); font-size: 19px; }
        .cart-count { min-width: 27px; padding: 4px 8px; border-radius: 999px; background: #2d3438; color: #fff; font-size: 11px; font-weight: 700; text-align: center; }
        .cart-items { max-height: 325px; overflow-y: auto; padding: 5px 20px; }
        .cart-empty { padding: 42px 20px; color: #96918b; text-align: center; }
        .cart-empty i { display: flex; align-items: center; justify-content: center; width: 62px; height: 62px; margin: 0 auto 13px; border-radius: 18px; background: #f5f2ed; color: #c2a98e; font-size: 25px; }
        .cart-empty strong { display: block; margin-bottom: 4px; color: #58544f; }
        .cart-item { padding: 15px 0; border-bottom: 1px solid #f0ede8; }
        .cart-item:last-child { border-bottom: 0; }
        .cart-item-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; }
        .cart-item-name { display: block; margin-bottom: 3px; color: var(--food-ink); font-size: 13px; font-weight: 700; line-height: 1.4; }
        .cart-item-subtotal { color: #a96d34; font-size: 12px; font-weight: 700; }
        .cart-mini-stepper { display: inline-flex; align-items: center; overflow: hidden; border: 1px solid #e5ddd4; border-radius: 8px; }
        .cart-mini-stepper button { width: 27px; height: 27px; border: 0; background: #f8f5f1; color: #796f65; cursor: pointer; font-weight: 700; }
        .cart-mini-stepper span { min-width: 27px; font-size: 11px; font-weight: 800; text-align: center; }
        .cart-note-input { width: 100%; height: 34px; margin-top: 9px; padding: 0 10px; border: 1px solid #ece5dd; border-radius: 8px; background: #faf9f7; color: #4d4a46; font-size: 11px; outline: 0; }
        .cart-note-input:focus { border-color: var(--food-accent); background: #fff; }
        .cart-checkout { padding: 19px 22px 22px; border-top: 1px solid #eeeae5; background: #fcfbf9; }
        .delivery-label { display: block; margin-bottom: 7px; color: #514e4a; font-size: 12px; font-weight: 700; }
        .delivery-notes { width: 100%; min-height: 72px; padding: 10px 12px; border: 1px solid #e7e0d8; border-radius: 10px; background: #fff; color: #45423f; font-size: 12px; line-height: 1.5; outline: 0; resize: vertical; }
        .delivery-notes:focus { border-color: var(--food-accent); box-shadow: 0 0 0 3px rgba(223, 169, 116, .11); }
        .cart-summary { display: flex; align-items: flex-end; justify-content: space-between; margin: 16px 0; }
        .cart-summary span { color: #7d7872; font-size: 12px; }
        .cart-summary strong { color: var(--food-ink); font-size: 21px; }
        .submit-order-button { width: 100%; min-height: 48px; border: 0; border-radius: 12px; background: var(--food-accent); color: #fff; cursor: pointer; font-size: 12px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; transition: .18s ease; }
        .submit-order-button:hover:not(:disabled) { background: var(--food-accent-dark); transform: translateY(-1px); }
        .submit-order-button:disabled { cursor: not-allowed; opacity: .48; }
        .folio-note { margin: 11px 0 0; color: #8d8882; font-size: 10px; line-height: 1.5; text-align: center; }
        .menu-no-result { display: none; padding: 45px 20px; border: 1px dashed #dcd4cb; border-radius: 16px; background: #fff; color: #847f79; text-align: center; }
        .mobile-cart-bar { display: none; }

        @media (max-width: 991.98px) {
            .cart-panel { position: static; margin-top: 12px; }
            .mobile-cart-bar { position: fixed; right: 12px; bottom: 12px; left: 12px; z-index: 99; display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; border: 1px solid rgba(255, 255, 255, .12); border-radius: 14px; background: #262d31; box-shadow: 0 12px 30px rgba(22, 26, 28, .3); color: #fff; transform: translateY(130%); transition: .25s ease; }
            .mobile-cart-bar.show { transform: translateY(0); }
            .mobile-cart-bar small { display: block; color: rgba(255, 255, 255, .63); }
            .mobile-cart-bar button { padding: 9px 12px; border: 0; border-radius: 9px; background: var(--food-accent); color: #fff; font-size: 11px; font-weight: 800; }
        }

        @media (max-width: 575.98px) {
            .food-order-page { padding-top: 38px; }
            .food-order-hero { padding: 24px 20px; border-radius: 17px; }
            .food-order-hero h1 { font-size: 27px; }
            .hero-actions { width: 100%; margin-top: 18px; }
            .hero-link { flex: 1; justify-content: center; }
            .menu-toolbar { padding: 14px; }
            .food-menu-body { min-height: 205px; }
        }
    </style>
@endpush

@section('content')
    <section class="food-order-page">
        <div class="container">
            <div class="food-order-hero">
                <div class="food-order-hero-content d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <div>
                        <span class="room-pill"><i class="fa fa-bed"></i>Kamar {{ $access->room->room_number }}</span>
                        <h1>Pesan Makanan &amp; Minuman</h1>
                        <p>Pilih menu favorit Anda, kami akan mengantarkannya langsung ke kamar.</p>
                    </div>
                    <div class="d-flex flex-wrap hero-actions" style="gap:8px">
                        <a href="{{ route('room-service.food.orders') }}" class="hero-link"><i class="fa fa-list-alt"></i>Pesanan Saya</a>
                        <a href="{{ route('room-service.home') }}" class="hero-link"><i class="fa fa-th-large"></i>Portal Kamar</a>
                    </div>
                </div>
            </div>

            @error('items')
                <div class="alert alert-danger mb-4"><i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}</div>
            @enderror

            @if ($menuItems->isNotEmpty())
                <form
                    id="food-order-form"
                    method="POST"
                    action="{{ route('room-service.food.store') }}"
                    data-confirm="Pastikan menu, jumlah, dan catatan pesanan sudah benar."
                    data-confirm-title="Kirim pesanan ke Receptionist?"
                    data-confirm-button="Ya, Kirim Pesanan"
                >
                    @csrf

                    <div class="row">
                        <div class="col-lg-8">
                            <div class="menu-toolbar">
                                <div class="menu-search mb-3">
                                    <i class="fa fa-search"></i>
                                    <input id="menu-search" type="search" placeholder="Cari makanan atau minuman..." autocomplete="off">
                                </div>
                                <div class="category-filters" aria-label="Filter kategori menu">
                                    <button type="button" class="category-filter active" data-category-filter="all">Semua Menu</button>
                                    @foreach ($categories as $category => $items)
                                        <button type="button" class="category-filter" data-category-filter="{{ Str::slug($category) }}">{{ $category }}</button>
                                    @endforeach
                                </div>
                            </div>

                            <div id="menu-catalog">
                                @foreach ($categories as $category => $items)
                                    @php
                                        $categorySlug = Str::slug($category);
                                    @endphp
                                    <section class="menu-category-section mb-5" data-category-section="{{ $categorySlug }}">
                                        <div class="menu-section-title"><h3>{{ $category }}</h3><span>{{ $items->count() }} menu</span></div>
                                        <div class="row">
                                            @foreach ($items as $item)
                                                @php
                                                    $oldQuantity = (int) old('items.'.$item->id.'.quantity', 0);
                                                    $oldNotes = old('items.'.$item->id.'.special_notes', '');
                                                @endphp
                                                <div class="col-md-6 mb-4 menu-grid-item">
                                                    <article
                                                        class="food-menu-card {{ $oldQuantity > 0 ? 'is-selected' : '' }}"
                                                        data-menu-card
                                                        data-menu-id="{{ $item->id }}"
                                                        data-menu-name="{{ $item->name }}"
                                                        data-menu-price="{{ (int) round((float) $item->price) }}"
                                                        data-menu-category="{{ $categorySlug }}"
                                                        data-menu-note="{{ $oldNotes }}"
                                                    >
                                                        <div class="menu-image-wrap">
                                                            @if ($item->image_path)
                                                                <img src="{{ Storage::url($item->image_path) }}" alt="{{ $item->name }}" loading="lazy">
                                                            @else
                                                                <div class="menu-image-placeholder"><i class="fa fa-cutlery"></i></div>
                                                            @endif
                                                            <span class="menu-category-badge">{{ $category }}</span>
                                                            <span class="selected-check"><i class="fa fa-check"></i></span>
                                                        </div>
                                                        <div class="food-menu-body">
                                                            <h4 class="food-menu-title">{{ $item->name }}</h4>
                                                            <p class="food-menu-description">{{ $item->description ?: 'Disiapkan segar khusus untuk menemani waktu santai Anda.' }}</p>
                                                            @if ($item->preparation_minutes)
                                                                <span class="prep-time"><i class="fa fa-clock-o"></i>Sekitar {{ $item->preparation_minutes }} menit</span>
                                                            @endif
                                                            <div class="menu-card-footer">
                                                                <span class="menu-price">Rp{{ number_format((float) $item->price, 0, ',', '.') }}</span>
                                                                <button type="button" class="add-menu-button" data-cart-action="increment" data-menu-id="{{ $item->id }}" {{ $oldQuantity > 0 ? 'hidden' : '' }}><i class="fa fa-plus mr-1"></i>Tambah</button>
                                                                <div class="quantity-stepper" data-card-stepper="{{ $item->id }}" {{ $oldQuantity === 0 ? 'hidden' : '' }}>
                                                                    <button type="button" class="quantity-button" data-cart-action="decrement" data-menu-id="{{ $item->id }}" aria-label="Kurangi {{ $item->name }}">&minus;</button>
                                                                    <span class="quantity-number" data-quantity-display="{{ $item->id }}">{{ $oldQuantity }}</span>
                                                                    <button type="button" class="quantity-button" data-cart-action="increment" data-menu-id="{{ $item->id }}" aria-label="Tambah {{ $item->name }}">+</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="items[{{ $item->id }}][quantity]" value="{{ $oldQuantity }}" data-quantity-input="{{ $item->id }}">
                                                    </article>
                                                </div>
                                            @endforeach
                                        </div>
                                    </section>
                                @endforeach

                                <div id="menu-no-result" class="menu-no-result">
                                    <i class="fa fa-search mb-2" style="font-size:28px;color:#c1a07d"></i>
                                    <h4 class="mb-1">Menu tidak ditemukan</h4>
                                    <p class="mb-0">Coba gunakan kata pencarian atau kategori lain.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <aside id="food-cart" class="cart-panel">
                                <div class="cart-header">
                                    <div class="cart-heading"><span class="cart-icon"><i class="fa fa-shopping-basket"></i></span><div><h4>Keranjang</h4><small class="text-muted">Pesanan Kamar {{ $access->room->room_number }}</small></div></div>
                                    <span id="cart-count" class="cart-count">0</span>
                                </div>

                                <div id="cart-items" class="cart-items"></div>
                                <div id="cart-empty" class="cart-empty"><i class="fa fa-shopping-basket"></i><strong>Keranjang masih kosong</strong><span>Tekan tombol Tambah pada menu yang Anda inginkan.</span></div>

                                <div class="cart-checkout">
                                    <label class="delivery-label" for="delivery-notes"><i class="fa fa-comment-o mr-1"></i>Catatan pengantaran</label>
                                    <textarea id="delivery-notes" name="delivery_notes" class="delivery-notes" maxlength="1000" placeholder="Contoh: antar pukul 19.00, ketuk pintu dua kali">{{ old('delivery_notes') }}</textarea>
                                    <div class="cart-summary"><span>Total pesanan<br><small>Belum termasuk tagihan kamar lainnya</small></span><strong id="cart-total">Rp0</strong></div>
                                    <button id="submit-order" type="submit" class="submit-order-button" disabled><i class="fa fa-paper-plane mr-1"></i>Kirim Pesanan</button>
                                    <p class="folio-note"><i class="fa fa-shield mr-1"></i>Harga tersimpan saat dipesan dan masuk ke folio setelah pesanan diselesaikan Receptionist.</p>
                                </div>
                            </aside>
                        </div>
                    </div>
                </form>

                <div id="mobile-cart-bar" class="mobile-cart-bar">
                    <div><strong id="mobile-cart-total">Rp0</strong><small><span id="mobile-cart-count">0</span> item dipilih</small></div>
                    <button type="button" id="view-cart-button"><i class="fa fa-shopping-basket mr-1"></i>Lihat Keranjang</button>
                </div>
            @else
                <div class="auth-card text-center py-5">
                    <i class="fa fa-cutlery mb-3" style="font-size:42px;color:#dfa974"></i>
                    <h3>Belum ada menu tersedia</h3>
                    <p class="text-muted">Silakan hubungi Receptionist untuk informasi makanan dan minuman.</p>
                    <a href="{{ route('room-service.home') }}" class="sona-button d-inline-block mt-2">Kembali ke Portal</a>
                </div>
            @endif
        </div>
    </section>
@endsection

@if ($menuItems->isNotEmpty())
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('food-order-form');
                const cards = Array.from(document.querySelectorAll('[data-menu-card]'));
                const cartItems = document.getElementById('cart-items');
                const cartEmpty = document.getElementById('cart-empty');
                const cartCount = document.getElementById('cart-count');
                const cartTotal = document.getElementById('cart-total');
                const submitButton = document.getElementById('submit-order');
                const searchInput = document.getElementById('menu-search');
                const filterButtons = Array.from(document.querySelectorAll('[data-category-filter]'));
                const noResult = document.getElementById('menu-no-result');
                const mobileBar = document.getElementById('mobile-cart-bar');
                const mobileTotal = document.getElementById('mobile-cart-total');
                const mobileCount = document.getElementById('mobile-cart-count');
                let activeCategory = 'all';

                const rupiah = new Intl.NumberFormat('id-ID', {
                    style: 'currency', currency: 'IDR', maximumFractionDigits: 0
                });

                const cardFor = (id) => cards.find((card) => card.dataset.menuId === String(id));
                const quantityFor = (card) => Number(card.querySelector('[data-quantity-input]').value || 0);

                function setQuantity(id, nextQuantity) {
                    const card = cardFor(id);
                    if (!card) return;

                    const quantity = Math.max(0, Math.min(20, Number(nextQuantity) || 0));
                    card.querySelector('[data-quantity-input]').value = quantity;
                    render();
                }

                function createCartItem(card, quantity) {
                    const id = card.dataset.menuId;
                    const price = Number(card.dataset.menuPrice);
                    const item = document.createElement('div');
                    item.className = 'cart-item';

                    const top = document.createElement('div');
                    top.className = 'cart-item-top';
                    const detail = document.createElement('div');
                    detail.className = 'pr-2';
                    const name = document.createElement('span');
                    name.className = 'cart-item-name';
                    name.textContent = card.dataset.menuName;
                    const subtotal = document.createElement('span');
                    subtotal.className = 'cart-item-subtotal';
                    subtotal.textContent = rupiah.format(price * quantity);
                    detail.append(name, subtotal);

                    const stepper = document.createElement('div');
                    stepper.className = 'cart-mini-stepper';
                    const minus = document.createElement('button');
                    minus.type = 'button';
                    minus.textContent = '\u2212';
                    minus.setAttribute('aria-label', 'Kurangi ' + card.dataset.menuName);
                    minus.addEventListener('click', () => setQuantity(id, quantity - 1));
                    const amount = document.createElement('span');
                    amount.textContent = quantity;
                    const plus = document.createElement('button');
                    plus.type = 'button';
                    plus.textContent = '+';
                    plus.setAttribute('aria-label', 'Tambah ' + card.dataset.menuName);
                    plus.addEventListener('click', () => setQuantity(id, quantity + 1));
                    stepper.append(minus, amount, plus);
                    top.append(detail, stepper);

                    const notes = document.createElement('input');
                    notes.type = 'text';
                    notes.name = 'items[' + id + '][special_notes]';
                    notes.maxLength = 500;
                    notes.className = 'cart-note-input';
                    notes.placeholder = 'Catatan menu, contoh: tidak pedas';
                    notes.value = card.dataset.menuNote || '';
                    notes.addEventListener('input', () => { card.dataset.menuNote = notes.value; });

                    item.append(top, notes);
                    return item;
                }

                function render() {
                    const selected = [];
                    let itemCount = 0;
                    let total = 0;
                    cartItems.replaceChildren();

                    cards.forEach((card) => {
                        const id = card.dataset.menuId;
                        const quantity = quantityFor(card);
                        const addButton = card.querySelector('.add-menu-button');
                        const stepper = card.querySelector('[data-card-stepper]');
                        const quantityDisplay = card.querySelector('[data-quantity-display]');
                        const isSelected = quantity > 0;

                        card.classList.toggle('is-selected', isSelected);
                        addButton.hidden = isSelected;
                        stepper.hidden = !isSelected;
                        quantityDisplay.textContent = quantity;

                        if (isSelected) {
                            selected.push({ card, quantity });
                            itemCount += quantity;
                            total += Number(card.dataset.menuPrice) * quantity;
                        }
                    });

                    selected.forEach(({ card, quantity }) => cartItems.appendChild(createCartItem(card, quantity)));
                    cartEmpty.style.display = selected.length ? 'none' : 'block';
                    cartItems.style.display = selected.length ? 'block' : 'none';
                    cartCount.textContent = itemCount;
                    cartTotal.textContent = rupiah.format(total);
                    submitButton.disabled = selected.length === 0;
                    mobileCount.textContent = itemCount;
                    mobileTotal.textContent = rupiah.format(total);
                    mobileBar.classList.toggle('show', selected.length > 0);
                }

                function filterMenus() {
                    const query = searchInput.value.trim().toLocaleLowerCase('id-ID');
                    let visibleCards = 0;

                    document.querySelectorAll('[data-category-section]').forEach((section) => {
                        let visibleInSection = 0;
                        section.querySelectorAll('[data-menu-card]').forEach((card) => {
                            const matchesCategory = activeCategory === 'all' || card.dataset.menuCategory === activeCategory;
                            const matchesSearch = !query || card.dataset.menuName.toLocaleLowerCase('id-ID').includes(query);
                            const gridItem = card.closest('.menu-grid-item');
                            const visible = matchesCategory && matchesSearch;
                            gridItem.style.display = visible ? '' : 'none';
                            if (visible) visibleInSection++;
                        });
                        section.style.display = visibleInSection ? '' : 'none';
                        visibleCards += visibleInSection;
                    });

                    noResult.style.display = visibleCards ? 'none' : 'block';
                }

                document.addEventListener('click', function (event) {
                    const actionButton = event.target.closest('[data-cart-action]');
                    if (!actionButton) return;
                    const card = cardFor(actionButton.dataset.menuId);
                    const delta = actionButton.dataset.cartAction === 'increment' ? 1 : -1;
                    setQuantity(actionButton.dataset.menuId, quantityFor(card) + delta);
                });

                filterButtons.forEach((button) => {
                    button.addEventListener('click', function () {
                        activeCategory = button.dataset.categoryFilter;
                        filterButtons.forEach((item) => item.classList.toggle('active', item === button));
                        filterMenus();
                    });
                });

                searchInput.addEventListener('input', filterMenus);
                document.getElementById('view-cart-button').addEventListener('click', function () {
                    document.getElementById('food-cart').scrollIntoView({ behavior: 'smooth', block: 'start' });
                });

                form.addEventListener('submit', function (event) {
                    if (cards.every((card) => quantityFor(card) === 0)) {
                        event.preventDefault();
                        window.appNotyf?.error('Pilih minimal satu menu sebelum mengirim pesanan.');
                    }
                });

                render();
                filterMenus();
            });
        </script>
    @endpush
@endif
