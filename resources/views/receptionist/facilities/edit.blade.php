@extends('layouts.main')
@section('title', 'Edit Fasilitas')
@section('content')
    <x-dashboard.page-heading title="Edit Fasilitas" :description="$facility->name" :back="route('receptionist.facilities.index')" />
    <div class="card"><div class="card-body"><form method="POST" action="{{ route('receptionist.facilities.update', $facility) }}">@csrf @method('PUT') @include('receptionist.facilities.form')<x-dashboard.form-actions :cancel="route('receptionist.facilities.index')" label="Perbarui Fasilitas" /></form></div></div>
@endsection
