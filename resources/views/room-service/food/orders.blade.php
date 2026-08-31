@extends('layouts.guest')

@section('title', 'Pantau Pesanan Makanan')

@push('styles')
    @include('room-service.partials.tracking-styles')
@endpush

@section('content')
    <section id="food-tracking-page" class="tracking-page">
        <div class="container">
            <div class="tracking-hero">
                <div class="tracking-hero-content d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <div>
                        <span class="tracking-room-pill"><i class="fa fa-bed"></i>Kamar {{ $access->room->room_number }}</span>
                        <h1>Pantau Pesanan Makanan</h1>
                        <p>Lihat proses setiap pesanan, mulai dari diterima dapur hingga siap diantar ke kamar Anda.</p>
                    </div>
                    <div class="tracking-actions">
                        <a href="{{ route('room-service.food.index') }}" class="tracking-action is-primary"><i class="fa fa-plus"></i>Pesan Lagi</a>
                        <a href="{{ route('room-service.home') }}" class="tracking-action"><i class="fa fa-th-large"></i>Portal Kamar</a>
                    </div>
                </div>
            </div>

            <div class="tracking-summary" aria-label="Ringkasan Pesanan">
                <div class="tracking-stat"><span class="tracking-stat-icon"><i class="fa fa-list-alt"></i></span><div><small>Total pesanan</small><strong>{{ $summary['total'] }}</strong></div></div>
                <div class="tracking-stat"><span class="tracking-stat-icon"><i class="fa fa-clock-o"></i></span><div><small>Sedang diproses</small><strong>{{ $summary['active'] }}</strong></div></div>
                <div class="tracking-stat"><span class="tracking-stat-icon"><i class="fa fa-check"></i></span><div><small>Telah selesai</small><strong>{{ $summary['completed'] }}</strong></div></div>
                <div class="tracking-stat"><span class="tracking-stat-icon"><i class="fa fa-credit-card"></i></span><div><small>Nilai pesanan</small><strong>Rp{{ number_format($summary['amount'], 0, ',', '.') }}</strong></div></div>
            </div>

            <div class="tracking-toolbar">
                <div class="tracking-toolbar-copy"><span>Riwayat &amp; progres</span><h2>Pesanan selama menginap</h2></div>
                <nav class="tracking-filters" aria-label="Filter status pesanan makanan">
                    <a href="{{ route('room-service.food.orders') }}" class="tracking-filter {{ $selectedStatus === null ? 'is-active' : '' }}"><i class="fa fa-th-large"></i>Semua</a>
                    @foreach ($statuses as $status)
                        <a href="{{ route('room-service.food.orders', ['status' => $status->value]) }}" class="tracking-filter {{ $selectedStatus === $status->value ? 'is-active' : '' }}">{{ $status->label() }}</a>
                    @endforeach
                </nav>
            </div>

            @if ($orders->isNotEmpty())
                <div class="tracking-list">
                    @foreach ($orders as $order)
                        @php
                            $stages = [
                                ['requested', 'Dikirim', 'fa-paper-plane'],
                                ['accepted', 'Diterima', 'fa-check'],
                                ['processing', 'Dimasak', 'fa-fire'],
                                ['completed', 'Selesai', 'fa-bell'],
                            ];
                            $currentRank = collect($stages)->search(fn ($stage) => $stage[0] === $order->status->value);
                            $isCancelled = $order->status->value === 'cancelled';
                        @endphp
                        <article class="tracking-card">
                            <div class="tracking-card-top">
                                <div class="tracking-order-code"><span class="tracking-order-icon"><i class="fa fa-cutlery"></i></span><div><small>Kode pesanan</small><strong>{{ $order->order_code }}</strong></div></div>
                                <span class="badge badge-{{ $order->status->badgeClass() }} tracking-status">{{ $order->status->label() }}</span>
                            </div>
                            <div class="tracking-card-content">
                                <div class="tracking-main">
                                    <h3 class="tracking-primary-title">{{ (float) $order->items->sum('quantity') }} item dari {{ $order->items->count() }} menu</h3>
                                    <div class="tracking-meta">
                                        <span><i class="fa fa-calendar-o"></i>{{ $order->ordered_at?->translatedFormat('d M Y, H:i') ?? $order->created_at->translatedFormat('d M Y, H:i') }}</span>
                                        <span><i class="fa fa-map-marker"></i>Diantar ke Kamar {{ $access->room->room_number }}</span>
                                        <span><i class="fa fa-file-text-o"></i>Masuk ke tagihan kamar setelah selesai</span>
                                    </div>
                                    <div class="tracking-item-preview">
                                        @foreach ($order->items->take(3) as $item)
                                            <span class="tracking-item-chip">{{ (float) $item->quantity }}&times; {{ $item->item_name }}</span>
                                        @endforeach
                                        @if ($order->items->count() > 3)<span class="tracking-item-chip">+{{ $order->items->count() - 3 }} menu lain</span>@endif
                                    </div>
                                    @if ($order->delivery_notes)<div class="tracking-note"><i class="fa fa-sticky-note-o mr-1"></i><strong>Catatan:</strong> {{ $order->delivery_notes }}</div>@endif
                                    <div class="tracking-price-row">
                                        <div class="tracking-price"><small>Total pesanan</small><strong>Rp{{ number_format((float) $order->total_amount, 0, ',', '.') }}</strong></div>
                                        <a href="{{ route('room-service.food.show', $order) }}" class="tracking-detail-link">Lihat rincian <i class="fa fa-arrow-right"></i></a>
                                    </div>
                                </div>
                                <div>
                                    @if ($isCancelled)
                                        <div class="tracking-cancelled"><i class="fa fa-times-circle"></i><span>Pesanan ini dibatalkan. Hubungi Receptionist jika Anda membutuhkan informasi lebih lanjut.</span></div>
                                    @else
                                        <div class="order-progress" style="--progress-steps:{{ count($stages) }}" aria-label="Progres pesanan {{ $order->order_code }}">
                                            @foreach ($stages as $index => [$value, $label, $icon])
                                                <div class="progress-step {{ $currentRank !== false && $index < $currentRank ? 'is-complete' : '' }} {{ $currentRank === $index ? 'is-current' : '' }}">
                                                    <span class="progress-dot"><i class="fa {{ $currentRank !== false && $index < $currentRank ? 'fa-check' : $icon }}"></i></span>
                                                    <strong>{{ $label }}</strong>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                @if ($orders->hasPages())<div class="tracking-pagination">{{ $orders->links() }}</div>@endif
            @else
                <div class="tracking-empty"><span class="tracking-empty-icon"><i class="fa fa-cutlery"></i></span><h3>{{ $selectedStatus ? 'Belum ada pesanan dengan status ini' : 'Belum ada pesanan makanan' }}</h3><p>{{ $selectedStatus ? 'Pilih status lain untuk melihat riwayat pesanan Anda.' : 'Menu pilihan Anda akan dapat dipantau dari halaman ini.' }}</p><a href="{{ route('room-service.food.index') }}" class="sona-button">Lihat Menu</a></div>
            @endif
        </div>
    </section>
@endsection
