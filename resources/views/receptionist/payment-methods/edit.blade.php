@extends('layouts.main')
@section('title','Edit Metode Pembayaran')
@section('content')<x-dashboard.page-heading title="Edit Metode Pembayaran" :description="$paymentMethod->name" :back="route('receptionist.payment-methods.index')"/><div class="card"><div class="card-body"><form method="POST" action="{{ route('receptionist.payment-methods.update',$paymentMethod) }}">@csrf @method('PUT') @include('receptionist.payment-methods.form')<x-dashboard.form-actions :cancel="route('receptionist.payment-methods.index')" label="Perbarui Metode"/></form></div></div>@endsection
