@extends('layouts.main')
@section('title', 'Tambah Kategori F&B')
@section('content')
    <x-dashboard.page-heading title="Tambah Kategori F&B" description="Buat kelompok menu baru." :back="route('receptionist.food-categories.index')" />
    <div class="card"><div class="card-body"><form method="POST" action="{{ route('receptionist.food-categories.store') }}">@csrf @include('receptionist.food.categories.form')<x-dashboard.form-actions :cancel="route('receptionist.food-categories.index')" label="Simpan Kategori" /></form></div></div>
@endsection
