@extends('layouts.admin')

@section('title', 'Laporan Kepatuhan')
@section('page-title', 'Laporan Kepatuhan')
@section('breadcrumb', 'Rekap kepatuhan seluruh peserta wajib lapor')

@section('content')

{{-- Filter + Print --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5 no-print">
    <form method="GET" action="{{ route('admin.reports.index') }}"
          class="flex flex-wrap gap-2">
        <select name="violation_type_id" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">Semua Pelanggaran</option>
            @foreach($violationTypes as $vt)
                <option value="{{ $vt->id }}" {{ request('violation_type_id') == $vt->id ? 'selected' : '' }}>
                    {{ $vt->name }}
                </option>
            @endforeach
        </select>
        <select name="status" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">Semua Status</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Aktif</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
        </select>
        <button type="submit"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
            Filter
        </button>
        @if(request('violation_type_id') || request('status'))
            <a href="{{ route('admin.reports.index') }}"
               class="px-3 py-2 text-sm text-gray-400 hover:text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                Reset
            </a>
        @endif
    </form>

    <button onclick="window.print()"
            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg shadow-sm transition">
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

<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden sm:table-cell">NIK</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Tipe Kuota</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Periode</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Total Hadir</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Target</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Kepatuhan</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase no-print">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($participants as $p)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4 text-sm font-medium text-gray-900">{{ $p->full_name }}</td>
                        <td class="px-5 py-4 text-xs text-gray-500 font-mono hidden sm:table-cell">{{ $p->nik }}</td>
                        <td class="px-5 py-4 text-sm text-gray-600 hidden md:table-cell">{{ $p->quota_amount }}×/{{ $p->quota_type === 'weekly' ? 'minggu' : 'bulan' }}</td>
                        <td class="px-5 py-4">
                            @if ($p->status === 'active')
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Aktif</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Selesai</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center text-sm text-gray-700">{{ $p->total_periods }}</td>
                        <td class="px-5 py-4 text-center text-sm font-semibold {{ $p->total_attended >= $p->total_target ? 'text-emerald-600' : 'text-gray-700' }}">
                            {{ $p->total_attended }}
                        </td>
                        <td class="px-5 py-4 text-center text-sm text-gray-700">{{ $p->total_target }}</td>
                        <td class="px-5 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full {{ $p->compliance_percent >= 80 ? 'bg-emerald-500' : ($p->compliance_percent >= 50 ? 'bg-amber-500' : 'bg-red-500') }}"
                                         style="width: {{ min(100, $p->compliance_percent) }}%"></div>
                                </div>
                                <span class="text-xs font-semibold {{ $p->compliance_percent >= 80 ? 'text-emerald-600' : ($p->compliance_percent >= 50 ? 'text-amber-600' : 'text-red-600') }}">
                                    {{ $p->compliance_percent }}%
                                </span>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-right no-print">
                            <a href="{{ route('admin.reports.show', $p) }}"
                               class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition">
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
        <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 no-print">
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
    .bg-white { box-shadow: none !important; border: 1px solid #e5e7eb !important; }
}
</style>
@endpush
