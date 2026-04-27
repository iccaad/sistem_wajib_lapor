@extends('layouts.admin')
@section('title', 'Tambah Lokasi')
@section('page-title', 'Tambah Lokasi')
@section('breadcrumb', 'Admin / Lokasi / Tambah')
@section('content')
    @include('admin.locations._form', [
        'location'    => null,
        'formAction'  => route('admin.locations.store'),
        'method'      => 'POST',
    ])
@endsection
