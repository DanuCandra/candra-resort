@extends('layouts.main')
@section('title','Edit Promosi')
@section('content')<x-dashboard.page-heading title="Edit Promosi" :description="$promotion->code.' · '.$promotion->name" :back="route('receptionist.promotions.index')"/><div class="card"><div class="card-body"><form method="POST" action="{{ route('receptionist.promotions.update',$promotion) }}">@csrf @method('PUT') @include('receptionist.promotions.form')<x-dashboard.form-actions :cancel="route('receptionist.promotions.index')" label="Perbarui Promosi"/></form></div></div>@endsection
