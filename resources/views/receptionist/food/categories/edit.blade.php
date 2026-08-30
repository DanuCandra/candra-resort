@extends('layouts.main')
@section('title', 'Edit Kategori F&B')
@section('content')
    <x-dashboard.page-heading title="Edit Kategori F&B" :description="$foodCategory->name" :back="route('receptionist.food-categories.index')" />
    <div class="card"><div class="card-body"><form method="POST" action="{{ route('receptionist.food-categories.update', $foodCategory) }}">@csrf @method('PUT') @include('receptionist.food.categories.form')<x-dashboard.form-actions :cancel="route('receptionist.food-categories.index')" label="Perbarui Kategori" /></form></div></div>
@endsection
