@extends('layouts.admin')

@section('title', 'Manajemen Lokasi')
@section('page-title', 'Manajemen Lokasi')
@section('breadcrumb', 'Kelola titik lokasi wajib lapor')

@section('content')

<div class="flex justify-end mb-5">
    <a href="{{ route('admin.locations.create') }}"
       id="btn-tambah-lokasi"
       class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-md border-t-2 border-indigo-500 shadow-gray-950/50 transition">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Tambah Lokasi
    </a>
</div>

{{-- Table wrapped in Alpine confirmation context --}}
<div x-data="{
    confirmOpen: false,
    confirmName: '',
    confirmAction: '',
    confirmIsActive: true,
    openConfirm(formId, name, isActive) {
        this.confirmName     = name;
        this.confirmAction   = formId;
        this.confirmIsActive = isActive;
        this.confirmOpen     = true;
    },
    submit() {
        document.getElementById(this.confirmAction).submit();
        this.confirmOpen = false;
    }
}">

    {{-- ── Confirmation Modal ── --}}
    <div x-show="confirmOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        {{-- backdrop --}}
        <div class="absolute inset-0 bg-gray-950/60" @click="confirmOpen = false"></div>
        {{-- panel --}}
        <div class="relative bg-gray-800 rounded-md shadow-xl max-w-sm w-full p-6"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <div class="flex items-start gap-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-500/20">
                    <svg class="h-5 w-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-base font-semibold text-gray-100"
                        x-text="confirmIsActive ? 'Nonaktifkan Lokasi?' : 'Aktifkan Lokasi?'"></h3>
                    <p class="mt-1 text-sm text-gray-400">
                        Lokasi <span class="font-semibold text-gray-200" x-text="'«' + confirmName + '»'"></span>
                        akan <span x-text="confirmIsActive ? 'dinonaktifkan dan tidak bisa digunakan untuk absensi.' : 'diaktifkan kembali.'"></span>
                    </p>
                </div>
            </div>
            <div class="mt-5 flex justify-end gap-3">
                <button type="button" @click="confirmOpen = false"
                        class="px-4 py-2 text-sm font-medium text-gray-300 bg-gray-800 border border-gray-600 rounded-md hover:bg-gray-900 transition">
                    Batal
                </button>
                <button type="button" @click="submit()"
                        :class="confirmIsActive ? 'bg-amber-600 hover:bg-amber-700' : 'bg-emerald-600 hover:bg-emerald-700'"
                        class="px-4 py-2 text-sm font-medium text-white rounded-md transition">
                    <span x-text="confirmIsActive ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- ── Table ── --}}
    <div class="bg-gray-800 rounded-md border border-gray-700 shadow-md border-t-2 border-indigo-500 shadow-gray-950/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-900">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Nama</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider hidden md:table-cell">Alamat</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider hidden lg:table-cell">Koordinat</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Radius</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider hidden sm:table-cell">Absensi</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse ($locations as $loc)
                        <tr class="hover:bg-gray-900 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="h-2 w-2 rounded-full {{ $loc->is_active ? 'bg-emerald-500/200' : 'bg-gray-300' }}"></div>
                                    <span class="text-sm font-medium text-gray-100">{{ $loc->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 hidden md:table-cell">
                                <span class="text-sm text-gray-400">{{ $loc->address ?? '—' }}</span>
                            </td>
                            <td class="px-5 py-4 hidden lg:table-cell">
                                <span class="text-xs font-mono text-gray-400">
                                    {{ number_format($loc->latitude, 6) }}, {{ number_format($loc->longitude, 6) }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-sm text-gray-300">{{ $loc->radius_meters }}m</span>
                            </td>
                            <td class="px-5 py-4">
                                @if ($loc->is_active)
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-400">Aktif</span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-gray-800 text-gray-400">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 hidden sm:table-cell">
                                <span class="text-sm text-gray-400">{{ $loc->attendance_logs_count }} kali</span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.locations.edit', $loc) }}"
                                       class="text-indigo-400 hover:text-indigo-800 text-xs font-medium transition">Edit</a>

                                    {{-- Hidden form — submitted via Alpine modal --}}
                                    <form id="toggle-form-{{ $loc->id }}"
                                          method="POST"
                                          action="{{ route('admin.locations.toggle', $loc) }}">
                                        @csrf
                                        @method('PATCH')
                                    </form>

                                    <button type="button"
                                            @click="openConfirm('toggle-form-{{ $loc->id }}', '{{ addslashes($loc->name) }}', {{ $loc->is_active ? 'true' : 'false' }})"
                                            class="{{ $loc->is_active ? 'text-amber-400 hover:text-amber-800' : 'text-emerald-400 hover:text-emerald-800' }} text-xs font-medium transition">
                                        {{ $loc->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
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

        @if ($locations->hasPages())
            <div class="px-5 py-3 bg-gray-900 border-t border-gray-700">
                {{ $locations->links() }}
            </div>
        @endif
    </div>
</div>

@endsection


