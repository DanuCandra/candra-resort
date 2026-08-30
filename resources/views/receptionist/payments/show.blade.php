@extends('layouts.main')
@section('title',$payment->payment_code)
@section('content')
<x-dashboard.page-heading :title="$payment->payment_code" description="Detail transaksi pembayaran yang tersimpan." :back="route('receptionist.payments.index')"/>
<div class="row">
    <div class="col-lg-8"><div class="card"><div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-4"><div><span class="text-muted">Nominal Pembayaran</span><h2 class="text-primary">Rp{{ number_format((float)$payment->amount,0,',','.') }}</h2></div><span class="badge bg-light-{{ $payment->status->badgeClass() }} text-{{ $payment->status->badgeClass() }} fs-3">{{ $payment->status->label() }}</span></div>
        <div class="row g-4"><div class="col-md-4"><small class="text-muted">Metode</small><strong class="d-block">{{ $payment->method->name }}</strong></div><div class="col-md-4"><small class="text-muted">Kanal</small><strong class="d-block">{{ str($payment->method->channel)->title() }}</strong></div><div class="col-md-4"><small class="text-muted">Tujuan</small><strong class="d-block">{{ str($payment->purpose)->title() }}</strong></div><div class="col-md-4"><small class="text-muted">Dibuat</small><strong class="d-block">{{ $payment->created_at->format('d M Y H:i') }}</strong></div><div class="col-md-4"><small class="text-muted">Dibayar</small><strong class="d-block">{{ $payment->paid_at?->format('d M Y H:i') ?? '-' }}</strong></div><div class="col-md-4"><small class="text-muted">Diterima oleh</small><strong class="d-block">{{ $payment->receivedBy?->name ?? 'Sistem Midtrans' }}</strong></div></div>
        @if($payment->reference_number)
            <div class="alert alert-light border mt-4 mb-0"><strong>Referensi:</strong> {{ $payment->reference_number }}</div>
        @endif
        @if($payment->notes)
            <p class="mt-4 mb-0"><strong>Catatan:</strong> {{ $payment->notes }}</p>
        @endif
    </div></div></div>
    <div class="col-lg-4">
        <div class="card"><div class="card-body"><h5 class="fw-semibold mb-3">Terkait</h5>
            @if($payment->reservation)
                <small class="text-muted">Reservasi</small><a href="{{ route('receptionist.reservations.show',$payment->reservation) }}" class="d-block fw-semibold mb-3">{{ $payment->reservation->booking_code }}</a><p>{{ $payment->reservation->guest_name }}<br>{{ $payment->reservation->roomType->name }}</p>
            @endif
            @if($payment->folio)
                <hr><small class="text-muted">Folio</small><a href="{{ route('receptionist.folios.show',$payment->folio) }}" class="d-block fw-semibold">{{ $payment->folio->folio_number }}</a>
            @endif
        </div></div>
        @if($payment->method->channel==='midtrans')
            <div class="card"><div class="card-body"><h5 class="fw-semibold mb-3">Midtrans</h5><div class="mb-2"><small class="text-muted">Order ID</small><code class="d-block text-break">{{ $payment->midtrans_order_id ?? '-' }}</code></div><div><small class="text-muted">Jenis pembayaran</small><strong class="d-block">{{ str($payment->midtrans_payment_type ?: '-')->replace('_',' ')->title() }}</strong></div><p class="small text-muted mt-3 mb-0"><i class="ti ti-shield-lock me-1"></i>Payload dan credential rahasia tidak ditampilkan.</p></div></div>
        @endif
    </div>
</div>
@endsection
