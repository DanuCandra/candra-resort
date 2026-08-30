@extends('layouts.main')
@section('title', 'Tambah Fasilitas')
@section('content')
    <x-dashboard.page-heading title="Tambah Fasilitas" description="Tambahkan fasilitas baru untuk kamar atau area hotel." :back="route('receptionist.facilities.index')" />
    <div class="card"><div class="card-body"><form method="POST" action="{{ route('receptionist.facilities.store') }}">@csrf @include('receptionist.facilities.form')<x-dashboard.form-actions :cancel="route('receptionist.facilities.index')" label="Simpan Fasilitas" /></form></div></div>
@endsection
