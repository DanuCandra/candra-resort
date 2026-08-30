@extends('layouts.main')
@section('title', 'Tambah Kamar')
@section('content')
    <x-dashboard.page-heading title="Tambah Kamar" description="Daftarkan unit fisik baru dan buat QR permanennya secara otomatis." :back="route('receptionist.rooms.index')" />
    <div class="card"><div class="card-body"><form method="POST" action="{{ route('receptionist.rooms.store') }}">@csrf @include('receptionist.rooms.form')<x-dashboard.form-actions :cancel="route('receptionist.rooms.index')" label="Simpan Kamar" /></form></div></div>
@endsection
