@extends('layouts.main')
@section('title', 'Edit Kamar')
@section('content')
    <x-dashboard.page-heading :title="'Edit Kamar '.$room->room_number" description="Perubahan status akan dicatat dalam histori operasional." :back="route('receptionist.rooms.show', $room)" />
    <div class="card"><div class="card-body"><form method="POST" action="{{ route('receptionist.rooms.update', $room) }}">@csrf @method('PUT') @include('receptionist.rooms.form')<x-dashboard.form-actions :cancel="route('receptionist.rooms.show', $room)" label="Perbarui Kamar" /></form></div></div>
@endsection
