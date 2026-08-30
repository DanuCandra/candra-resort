@extends('layouts.main')
@section('title', 'Dashboard Receptionist')
@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4"><div><h4 class="fw-semibold mb-1">Dashboard Operasional</h4><p class="text-muted mb-0">Ringkasan aktivitas hotel hari ini, {{ now()->translatedFormat('d F Y') }}.</p></div></div>
    @php
        $cards = [
            ['Tamu Datang', $metrics['arrivals'], 'ti-calendar-event', 'primary'],
            ['Tamu Keluar', $metrics['departures'], 'ti-calendar-minus', 'warning'],
            ['Kamar Terisi', $metrics['occupied'], 'ti-bed', 'secondary'],
            ['Kamar Tersedia', $metrics['available'], 'ti-door', 'success'],
            ['Perlu Dibersihkan', $metrics['cleaning'], 'ti-spray', 'info'],
            ['Pembayaran Pending', $metrics['pending_payments'], 'ti-credit-card', 'danger'],
            ['Permintaan Tamu', $metrics['guest_requests'], 'ti-message-circle', 'warning'],
            ['Pesanan F&B', $metrics['food_orders'], 'ti-tools-kitchen-2', 'primary'],
        ];
    @endphp
    <div class="row">
        @foreach ($cards as [$label, $value, $icon, $color])
            <div class="col-xl-3 col-md-6"><div class="card"><div class="card-body"><div class="d-flex align-items-center"><span class="round-48 rounded bg-light-{{ $color }} d-flex align-items-center justify-content-center"><i class="ti {{ $icon }} fs-6 text-{{ $color }}"></i></span><div class="ms-3"><span class="text-muted d-block">{{ $label }}</span><h4 class="fw-semibold mb-0">{{ $value }}</h4></div></div></div></div></div>
        @endforeach
    </div>
    <div class="row">
        <div class="col-lg-6"><div class="card"><div class="card-body"><h5 class="card-title fw-semibold mb-4">Kedatangan Hari Ini</h5><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Tamu</th><th>Kamar</th><th>Status</th></tr></thead><tbody>@forelse($arrivals as $reservation)<tr><td><strong>{{ $reservation->guest_name }}</strong><span class="d-block fs-2 text-muted">{{ $reservation->booking_code }}</span></td><td>{{ $reservation->roomType->name }}</td><td><span class="badge bg-light-primary text-primary">{{ str($reservation->status->value)->replace('_', ' ')->title() }}</span></td></tr>@empty<tr><td colspan="3" class="text-center text-muted py-4">Tidak ada kedatangan.</td></tr>@endforelse</tbody></table></div></div></div></div>
        <div class="col-lg-6"><div class="card"><div class="card-body"><h5 class="card-title fw-semibold mb-4">Keberangkatan Hari Ini</h5><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Tamu</th><th>Kamar</th><th>Nomor</th></tr></thead><tbody>@forelse($departures as $reservation)<tr><td><strong>{{ $reservation->guest_name }}</strong><span class="d-block fs-2 text-muted">{{ $reservation->booking_code }}</span></td><td>{{ $reservation->roomType->name }}</td><td>{{ $reservation->room?->room_number ?? '-' }}</td></tr>@empty<tr><td colspan="3" class="text-center text-muted py-4">Tidak ada keberangkatan.</td></tr>@endforelse</tbody></table></div></div></div></div>
    </div>
    <div class="card"><div class="card-body"><h5 class="card-title fw-semibold mb-4">Permintaan Tamu Aktif</h5><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Kode</th><th>Kamar</th><th>Permintaan</th><th>Prioritas</th><th>Status</th></tr></thead><tbody>@forelse($recentRequests as $guestRequest)<tr><td>{{ $guestRequest->request_code }}</td><td>{{ $guestRequest->room->room_number }}</td><td>{{ $guestRequest->title }}</td><td><span class="badge bg-light-warning text-warning">{{ ucfirst($guestRequest->priority) }}</span></td><td>{{ ucfirst($guestRequest->status) }}</td></tr>@empty<tr><td colspan="5" class="text-center text-muted py-4">Tidak ada permintaan aktif.</td></tr>@endforelse</tbody></table></div></div></div>
@endsection
