@extends('layouts.main')
@section('title','Edit Aturan Harga')
@section('content')<x-dashboard.page-heading title="Edit Aturan Harga" :description="$rate->name" :back="route('receptionist.pricing.index')"/><div class="card"><div class="card-body"><form method="POST" action="{{ route('receptionist.pricing.update',$rate) }}">@csrf @method('PUT') @include('receptionist.pricing.form')<x-dashboard.form-actions :cancel="route('receptionist.pricing.index')" label="Perbarui Harga"/></form></div></div>@endsection
