@extends('layouts.admin')
@section('title', 'Edit Lokasi — ' . $location->name)
@section('page-title', 'Edit Lokasi')
@section('breadcrumb', 'Admin / Lokasi / Edit')
@section('content')
    @include('admin.locations._form', [
        'location'   => $location,
        'formAction' => route('admin.locations.update', $location),
        'method'     => 'PUT',
    ])
@endsection


