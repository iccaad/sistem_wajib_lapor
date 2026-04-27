@extends('layouts.participant')

@section('title', 'Riwayat Absensi')

@section('content')

{{-- Header info --}}
<div class="mb-5">
    <h2 class="text-lg font-bold text-slate-800">Riwayat Absensi</h2>
    <p class="text-sm text-slate-500 mt-0.5">Semua catatan kehadiran selama masa pengawasan.</p>
</div>

{{-- Summary strip --}}
@php
    $totalLogged  = $periods->sum(fn($p) => $p->attendanceLogs->count());
    $totalTarget  = $periods->sum('target_count');
    $compliance   = $totalTarget > 0 ? round($totalLogged / $totalTarget * 100) : 0;
    $metPeriods   = $periods->filter(fn($p) => $p->isFulfilled())->count();
@endphp

<div class="grid grid-cols-3 gap-3 mb-5">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 text-center">
        <p class="text-2xl font-bold text-blue-600">{{ $totalLogged }}</p>
        <p class="text-xs text-slate-500 mt-0.5">Total Hadir</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 text-center">
        <p class="text-2xl font-bold text-emerald-600">{{ $compliance }}%</p>
        <p class="text-xs text-slate-500 mt-0.5">Kepatuhan</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 text-center">
        <p class="text-2xl font-bold text-indigo-600">{{ $metPeriods }}/{{ $periods->count() }}</p>
        <p class="text-xs text-slate-500 mt-0.5">Periode Patuh</p>
    </div>
</div>

{{-- Per-period grouped list --}}
@forelse ($periods as $period)
    @php
        $attended = $period->attendanceLogs->count();
        $pct      = min(100, round($attended / max(1, $period->target_count) * 100));
        $fulfilled = $period->isFulfilled();
        $ended     = $period->hasEnded();
        $active    = !$ended;
    @endphp

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-4">
        {{-- Period header --}}
        <div class="px-5 py-4 border-b border-slate-100">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                        {{ ucfirst($period->period_type) }}
                    </p>
                    <p class="text-sm text-slate-600 mt-0.5">
                        {{ $period->period_start->format('d/m/Y') }} — {{ $period->period_end->format('d/m/Y') }}
                    </p>
                </div>
                @if ($fulfilled)
                    <span class="shrink-0 inline-flex px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                        ✓ Patuh
                    </span>
                @elseif ($ended)
                    <span class="shrink-0 inline-flex px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-600">
                        Mangkir
                    </span>
                @else
                    <span class="shrink-0 inline-flex px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                        Berjalan
                    </span>
                @endif
            </div>

            {{-- Progress bar --}}
            <div class="mt-3">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs text-slate-500">{{ $attended }} / {{ $period->target_count }} kehadiran</span>
                    <span class="text-xs font-semibold {{ $fulfilled ? 'text-emerald-600' : 'text-slate-500' }}">{{ $pct }}%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                    <div class="h-2 rounded-full {{ $fulfilled ? 'bg-emerald-500' : ($ended ? 'bg-red-400' : 'bg-blue-500') }}"
                         style="width: {{ $pct }}%"></div>
                </div>
            </div>
        </div>

        {{-- Logs --}}
        @if ($period->attendanceLogs->isNotEmpty())
            <div class="divide-y divide-slate-50">
                @foreach ($period->attendanceLogs as $log)
                    <div class="px-5 py-3 flex items-center gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full
                                    {{ $log->status === 'manual_override' ? 'bg-amber-100' : 'bg-emerald-100' }}">
                            @if ($log->status === 'manual_override')
                                <svg class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/>
                                </svg>
                            @else
                                <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-800">
                                {{ \Carbon\Carbon::parse($log->attendance_date)->translatedFormat('l, d M Y') }}
                            </p>
                            <p class="text-xs text-slate-400">
                                {{ $log->attendance_time }}
                                @if ($log->location)
                                    · {{ $log->location->name }}
                                @endif
                            </p>
                        </div>
                        <div class="shrink-0">
                            @if ($log->status === 'manual_override')
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Manual</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Hadir</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="px-5 py-5 text-center text-sm text-slate-400">
                @if ($active)
                    Belum ada absensi untuk periode ini.
                @else
                    Tidak ada catatan absensi pada periode ini.
                @endif
            </div>
        @endif
    </div>
@empty
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-5 py-12 text-center">
        <svg class="h-12 w-12 text-slate-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
        </svg>
        <p class="text-slate-500 font-medium">Belum ada riwayat absensi.</p>
    </div>
@endforelse

@endsection
