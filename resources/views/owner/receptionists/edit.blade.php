@extends('layouts.main')
@section('title','Edit Receptionist')
@section('content')<x-dashboard.page-heading title="Edit Receptionist" :description="$receptionist->name" :back="route('owner.receptionists.show',$receptionist)"/><div class="card"><div class="card-body"><form method="POST" action="{{ route('owner.receptionists.update',$receptionist) }}">@csrf @method('PUT') @include('owner.receptionists.form')<x-dashboard.form-actions :cancel="route('owner.receptionists.show',$receptionist)" label="Perbarui Akun"/></form></div></div>@endsection
