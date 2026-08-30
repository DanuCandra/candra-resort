@extends('layouts.main')
@section('title','Tambah Aturan Harga')
@section('content')<x-dashboard.page-heading title="Tambah Aturan Harga" description="Harga ini akan dipilih berdasarkan periode, hari, dan prioritas." :back="route('receptionist.pricing.index')"/><div class="card"><div class="card-body"><form method="POST" action="{{ route('receptionist.pricing.store') }}">@csrf @include('receptionist.pricing.form')<x-dashboard.form-actions :cancel="route('receptionist.pricing.index')" label="Simpan Harga"/></form></div></div>@endsection
