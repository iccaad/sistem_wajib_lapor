@extends('layouts.admin')

@section('title', 'Manajemen Lokasi')
@section('page-title', 'Manajemen Lokasi')
@section('breadcrumb', 'Kelola titik lokasi wajib lapor')

@section('content')

<div class="flex justify-end mb-5">
    <a href="{{ route('admin.locations.create') }}"
       id="btn-tambah-lokasi"
       class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Tambah Lokasi
    </a>
</div>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Alamat</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Koordinat</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Radius</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Absensi</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($locations as $loc)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <div class="h-2 w-2 rounded-full {{ $loc->is_active ? 'bg-emerald-500' : 'bg-gray-300' }}"></div>
                                <span class="text-sm font-medium text-gray-900">{{ $loc->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 hidden md:table-cell">
                            <span class="text-sm text-gray-500">{{ $loc->address ?? '—' }}</span>
                        </td>
                        <td class="px-5 py-4 hidden lg:table-cell">
                            <span class="text-xs font-mono text-gray-500">
                                {{ number_format($loc->latitude, 6) }}, {{ number_format($loc->longitude, 6) }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-sm text-gray-700">{{ $loc->radius_meters }}m</span>
                        </td>
                        <td class="px-5 py-4">
                            @if ($loc->is_active)
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Aktif</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 hidden sm:table-cell">
                            <span class="text-sm text-gray-600">{{ $loc->attendance_logs_count }} kali</span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.locations.edit', $loc) }}"
                                   class="text-indigo-600 hover:text-indigo-800 text-xs font-medium transition">Edit</a>
                                <form method="POST" action="{{ route('admin.locations.toggle', $loc) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="{{ $loc->is_active ? 'text-amber-600 hover:text-amber-800' : 'text-emerald-600 hover:text-emerald-800' }} text-xs font-medium transition">
                                        {{ $loc->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-400">
                            Belum ada lokasi yang ditambahkan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
