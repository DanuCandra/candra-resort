@extends('layouts.main')
@section('title','Edit Layanan Hotel')
@section('content')<x-dashboard.page-heading title="Edit Layanan Hotel" :description="$hotelService->name" :back="route('receptionist.hotel-services.index')"/><div class="card"><div class="card-body"><form method="POST" enctype="multipart/form-data" action="{{ route('receptionist.hotel-services.update',$hotelService) }}">@csrf @method('PUT') @include('receptionist.hotel-services.form')<x-dashboard.form-actions :cancel="route('receptionist.hotel-services.index')" label="Perbarui Layanan"/></form></div></div>@endsection
