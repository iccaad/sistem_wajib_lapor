@extends('layouts.admin')

@section('title', 'Edit Jenis Pelanggaran')
@section('page-title', 'Edit Jenis Pelanggaran')
@section('breadcrumb', 'Manajemen data jenis pelanggaran / Edit')

@section('content')

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.violation-types.update', $violationType) }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Jenis Pelanggaran <span class="text-red-500">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name', $violationType->name) }}" required autofocus
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-400 @enderror">
            @error('name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
            <textarea id="description" name="description" rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-400 @enderror">{{ old('description', $violationType->description) }}</textarea>
            @error('description') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
        </div>

        <div class="pt-4 flex items-center justify-end gap-3 border-t">
            <a href="{{ route('admin.violation-types.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@endsection
