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
         class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 px-4"
         @click.self="open = false">
        <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="flex items-start gap-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-semibold text-gray-900">Nonaktifkan Peserta</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Anda akan menonaktifkan akun peserta <strong x-text="participantName"></strong>.
                        Peserta tidak dapat login setelah dinonaktifkan. Lanjutkan?
                    </p>
                </div>
            </div>
            <div class="mt-5 flex gap-3 justify-end">
                <button @click="open = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Batal
                </button>
                <button @click="confirm()"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition">
                    Ya, Nonaktifkan
                </button>
            </div>
        </div>
    </div>



{{-- ── Top Actions & Page Dropdown ── --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5">
    

    {{-- Right: Tambah Peserta Button --}}
    <a href="{{ route('admin.participants.create') }}"
       id="btn-tambah-peserta"
       class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-brand-accent hover:bg-brand-accent/80 text-white text-sm font-bold rounded-xl shadow-lg shadow-black/10 transition transform active:scale-95 whitespace-nowrap">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Tambah Peserta
    </a>
    
    {{-- Left: Page Dropdown --}}
    @include('components.per-page-dropdown', [
        'route' => 'admin.participants.index',
        'current' => $participants->perPage(),
        'class' => 'flex items-center justify-start'
    ])

</div>

{{-- ── Filter Bar ── --}}
@include('components.participant-filters', ['route' => 'admin.participants.index', 'admins' => $admins, 'showSearch' => true])

@include('components.participant-table', [
    'participants' => $participants,
    'showDeactivate' => true
])

</div>{{-- /x-data deactivate modal --}}
@endsection
