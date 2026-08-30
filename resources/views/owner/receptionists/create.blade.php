@extends('layouts.main')
@section('title','Tambah Receptionist')
@section('content')<x-dashboard.page-heading title="Tambah Receptionist" description="Akun staf hanya dapat dibuat oleh Owner." :back="route('owner.receptionists.index')"/><div class="card"><div class="card-body"><form method="POST" action="{{ route('owner.receptionists.store') }}">@csrf @include('owner.receptionists.form')<x-dashboard.form-actions :cancel="route('owner.receptionists.index')" label="Buat Akun"/></form></div></div>@endsection
