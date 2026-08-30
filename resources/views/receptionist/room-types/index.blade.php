@extends('layouts.main')
@section('title', 'Tipe Kamar')
@section('content')
    <x-dashboard.page-heading title="Tipe Kamar" description="Kelola informasi, kapasitas, harga dasar, fasilitas, dan galeri tipe kamar.">
        <a href="{{ route('receptionist.room-types.create') }}" class="btn btn-primary"><i class="ti ti-plus me-1"></i>Tambah Tipe Kamar</a>
    </x-dashboard.page-heading>
    <div class="card"><div class="card-body">
        <form method="GET" class="row g-2 mb-4"><div class="col-md-9"><input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama atau kode tipe kamar..."></div><div class="col-md-3 d-flex gap-2"><button class="btn btn-outline-primary flex-grow-1">Cari</button><a href="{{ route('receptionist.room-types.index') }}" class="btn btn-outline-secondary">Reset</a></div></form>
        <div class="table-responsive"><table class="table align-middle text-nowrap"><thead><tr><th>Tipe Kamar</th><th>Kapasitas</th><th>Harga Dasar</th><th>Unit</th><th>Status</th><th class="text-end">Aksi</th></tr></thead><tbody>
            @forelse($roomTypes as $roomType)
                @php($primaryImage = $roomType->images->first())
                <tr>
                    <td><div class="d-flex align-items-center gap-3">@if($primaryImage)<img src="{{ asset('storage/'.$primaryImage->image_path) }}" class="rounded object-fit-cover" width="64" height="48" alt="{{ $primaryImage->alt_text }}">@else<span class="rounded bg-light-primary d-flex align-items-center justify-content-center" style="width:64px;height:48px"><i class="ti ti-photo text-primary fs-6"></i></span>@endif<div><a href="{{ route('receptionist.room-types.show', $roomType) }}" class="fw-semibold text-dark">{{ $roomType->name }}</a><small class="d-block text-muted">{{ $roomType->code }} · {{ $roomType->facilities_count }} fasilitas</small></div></div></td>
                    <td>{{ $roomType->capacity }} tamu</td><td>Rp{{ number_format((float) $roomType->base_price, 0, ',', '.') }}</td><td>{{ $roomType->rooms_count }}</td>
                    <td><span class="badge {{ $roomType->is_active ? 'bg-light-success text-success' : 'bg-light-danger text-danger' }}">{{ $roomType->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td><div class="d-flex justify-content-end gap-2"><a href="{{ route('receptionist.room-types.show', $roomType) }}" class="btn btn-sm btn-outline-secondary"><i class="ti ti-eye"></i></a><a href="{{ route('receptionist.room-types.edit', $roomType) }}" class="btn btn-sm btn-outline-primary"><i class="ti ti-edit"></i></a><form method="POST" action="{{ route('receptionist.room-types.destroy', $roomType) }}" data-confirm="Tipe kamar dengan riwayat terkait hanya akan dinonaktifkan." data-confirm-title="Hapus tipe kamar?">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="ti ti-trash"></i></button></form></div></td>
                </tr>
            @empty<tr><td colspan="6" class="text-center text-muted py-5"><i class="ti ti-bed fs-8 d-block mb-2"></i>Belum ada tipe kamar.</td></tr>@endforelse
        </tbody></table></div>{{ $roomTypes->links() }}
    </div></div>
@endsection
