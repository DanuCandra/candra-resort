@extends('layouts.main')
@section('title', 'Edit Menu')
@section('content')
    <x-dashboard.page-heading title="Edit Menu" :description="$menuItem->name" :back="route('receptionist.menu-items.index')" />
    <div class="card"><div class="card-body"><form method="POST" enctype="multipart/form-data" action="{{ route('receptionist.menu-items.update', $menuItem) }}">@csrf @method('PUT') @include('receptionist.food.menus.form')<x-dashboard.form-actions :cancel="route('receptionist.menu-items.index')" label="Perbarui Menu" /></form></div></div>
@endsection
