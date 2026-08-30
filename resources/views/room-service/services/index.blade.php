@extends('layouts.guest')

@section('title', 'Layanan Hotel')

@push('styles')
    <style>.service-card{border:0;box-shadow:0 8px 24px rgba(25,25,26,.08);overflow:hidden;height:100%}.service-card img{height:190px;width:100%;object-fit:cover}.service-price{color:#dfa974;font-size:20px;font-weight:700}.service-card .form-control{height:44px}</style>
@endpush

@section('content')
    <section class="auth-section"><div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-5"><div><p class="text-uppercase mb-1" style="letter-spacing:2px;color:#dfa974;font-weight:700">Kamar {{ $access->room->room_number }}</p><h2>Layanan Hotel</h2><p class="text-muted">Harga tersimpan saat dipesan dan ditagihkan setelah layanan selesai.</p></div><div><a href="{{ route('room-service.services.orders') }}" class="btn btn-outline-secondary mr-2">Pesanan Saya</a><a href="{{ route('room-service.home') }}" class="btn btn-outline-secondary">Portal</a></div></div>
        @forelse ($services as $category => $items)
            <div class="mb-5"><div class="section-title text-left mb-4"><span>{{ str($category ?: 'other')->replace('_', ' ')->title() }}</span></div><div class="row">
                @foreach ($items as $service)
                    <div class="col-md-6 col-lg-4 mb-4"><div class="card service-card">
                        @if ($service->image_path)
                            <img src="{{ Storage::url($service->image_path) }}" alt="{{ $service->name }}">
                        @else
                            <div class="d-flex justify-content-center align-items-center bg-light" style="height:190px"><i class="fa fa-bell" style="font-size:44px;color:#dfa974"></i></div>
                        @endif
                        <div class="card-body p-4"><h4>{{ $service->name }}</h4><p class="text-muted">{{ $service->description ?: 'Layanan untuk melengkapi kenyamanan Anda.' }}</p><p class="service-price">Rp{{ number_format((float) $service->price, 0, ',', '.') }} <small class="text-muted">/{{ str($service->price_unit)->replace('per_', '') }}</small></p>
                            @if ($service->duration_minutes)<small><i class="fa fa-clock-o"></i> ± {{ $service->duration_minutes }} menit</small>@endif
                            <form method="POST" action="{{ route('room-service.services.store') }}" class="sona-form mt-3" data-confirm="Pastikan jumlah, jadwal, dan catatan sudah benar." data-confirm-title="Pesan {{ $service->name }}?">
                                @csrf
                                <input type="hidden" name="hotel_service_id" value="{{ $service->id }}">
                                <div class="form-group"><label>Jumlah</label><input type="number" min="0.1" max="100" step="0.1" name="quantity" value="1" class="form-control" required></div>
                                @if ($service->requires_schedule)<div class="form-group"><label>Jadwal *</label><input type="datetime-local" min="{{ now()->addMinutes(15)->format('Y-m-d\TH:i') }}" name="scheduled_at" class="form-control" required></div>@endif
                                <div class="form-group"><label>Catatan</label><textarea name="notes" rows="2" class="form-control" placeholder="Detail kebutuhan Anda"></textarea></div>
                                <button class="sona-button w-100">Pesan Layanan</button>
                            </form>
                        </div>
                    </div></div>
                @endforeach
            </div></div>
        @empty
            <div class="auth-card text-center"><i class="fa fa-bell mb-3" style="font-size:42px;color:#dfa974"></i><h4>Belum ada layanan tersedia</h4><p class="text-muted">Silakan hubungi Receptionist untuk bantuan.</p></div>
        @endforelse
        <div class="d-flex justify-content-center mt-4">{{ $servicePaginator->links() }}</div>
    </div></section>
@endsection
