@extends('layouts.guest')
@section('title', $order->order_code)
@section('content')
<section class="auth-section"><div class="container"><div class="d-flex flex-wrap justify-content-between align-items-center mb-5"><div><p class="text-uppercase mb-1" style="letter-spacing:2px;color:#dfa974;font-weight:700">Detail Pesanan</p><h2>{{ $order->order_code }}</h2><p class="text-muted">{{ $order->ordered_at->translatedFormat('d F Y, H:i') }} · Kamar {{ $order->room->room_number }}</p></div><a href="{{ route('room-service.food.orders') }}" class="btn btn-outline-secondary">Kembali</a></div>
    <div class="row"><div class="col-lg-8"><div class="auth-card"><h4 class="mb-4">Rincian Menu</h4>
        @foreach($order->items as $item)
            <div class="d-flex justify-content-between border-bottom py-3"><div><strong>{{ $item->quantity }}× {{ $item->item_name }}</strong>@if($item->special_notes)<small class="d-block text-muted">{{ $item->special_notes }}</small>@endif</div><span>Rp{{ number_format((float)$item->subtotal,0,',','.') }}</span></div>
        @endforeach
        @if($order->delivery_notes)
            <div class="mt-4"><strong>Catatan pengantaran</strong><p class="text-muted mb-0">{{ $order->delivery_notes }}</p></div>
        @endif
    </div></div><div class="col-lg-4"><div class="auth-card"><span class="badge badge-{{ $order->status->badgeClass() }} mb-3">{{ $order->status->label() }}</span><div class="d-flex justify-content-between"><span>Subtotal</span><span>Rp{{ number_format((float)$order->subtotal,0,',','.') }}</span></div><hr><div class="d-flex justify-content-between"><strong>Total</strong><strong style="color:#dfa974">Rp{{ number_format((float)$order->total_amount,0,',','.') }}</strong></div><p class="small text-muted mt-3 mb-0">Ditagihkan ke kamar setelah pesanan selesai.</p></div></div></div>
</div></section>
@endsection
