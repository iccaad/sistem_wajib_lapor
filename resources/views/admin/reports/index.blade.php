@extends('layouts.admin')

@section('title', 'Laporan Kepatuhan')
@section('page-title', 'Laporan Kepatuhan')
@section('breadcrumb', 'Rekap kepatuhan seluruh peserta wajib lapor')

@section('content')

{{-- ── Cetak Excel ── --}}
<div class="flex justify-end mb-5 no-print">
    <a href="{{ route('admin.reports.export', request()->all()) }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-lg shadow-lg shadow-black/10 transition transform active:scale-95">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
        </svg>
        Cetak Excel
    </a>
</div>

{{-- ── Filter Bar ── --}}
@include('components.participant-filters', ['route' => 'admin.reports.index', 'admins' => $admins])

{{-- Print header (hidden on screen) --}}
<div class="print-only hidden mb-6">
    <h1 class="text-xl font-bold text-center">LAPORAN KEPATUHAN PESERTA WAJIB LAPOR</h1>
    <p class="text-center text-sm mt-1">Polrestabes Semarang — Dicetak {{ now()->translatedFormat('d M Y, H:i') }} WIB</p>
</div>

@include('components.participant-table', [
    'participants' => $participants,
    'emptyText' => 'Tidak ada data.'
])

@endsection

@push('head')
<style>
@media print {
    .no-print { display: none !important; }
    .print-only { display: block !important; }
    aside { display: none !important; }
    header { display: none !important; }
    main { padding: 0 !important; }
    body { background: white; }
    .bg-white { box-shadow: none !important; border: 1px solid #e5e7eb !important; }
}
</style>
@endpush
