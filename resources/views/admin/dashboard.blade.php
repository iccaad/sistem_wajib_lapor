@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Sistem Wajib Lapor Digital — Polrestabes Semarang')

@section('content')

{{-- ── Stat Cards ── --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">

    {{-- Total Aktif --}}
    <div class="col-span-1 bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-50">
                <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $totalActive }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Peserta Aktif</p>
            </div>
        </div>
    </div>

    {{-- Patuh --}}
    <div class="col-span-1 bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-50">
                <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-emerald-600">{{ $totalCompliant }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Peserta Patuh</p>
            </div>
        </div>
    </div>

    {{-- Berisiko --}}
    <div class="col-span-1 bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-50">
                <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-amber-600">{{ $totalAtRisk }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Berisiko</p>
            </div>
        </div>
    </div>

    {{-- Mangkir --}}
    <div class="col-span-1 bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-red-50">
                <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-red-600">{{ $totalAbsent }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Mangkir</p>
            </div>
        </div>
    </div>

    {{-- Selesai Segera --}}
    <div class="col-span-1 bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gray-100">
                <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-700">{{ $endingSoon }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Selesai 7 Hari</p>
            </div>
        </div>
    </div>

</div>

{{-- ── Recent Participants Table ── --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Peserta Terbaru</h2>
        <a href="{{ route('admin.participants.index') }}"
           class="text-xs text-indigo-600 hover:text-indigo-800 font-medium transition">
            Lihat Semua →
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">NIK</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Pelanggaran</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Masa Pengawasan</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Periode Ini</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($recentParticipants as $p)
                    @php
                        $currentPeriod = $p->attendancePeriods
                            ->first(fn($per) => today()->between($per->period_start, $per->period_end))
                            ?? $p->attendancePeriods->where('period_start', '<=', today())->sortByDesc('period_start')->first()
                            ?? $p->attendancePeriods->first();
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full
                                            {{ $p->status === 'active' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-400' }}
                                            text-xs font-bold uppercase">
                                    {{ substr($p->full_name, 0, 1) }}
                                </div>
                                <div class="text-sm font-medium {{ $p->hasCompletedAllPeriods() ? 'text-green-600' : 'text-gray-900' }}">{{ $p->full_name }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 hidden sm:table-cell">
                            <span class="text-sm text-gray-500 font-mono">{{ $p->nik }}</span>
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell">
                            <span class="text-sm text-gray-600">{{ Str::limit($p->violationType->name ?? '—', 22) }}</span>
                        </td>
                        <td class="px-6 py-4 hidden lg:table-cell">
                            <div class="text-xs text-gray-500">
                                {{ $p->supervision_start->format('d/m/Y') }} –
                                {{ $p->supervision_end->format('d/m/Y') }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if ($currentPeriod)
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-200 rounded-full h-1.5 min-w-[60px]">
                                        <div class="h-1.5 rounded-full {{ $currentPeriod->isFulfilled() ? 'bg-emerald-500' : 'bg-indigo-500' }}"
                                             style="width: {{ min(100, round($currentPeriod->attended_count / max(1, $currentPeriod->target_count) * 100)) }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium {{ $currentPeriod->isFulfilled() ? 'text-emerald-600' : 'text-gray-600' }} whitespace-nowrap">
                                        {{ $currentPeriod->attended_count }}/{{ $currentPeriod->target_count }}
                                    </span>
                                </div>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.participants.show', $p) }}"
                               class="inline-flex items-center gap-1 text-xs font-medium text-indigo-600 hover:text-indigo-800 transition">
                                Detail
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-400">
                            Belum ada peserta terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
