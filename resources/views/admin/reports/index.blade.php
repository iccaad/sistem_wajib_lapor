@extends('layouts.admin')

@section('title', 'Laporan Kepatuhan')
@section('page-title', 'Laporan Kepatuhan')
@section('breadcrumb', 'Rekap kepatuhan seluruh peserta wajib lapor')

@section('content')

{{-- Filter + Print --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5 no-print">
    <form method="GET" action="{{ route('admin.reports.index') }}"
          class="flex flex-wrap gap-2">
        <input type="text" name="violation_type" value="{{ request('violation_type') }}"
               placeholder="Filter pelanggaran..."
               class="px-3 py-2 text-sm border border-gray-600 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
        <select name="status" class="px-3 py-2 pr-10 text-sm border border-gray-600 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">Semua Status</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Aktif</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
        </select>
        <button type="submit"
                class="px-4 py-2 text-sm font-medium text-gray-300 bg-gray-800 border border-gray-600 rounded-md hover:bg-gray-900 transition">
            Filter
        </button>
        @if(request('violation_type') || request('status'))
            <a href="{{ route('admin.reports.index') }}"
               class="px-3 py-2 text-sm text-gray-400 hover:text-gray-400 border border-gray-700 rounded-md hover:bg-gray-900 transition">
                Reset
            </a>
        @endif
    </form>

    <button onclick="window.print()"
            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-800 hover:bg-gray-950 text-white text-sm font-medium rounded-md shadow-md border-t-2 border-indigo-500 shadow-gray-950/50 transition">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.056 48.056 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
        </svg>
        Cetak Laporan
    </button>
</div>

{{-- Print header (hidden on screen) --}}
<div class="print-only hidden mb-6">
    <h1 class="text-xl font-bold text-center">LAPORAN KEPATUHAN PESERTA WAJIB LAPOR</h1>
    <p class="text-center text-sm mt-1">Polrestabes Semarang — Dicetak {{ now()->translatedFormat('d M Y, H:i') }} WIB</p>
</div>

<div class="bg-gray-800 rounded-md border border-gray-700 shadow-md border-t-2 border-indigo-500 shadow-gray-950/50 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-700">
            <thead class="bg-gray-900">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Nama</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase hidden sm:table-cell">NIK</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase hidden md:table-cell">Pelanggaran</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Status</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-400 uppercase">Periode</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-400 uppercase">Total Hadir</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-400 uppercase">Target</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-400 uppercase">Kepatuhan</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase no-print">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @forelse ($participants as $p)
                    <tr class="hover:bg-gray-900">
                        <td class="px-5 py-4 text-sm font-medium text-gray-100">{{ $p->full_name }}</td>
                        <td class="px-5 py-4 text-xs text-gray-400 font-mono hidden sm:table-cell">{{ $p->nik }}</td>
                        <td class="px-5 py-4 text-sm text-gray-400 hidden md:table-cell">{{ Str::limit($p->violation_type, 20) }}</td>
                        <td class="px-5 py-4">
                            @if ($p->status === 'active')
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-400">Aktif</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-gray-800 text-gray-400">Selesai</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center text-sm text-gray-300">{{ $p->total_periods }}</td>
                        <td class="px-5 py-4 text-center text-sm font-semibold {{ $p->total_attended >= $p->total_target ? 'text-emerald-400' : 'text-gray-300' }}">
                            {{ $p->total_attended }}
                        </td>
                        <td class="px-5 py-4 text-center text-sm text-gray-300">{{ $p->total_target }}</td>
                        <td class="px-5 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full {{ $p->compliance_percent >= 80 ? 'bg-emerald-500/200' : ($p->compliance_percent >= 50 ? 'bg-amber-500/200' : 'bg-red-500/200') }}"
                                         style="width: {{ min(100, $p->compliance_percent) }}%"></div>
                                </div>
                                <span class="text-xs font-semibold {{ $p->compliance_percent >= 80 ? 'text-emerald-400' : ($p->compliance_percent >= 50 ? 'text-amber-400' : 'text-red-400') }}">
                                    {{ $p->compliance_percent }}%
                                </span>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-right no-print">
                            <a href="{{ route('admin.reports.show', $p) }}"
                               class="text-xs font-medium text-indigo-400 hover:text-indigo-800 transition">
                                Lihat Detail →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-sm text-gray-400">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($participants->hasPages())
        <div class="px-5 py-3 bg-gray-900 border-t border-gray-700 no-print">
            {{ $participants->links() }}
        </div>
    @endif
</div>

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
    .bg-gray-800 { box-shadow: none !important; border: 1px solid #e5e7eb !important; }
}
</style>
@endpush


