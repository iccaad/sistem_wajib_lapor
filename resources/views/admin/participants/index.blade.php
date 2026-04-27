@extends('layouts.admin')

@section('title', 'Daftar Peserta')
@section('page-title', 'Daftar Peserta')
@section('breadcrumb', 'Kelola data peserta wajib lapor')

@section('content')

{{-- ── Top bar ── --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
    <form method="GET" action="{{ route('admin.participants.index') }}" class="flex gap-2 flex-1 max-w-lg">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nama atau NIK..."
               class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
        <button type="submit"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
            Cari
        </button>
        @if(request('search'))
            <a href="{{ route('admin.participants.index') }}"
               class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                ✕
            </a>
        @endif
    </form>

    <a href="{{ route('admin.participants.create') }}"
       id="btn-tambah-peserta"
       class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Tambah Peserta
    </a>
</div>

{{-- ── Table ── --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">NIK</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Pelanggaran</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Masa Pengawasan</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Kuota</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden xl:table-cell">Admin</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($participants as $p)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full
                                            {{ $p->status === 'active' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-400' }}
                                            text-xs font-bold uppercase">
                                    {{ substr($p->full_name, 0, 1) }}
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $p->full_name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 hidden sm:table-cell">
                            <span class="text-xs text-gray-500 font-mono">{{ $p->nik }}</span>
                        </td>
                        <td class="px-5 py-4 hidden md:table-cell">
                            <span class="text-sm text-gray-600">{{ Str::limit($p->violation_type, 22) }}</span>
                        </td>
                        <td class="px-5 py-4 hidden lg:table-cell">
                            <div class="text-xs text-gray-500 leading-5">
                                <div>{{ $p->supervision_start->format('d/m/Y') }}</div>
                                <div>s/d {{ $p->supervision_end->format('d/m/Y') }}</div>
                            </div>
                        </td>
                        <td class="px-5 py-4 hidden lg:table-cell">
                            <span class="text-sm text-gray-600">
                                {{ $p->quota_amount }}×/{{ $p->quota_type === 'weekly' ? 'minggu' : 'bulan' }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            @if ($p->status === 'active')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span> Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 hidden xl:table-cell">
                            <span class="text-sm text-gray-500">{{ $p->assignedAdmin?->name ?? '—' }}</span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.participants.show', $p) }}"
                                   class="text-indigo-600 hover:text-indigo-800 text-xs font-medium transition">Detail</a>
                                <a href="{{ route('admin.participants.edit', $p) }}"
                                   class="text-amber-600 hover:text-amber-800 text-xs font-medium transition">Edit</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center text-sm text-gray-400">
                            <svg class="h-10 w-10 mx-auto mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                            </svg>
                            Belum ada peserta terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($participants->hasPages())
        <div class="px-5 py-3 bg-gray-50 border-t border-gray-100">
            {{ $participants->withQueryString()->links() }}
        </div>
    @endif
</div>

@endsection
