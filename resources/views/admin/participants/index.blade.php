@extends('layouts.admin')

@section('title', 'Daftar Peserta')
@section('page-title', 'Daftar Peserta')
@section('breadcrumb', 'Kelola data peserta wajib lapor')

@section('content')

{{-- ── Alpine.js Deactivate Confirmation Modal ── --}}
<div x-data="{
    open: false,
    participantName: '',
    formId: '',
    openConfirm(name, id) {
        this.participantName = name;
        this.formId = 'deactivate-form-' + id;
        this.open = true;
    },
    confirm() {
        document.getElementById(this.formId).submit();
    }
}">

    {{-- Modal backdrop --}}
    <div x-show="open" x-cloak
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/60 px-4"
         @click.self="open = false">
        <div class="bg-gray-800 rounded-md shadow-xl max-w-sm w-full p-6"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="flex items-start gap-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-500/20">
                    <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-semibold text-gray-100">Nonaktifkan Peserta</h3>
                    <p class="mt-1 text-sm text-gray-400">
                        Anda akan menonaktifkan akun peserta <strong x-text="participantName"></strong>.
                        Peserta tidak dapat login setelah dinonaktifkan. Lanjutkan?
                    </p>
                </div>
            </div>
            <div class="mt-5 flex gap-3 justify-end">
                <button @click="open = false"
                        class="px-4 py-2 text-sm font-medium text-gray-300 bg-gray-800 border border-gray-600 rounded-md hover:bg-gray-900 transition">
                    Batal
                </button>
                <button @click="confirm()"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition">
                    Ya, Nonaktifkan
                </button>
            </div>
        </div>
    </div>

{{-- ── Top bar ── --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
    <form method="GET" action="{{ route('admin.participants.index') }}" class="flex gap-2 flex-1 max-w-lg">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nama atau NIK..."
               class="flex-1 px-3 py-2 text-sm border border-gray-600 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
        <button type="submit"
                class="px-4 py-2 text-sm font-medium text-gray-300 bg-gray-800 border border-gray-600 rounded-md hover:bg-gray-900 transition">
            Cari
        </button>
        @if(request('search'))
            <a href="{{ route('admin.participants.index') }}"
               class="px-3 py-2 text-sm text-gray-400 hover:text-gray-300 border border-gray-700 rounded-md hover:bg-gray-900 transition">
                ✕
            </a>
        @endif
    </form>

    <a href="{{ route('admin.participants.create') }}"
       id="btn-tambah-peserta"
       class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-md border-t-2 border-indigo-500 shadow-gray-950/50 transition">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Tambah Peserta
    </a>
</div>

{{-- ── Table ── --}}
<div class="bg-gray-800 rounded-md border border-gray-700 shadow-md border-t-2 border-indigo-500 shadow-gray-950/50 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-700">
            <thead class="bg-gray-900">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Nama</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider hidden sm:table-cell">NIK</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider hidden md:table-cell">Pelanggaran</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider hidden lg:table-cell">Masa Pengawasan</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider hidden lg:table-cell">Kuota</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider hidden xl:table-cell">Admin</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @forelse ($participants as $p)
                    <tr class="hover:bg-gray-900 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full
                                            {{ $p->status === 'active' ? 'bg-indigo-500/20 text-indigo-400' : 'bg-gray-800 text-gray-400' }}
                                            text-xs font-bold uppercase">
                                    {{ substr($p->full_name, 0, 1) }}
                                </div>
                                <span class="text-sm font-medium text-gray-100">{{ $p->full_name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 hidden sm:table-cell">
                            <span class="text-xs text-gray-400 font-mono">{{ $p->nik }}</span>
                        </td>
                        <td class="px-5 py-4 hidden md:table-cell">
                            <span class="text-sm text-gray-400">{{ Str::limit($p->violation_type, 22) }}</span>
                        </td>
                        <td class="px-5 py-4 hidden lg:table-cell">
                            <div class="text-xs text-gray-400 leading-5">
                                <div>{{ $p->supervision_start->format('d/m/Y') }}</div>
                                <div>s/d {{ $p->supervision_end->format('d/m/Y') }}</div>
                            </div>
                        </td>
                        <td class="px-5 py-4 hidden lg:table-cell">
                            <span class="text-sm text-gray-400">
                                {{ $p->quota_amount }}×/{{ $p->quota_type === 'weekly' ? 'minggu' : 'bulan' }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            @if ($p->status === 'active')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500/200"></span> Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-800 text-gray-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span> Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 hidden xl:table-cell">
                            <span class="text-sm text-gray-400">{{ $p->assignedAdmin?->name ?? '—' }}</span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            {{-- Hidden DELETE form for deactivation --}}
                            <form id="deactivate-form-{{ $p->id }}"
                                  method="POST" action="{{ route('admin.participants.destroy', $p) }}"
                                  class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>

                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.participants.show', $p) }}"
                                   class="text-indigo-400 hover:text-indigo-800 text-xs font-medium transition">Detail</a>
                                <a href="{{ route('admin.participants.edit', $p) }}"
                                   class="text-amber-400 hover:text-amber-800 text-xs font-medium transition">Edit</a>
                                @if($p->status === 'active')
                                    <button type="button"
                                            @click="openConfirm('{{ addslashes($p->full_name) }}', {{ $p->id }})"
                                            class="text-red-500 hover:text-red-400 text-xs font-medium transition">
                                        Nonaktifkan
                                    </button>
                                @else
                                    <span class="text-gray-300 text-xs">Nonaktif</span>
                                @endif
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
        <div class="px-5 py-3 bg-gray-900 border-t border-gray-700">
            {{ $participants->withQueryString()->links() }}
        </div>
    @endif
</div>

</div>{{-- /x-data deactivate modal --}}
@endsection


