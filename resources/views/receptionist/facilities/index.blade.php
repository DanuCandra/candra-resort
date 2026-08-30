@extends('layouts.main')
@section('title', 'Fasilitas')
@section('content')
    <x-dashboard.page-heading title="Fasilitas" description="Kelola fasilitas kamar dan fasilitas umum hotel.">
        <a href="{{ route('receptionist.facilities.create') }}" class="btn btn-primary"><i class="ti ti-plus me-1"></i>Tambah Fasilitas</a>
    </x-dashboard.page-heading>

    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-2 mb-4">
                <div class="col-md-6"><input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama atau deskripsi..."></div>
                <div class="col-md-3"><select name="scope" class="form-select"><option value="">Semua cakupan</option><option value="room" @selected(request('scope') === 'room')>Kamar</option><option value="hotel" @selected(request('scope') === 'hotel')>Hotel</option><option value="both" @selected(request('scope') === 'both')>Keduanya</option></select></div>
                <div class="col-md-3 d-flex gap-2"><button class="btn btn-outline-primary flex-grow-1">Filter</button><a href="{{ route('receptionist.facilities.index') }}" class="btn btn-outline-secondary">Reset</a></div>
            </form>
            <div class="table-responsive">
                <table class="table align-middle text-nowrap">
                    <thead><tr><th>Fasilitas</th><th>Cakupan</th><th>Tipe Kamar</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                    <tbody>
                    @forelse ($facilities as $facility)
                        <tr>
                            <td><div class="d-flex align-items-center gap-3"><span class="round-40 rounded bg-light-primary d-flex align-items-center justify-content-center"><i class="ti {{ $facility->icon ?: 'ti-building-community' }} text-primary"></i></span><div><h6 class="mb-0">{{ $facility->name }}</h6><small class="text-muted">{{ Str::limit($facility->description, 55) }}</small></div></div></td>
                            <td><span class="badge bg-light-secondary text-secondary">{{ ['room' => 'Kamar', 'hotel' => 'Hotel', 'both' => 'Keduanya'][$facility->scope] }}</span></td>
                            <td>{{ $facility->room_types_count }}</td>
                            <td><span class="badge {{ $facility->is_active ? 'bg-light-success text-success' : 'bg-light-danger text-danger' }}">{{ $facility->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                            <td><div class="d-flex justify-content-end gap-2"><a href="{{ route('receptionist.facilities.edit', $facility) }}" class="btn btn-sm btn-outline-primary"><i class="ti ti-edit"></i></a><form method="POST" action="{{ route('receptionist.facilities.destroy', $facility) }}" data-confirm="Fasilitas ini akan dihapus dari daftar aktif." data-confirm-title="Hapus fasilitas?">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="ti ti-trash"></i></button></form></div></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-5"><i class="ti ti-building-community fs-8 d-block mb-2"></i>Belum ada fasilitas.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $facilities->links() }}
        </div>
    </div>
@endsection
