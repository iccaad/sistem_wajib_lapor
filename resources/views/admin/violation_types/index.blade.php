@extends('layouts.admin')

@section('title', 'Jenis Pelanggaran')
@section('page-title', 'Jenis Pelanggaran')
@section('breadcrumb', 'Manajemen data jenis pelanggaran')

@section('content')

<div class="mb-5 flex justify-end">
    <a href="{{ route('admin.violation-types.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Tambah Jenis
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50/75">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Jenis Pelanggaran</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Deskripsi</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($violationTypes as $vt)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="text-sm font-medium text-gray-900">{{ $vt->name }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ Str::limit($vt->description, 50) ?: '—' }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form id="delete-form-{{ $vt->id }}" method="POST" action="{{ route('admin.violation-types.destroy', $vt) }}" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                            
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.violation-types.edit', $vt) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium transition">Edit</a>
                                <button type="button" 
                                        onclick="if(confirm('Apakah Anda yakin ingin menghapus jenis pelanggaran ini?')) document.getElementById('delete-form-{{ $vt->id }}').submit();"
                                        class="text-red-600 hover:text-red-800 text-xs font-medium transition">
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-10 text-center text-gray-500 text-sm">
                            Belum ada data jenis pelanggaran.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
