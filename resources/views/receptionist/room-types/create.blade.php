@extends('layouts.main')
@section('title', 'Tambah Tipe Kamar')
@section('content')
    <x-dashboard.page-heading title="Tambah Tipe Kamar" description="Lengkapi informasi yang akan dilihat calon tamu." :back="route('receptionist.room-types.index')" />
    <div class="card"><div class="card-body"><form method="POST" enctype="multipart/form-data" action="{{ route('receptionist.room-types.store') }}">@csrf @include('receptionist.room-types.form')<x-dashboard.form-actions :cancel="route('receptionist.room-types.index')" label="Simpan Tipe Kamar" /></form></div></div>
@endsection
