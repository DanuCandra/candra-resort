@extends('layouts.main')
@section('title', 'Tambah Menu')
@section('content')
    <x-dashboard.page-heading title="Tambah Menu" description="Tambahkan makanan atau minuman ke portal layanan kamar." :back="route('receptionist.menu-items.index')" />
    <div class="card"><div class="card-body"><form method="POST" enctype="multipart/form-data" action="{{ route('receptionist.menu-items.store') }}">@csrf @include('receptionist.food.menus.form')<x-dashboard.form-actions :cancel="route('receptionist.menu-items.index')" label="Simpan Menu" /></form></div></div>
@endsection
