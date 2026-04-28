@extends('layouts.admin')

@section('title', 'Laporan — ' . $participant->full_name)
@section('page-title', 'Laporan Peserta')
@section('breadcrumb', 'Laporan / ' . $participant->full_name)

@section('content')

{{-- Print & nav buttons (no-print) --}}
<div class="flex items-center justify-between mb-5 no-print">
    <a href="{{ route('admin.reports.index') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
        Kembali ke Laporan
    </a>
    <button onclick="window.print()"
            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg shadow-sm transition">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.056 48.056 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659" />
        </svg>
        Cetak
    </button>
</div>

{{-- ── Document Header (print-visible) ── --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8 mb-5 print-doc">
    <div class="text-center border-b border-gray-200 pb-5 mb-5">
        <h1 class="text-lg font-bold text-gray-900">LAPORAN KEPATUHAN WAJIB LAPOR</h1>
        <p class="text-sm text-gray-500 mt-1">Polrestabes Semarang</p>
    </div>

    <div class="grid grid-cols-2 gap-x-8 gap-y-2 text-sm mb-6">
        <div><span class="text-gray-500 w-40 inline-block">Nama Peserta</span><span class="font-medium">: {{ $participant->full_name }}</span></div>
        <div><span class="text-gray-500 w-40 inline-block">NIK</span><span class="font-medium">: {{ $participant->nik }}</span></div>
        <div><span class="text-gray-500 w-40 inline-block">Jenis Pelanggaran</span><span class="font-medium">: {{ $participant->violationType->name ?? '—' }}</span></div>
        <div><span class="text-gray-500 w-40 inline-block">Admin Pengawas</span><span class="font-medium">: {{ $participant->assignedAdmin?->name ?? '—' }}</span></div>
        <div><span class="text-gray-500 w-40 inline-block">Mulai Pengawasan</span><span class="font-medium">: {{ $participant->supervision_start->translatedFormat('d F Y') }}</span></div>
        <div><span class="text-gray-500 w-40 inline-block">Selesai Pengawasan</span><span class="font-medium">: {{ $participant->supervision_end->translatedFormat('d F Y') }}</span></div>
        <div><span class="text-gray-500 w-40 inline-block">Kuota</span><span class="font-medium">: {{ $participant->quota_amount }}× per {{ $participant->quota_type === 'weekly' ? 'minggu' : 'bulan' }}</span></div>
        <div><span class="text-gray-500 w-40 inline-block">Status</span>
            <span class="font-semibold {{ $participant->status === 'active' ? 'text-emerald-600' : 'text-gray-500' }}">
                : {{ $participant->status === 'active' ? 'Aktif' : 'Selesai' }}
            </span>
        </div>
    </div>

    {{-- Per-period table --}}
    <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-3">Rekap per Periode</h2>
    <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden mb-6">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Ke-</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Tipe</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Rentang</th>
                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Target</th>
                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Hadir</th>
                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Hasil</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($participant->attendancePeriods->sortBy('period_start') as $i => $period)
                <tr class="{{ $period->isFulfilled() ? '' : 'bg-red-50/40' }}">
                    <td class="px-4 py-2 text-xs text-gray-500">{{ $i + 1 }}</td>
                    <td class="px-4 py-2 text-sm text-gray-600">{{ ucfirst($period->period_type) }}</td>
                    <td class="px-4 py-2 text-sm text-gray-700">
                        {{ $period->period_start->format('d/m/Y') }} — {{ $period->period_end->format('d/m/Y') }}
                    </td>
                    <td class="px-4 py-2 text-center text-sm">{{ $period->target_count }}</td>
                    <td class="px-4 py-2 text-center text-sm font-semibold {{ $period->isFulfilled() ? 'text-emerald-600' : 'text-red-600' }}">
                        {{ $period->attended_count }}
                    </td>
                    <td class="px-4 py-2 text-center">
                        @if ($period->isFulfilled())
                            <span class="text-xs font-semibold text-emerald-600">PATUH</span>
                        @elseif ($period->hasEnded())
                            <span class="text-xs font-semibold text-red-600">MANGKIR</span>
                        @else
                            <span class="text-xs font-semibold text-blue-600">BERJALAN</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-4 text-center text-sm text-gray-400">Tidak ada periode.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- All attendance logs --}}
    <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-3">Riwayat Absensi</h2>
    @foreach ($participant->attendancePeriods->sortBy('period_start') as $period)
        @if ($period->attendanceLogs->isNotEmpty())
        <p class="text-xs font-semibold text-gray-500 uppercase mb-2 mt-4">
            Periode {{ $period->period_start->format('d/m/Y') }} — {{ $period->period_end->format('d/m/Y') }}
        </p>
        <table class="w-full text-sm border border-gray-100 mb-4">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Tanggal</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Waktu</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Lokasi</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Metode</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($period->attendanceLogs->sortBy('attendance_date') as $log)
                    <tr class="{{ $log->status === 'manual_override' ? 'bg-amber-50' : '' }}">
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($log->attendance_date)->translatedFormat('d M Y') }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $log->attendance_time }}</td>
                        <td class="px-4 py-2 text-gray-600">{{ $log->location?->name ?? '—' }}</td>
                        <td class="px-4 py-2">
                            {{ $log->status === 'manual_override' ? 'Input Manual' : 'Normal' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    @endforeach

    {{-- Signature footer (print only) --}}
    <div class="mt-10 grid grid-cols-2 gap-8 border-t border-gray-200 pt-6">
        <div class="text-sm">
            <p class="text-gray-500 mb-12">Peserta,</p>
            <div class="border-t border-gray-400 pt-1">
                <p class="font-medium text-gray-800">{{ $participant->full_name }}</p>
                <p class="text-gray-500 text-xs">NIK: {{ $participant->nik }}</p>
            </div>
        </div>
        <div class="text-sm">
            <p class="text-gray-500 mb-12">Petugas Pengawas,</p>
            <div class="border-t border-gray-400 pt-1">
                <p class="font-medium text-gray-800">{{ $participant->assignedAdmin?->name ?? '________________________' }}</p>
                <p class="text-gray-500 text-xs">Polrestabes Semarang</p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('head')
<style>
@media print {
    .no-print { display: none !important; }
    aside { display: none !important; }
    header { display: none !important; }
    main { padding: 0 !important; }
    body { background: white !important; font-size: 12px; }
    .print-doc { border: none !important; box-shadow: none !important; }
}
</style>
@endpush
