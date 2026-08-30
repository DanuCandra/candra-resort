@extends('layouts.main')
@section('title','Tambah Promosi')
@section('content')<x-dashboard.page-heading title="Tambah Promosi" description="Tentukan aturan diskon dan cakupan kamar." :back="route('receptionist.promotions.index')"/><div class="card"><div class="card-body"><form method="POST" action="{{ route('receptionist.promotions.store') }}">@csrf @include('receptionist.promotions.form')<x-dashboard.form-actions :cancel="route('receptionist.promotions.index')" label="Simpan Promosi"/></form></div></div>@endsection
