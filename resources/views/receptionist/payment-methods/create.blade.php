@extends('layouts.main')
@section('title','Tambah Metode Pembayaran')
@section('content')<x-dashboard.page-heading title="Tambah Metode Pembayaran" description="Tambahkan metode manual atau kanal pembayaran online." :back="route('receptionist.payment-methods.index')"/><div class="card"><div class="card-body"><form method="POST" action="{{ route('receptionist.payment-methods.store') }}">@csrf @include('receptionist.payment-methods.form')<x-dashboard.form-actions :cancel="route('receptionist.payment-methods.index')" label="Simpan Metode"/></form></div></div>@endsection
