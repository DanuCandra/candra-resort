@extends('layouts.guest')
@section('title','Tagihan Berjalan')
@push('styles')
<style>.bill-summary{background:linear-gradient(135deg,#19191a,#353538);color:#fff}.bill-summary .muted{color:rgba(255,255,255,.65)}.bill-row{border-bottom:1px solid #eee;padding:16px 0}.bill-icon{align-items:center;background:#f8efe5;border-radius:50%;color:#dfa974;display:flex;height:42px;justify-content:center;width:42px}</style>
@endpush
@section('content')
<section class="auth-section"><div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-5"><div><p class="text-uppercase mb-1" style="letter-spacing:2px;color:#dfa974;font-weight:700">Kamar {{ $access->room->room_number }}</p><h2>Tagihan Berjalan</h2><p class="text-muted">Rincian biaya yang telah diposting selama masa menginap.</p></div><a href="{{ route('room-service.home') }}" class="btn btn-outline-secondary">Kembali ke Portal</a></div>
    @if($folio)
        <div class="row"><div class="col-lg-8"><div class="auth-card"><h3 class="mb-4">Rincian Folio</h3>
            @forelse($folio->items as $item)
                @if(! $item->is_void)
                    <div class="bill-row d-flex align-items-center"><span class="bill-icon mr-3"><i class="fa {{ $item->item_type==='room'?'fa-bed':($item->item_type==='food'?'fa-cutlery':'fa-bell') }}"></i></span><div class="flex-grow-1"><strong>{{ $item->description }}</strong><small class="d-block text-muted">{{ $item->posted_at->translatedFormat('d M Y H:i') }} · {{ (float)$item->quantity }} × Rp{{ number_format((float)$item->unit_price,0,',','.') }}</small></div><strong>Rp{{ number_format((float)$item->amount,0,',','.') }}</strong></div>
                @endif
            @empty
                <p class="text-muted text-center py-5">Belum ada item tagihan.</p>
            @endforelse
        </div>
        @if($folio->payments->isNotEmpty())
            <div class="auth-card mt-4"><h3 class="mb-3">Pembayaran</h3>
                @foreach($folio->payments as $payment)
                    <div class="d-flex justify-content-between border-bottom py-3"><span>{{ $payment->method->name }}<small class="d-block text-muted">{{ $payment->payment_code }} · {{ $payment->status->label() }}</small></span><strong class="text-success">Rp{{ number_format((float)$payment->amount,0,',','.') }}</strong></div>
                @endforeach
            </div>
        @endif
        </div><div class="col-lg-4"><div class="auth-card bill-summary"><small class="muted">Nomor Folio</small><h4 class="text-white">{{ $folio->folio_number }}</h4><hr style="border-color:rgba(255,255,255,.15)"><div class="d-flex justify-content-between"><span class="muted">Total</span><strong>Rp{{ number_format((float)$folio->total_amount,0,',','.') }}</strong></div><div class="d-flex justify-content-between mt-2"><span class="muted">Sudah dibayar</span><strong class="text-success">Rp{{ number_format((float)$folio->paid_amount,0,',','.') }}</strong></div><div class="d-flex justify-content-between mt-4 pt-4" style="border-top:1px solid rgba(255,255,255,.15)"><span>Outstanding</span><h4 class="text-white mb-0">Rp{{ number_format((float)$folio->balance_amount,0,',','.') }}</h4></div><p class="muted small mt-4 mb-0">Pembayaran akhir diproses Receptionist saat atau sebelum check-out.</p></div></div></div>
    @else
        <div class="auth-card text-center"><h3>Folio belum tersedia</h3><p class="text-muted">Silakan hubungi Receptionist jika Anda memerlukan bantuan.</p></div>
    @endif
</div></section>
@endsection
