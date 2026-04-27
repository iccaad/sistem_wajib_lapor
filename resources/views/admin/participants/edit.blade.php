@extends('layouts.admin')

@section('title', 'Edit Peserta')
@section('page-title', 'Edit Data Peserta')
@section('breadcrumb', 'Admin / Peserta / Edit')

@section('content')

<div class="max-w-3xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.participants.show', $participant) }}"
                   class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </a>
                <h2 class="text-base font-semibold text-gray-800">Edit: {{ $participant->full_name }}</h2>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.participants.update', $participant) }}" class="p-6">
            @csrf
            @method('PUT')

            {{-- NIK readonly notice --}}
            <div class="mb-5 p-3 bg-amber-50 border border-amber-200 rounded-lg flex items-center gap-2">
                <svg class="h-4 w-4 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
                <p class="text-xs text-amber-700">NIK tidak dapat diubah karena merupakan identifier tetap peserta.</p>
            </div>

            @include('admin.participants._form', ['participant' => $participant, 'nikReadonly' => true])

            <div class="flex justify-end gap-3 mt-6 pt-5 border-t border-gray-100">
                <a href="{{ route('admin.participants.show', $participant) }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" id="btn-update-peserta"
                        class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
