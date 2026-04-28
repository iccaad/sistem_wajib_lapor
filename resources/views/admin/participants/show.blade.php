@extends('layouts.admin')

@section('title', 'Detail Peserta — ' . $participant->full_name)
@section('page-title', 'Detail Peserta')
@section('breadcrumb', 'Admin / Peserta / ' . $participant->full_name)

@section('content')

<div class="space-y-5" x-data="{ photoModalOpen: false, photoUrl: '' }">

    {{-- ── Error messages ── --}}
    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4">
            <p class="text-sm font-semibold text-red-700 mb-1">Terdapat kesalahan:</p>
            <ul class="list-disc list-inside text-sm text-red-600 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ── Profile Card ── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-5">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-white">{{ $participant->full_name }}</h3>
                    <p class="text-indigo-200 text-sm font-mono mt-0.5">NIK: {{ $participant->nik }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.participants.edit', $participant) }}"
                       id="btn-edit-peserta"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white text-xs font-medium rounded-lg border border-white/30 transition">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Z" />
                        </svg>
                        Edit
                    </a>
                    @if ($participant->status === 'active')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-500/20 text-emerald-100 border border-emerald-400/30">
                            <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span> Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-500/20 text-gray-200 border border-gray-400/30">
                            <span class="h-2 w-2 rounded-full bg-gray-400"></span> Nonaktif
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="p-6 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Pelanggaran</p>
                <p class="text-sm text-gray-800">{{ $participant->violation_type }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Kuota</p>
                <p class="text-sm text-gray-800">{{ $participant->quota_amount }}×/{{ $participant->quota_type === 'weekly' ? 'minggu' : 'bulan' }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Mulai</p>
                <p class="text-sm text-gray-800">{{ $participant->supervision_start->format('d M Y') }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Selesai</p>
                <p class="text-sm text-gray-800">{{ $participant->supervision_end->format('d M Y') }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Sisa Hari</p>
                <p class="text-sm font-semibold {{ $participant->getRemainingDays() > 30 ? 'text-emerald-600' : ($participant->getRemainingDays() > 7 ? 'text-amber-600' : 'text-red-600') }}">
                    {{ $participant->getRemainingDays() }} hari
                </p>
            </div>
        </div>

        @if ($participant->case_notes)
            <div class="px-6 pb-5">
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Catatan Kasus</p>
                <p class="text-sm text-gray-700 bg-gray-50 rounded-lg p-3">{{ $participant->case_notes }}</p>
            </div>
        @endif
    </div>

    {{-- ── Assigned Locations ── --}}
    @if ($participant->locations->isNotEmpty())
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                    </svg>
                    Lokasi Wajib Lapor ({{ $participant->locations->count() }})
                </h3>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach ($participant->locations as $loc)
                    <div class="px-6 py-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold">
                                {{ $loc->pivot->check_in_order }}
                            </span>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $loc->name }}</p>
                                <p class="text-xs text-gray-400">{{ $loc->address ?? '—' }} • radius ±{{ $loc->radius_meters }}m</p>
                            </div>
                        </div>
                        @if ($loc->is_active)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span> Nonaktif
                            </span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ── Stat Row ── --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @php
            $totalAttended = $participant->attendanceLogs->count();
            $totalTarget   = $participant->attendancePeriods->sum('target_count');
            $activeWarnings = $participant->warnings->where('status', 'active')->count();
        @endphp
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-indigo-600">{{ $participant->attendancePeriods->count() }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Total Periode</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-emerald-600">{{ $totalAttended }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Total Hadir</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-gray-700">{{ $totalTarget > 0 ? round($totalAttended / $totalTarget * 100) : 0 }}%</p>
            <p class="text-xs text-gray-500 mt-0.5">Kepatuhan</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold {{ $activeWarnings > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ $activeWarnings }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Peringatan Aktif</p>
        </div>
    </div>

    {{-- ── Periods Table ── --}}
    @if ($participant->attendancePeriods->isNotEmpty())
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Periode Kehadiran</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Periode</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Rentang</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Target</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Hadir</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Progress</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($participant->attendancePeriods->sortByDesc('period_start') as $period)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 text-sm text-gray-600">{{ ucfirst($period->period_type) }}</td>
                            <td class="px-5 py-3 text-xs text-gray-500">
                                {{ $period->period_start->format('d/m/Y') }} — {{ $period->period_end->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ $period->target_count }}</td>
                            <td class="px-5 py-3 text-sm font-semibold {{ $period->isFulfilled() ? 'text-emerald-600' : 'text-gray-800' }}">
                                {{ $period->attended_count }}
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-24 bg-gray-200 rounded-full h-1.5">
                                        <div class="h-1.5 rounded-full {{ $period->isFulfilled() ? 'bg-emerald-500' : 'bg-indigo-500' }}"
                                             style="width: {{ min(100, round($period->attended_count / max(1, $period->target_count) * 100)) }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-400">{{ round($period->attended_count / max(1, $period->target_count) * 100) }}%</span>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                @if ($period->isFulfilled())
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Terpenuhi</span>
                                @elseif ($period->status === 'completed')
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Kurang</span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Berjalan</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ── Attendance Logs ── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Riwayat Absensi</h4>
            <span class="text-xs text-gray-400">{{ $participant->attendanceLogs->count() }} catatan</span>
        </div>
        @if ($participant->attendanceLogs->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Lokasi</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Foto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($participant->attendanceLogs->sortByDesc('attendance_date') as $log)
                        <tr class="hover:bg-gray-50 {{ $log->status === 'manual_override' ? 'bg-amber-50/40' : '' }}">
                            <td class="px-5 py-3 text-sm text-gray-800">
                                {{ \Carbon\Carbon::parse($log->attendance_date)->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-5 py-3 text-sm text-gray-500">{{ $log->attendance_time }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ $log->location?->name ?? '—' }}</td>
                            <td class="px-5 py-3">
                                @if ($log->status === 'manual_override')
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Input Manual</span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Normal</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                @if ($log->photo_path)
                                    <button type="button" @click="photoUrl = '{{ route('admin.attendance.photo', $log) }}'; photoModalOpen = true"
                                       class="text-xs text-indigo-600 hover:text-indigo-800 font-medium transition">
                                        Lihat Foto &rarr;
                                    </button>
                                @else
                                    <span class="text-xs text-gray-300">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <p class="px-6 py-8 text-sm text-gray-400 text-center">Belum ada catatan absensi.</p>
        @endif
    </div>

    {{-- ── Override Manual Form ── --}}
    @if ($participant->status === 'active' && $participant->isActive())
    <div class="bg-white rounded-xl border border-amber-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-amber-100 bg-amber-50">
            <h4 class="text-sm font-semibold text-amber-800 uppercase tracking-wider">Override Absensi Manual</h4>
            <p class="text-xs text-amber-600 mt-1">Gunakan hanya untuk kondisi khusus. Alasan wajib diisi minimal 10 karakter.</p>
        </div>
        <form method="POST"
              action="{{ route('admin.attendance.override', $participant) }}"
              class="p-6">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="attendance_date" class="block text-sm font-medium text-gray-700 mb-1">
                        Tanggal Absensi <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="attendance_date" name="attendance_date"
                           value="{{ old('attendance_date', today()->toDateString()) }}"
                           max="{{ today()->toDateString() }}"
                           required
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 @error('attendance_date') border-red-400 @enderror">
                    @error('attendance_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="override_reason" class="block text-sm font-medium text-gray-700 mb-1">
                        Alasan Override <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="override_reason" name="override_reason"
                           value="{{ old('override_reason') }}"
                           placeholder="Contoh: Sinyal GPS bermasalah di lokasi saat itu"
                           required minlength="10"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 @error('override_reason') border-red-400 @enderror">
                    @error('override_reason') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <button type="submit" id="btn-override-absensi"
                        onclick="return confirm('Yakin ingin menambahkan absensi manual? Tindakan ini tidak dapat dibatalkan.')"
                        class="px-4 py-2 text-sm font-medium text-white bg-amber-500 hover:bg-amber-600 rounded-lg shadow-sm transition">
                    Tambahkan Absensi Manual
                </button>
            </div>
        </form>
    </div>
    @endif

    {{-- ── Warnings ── --}}
    @if ($participant->warnings->isNotEmpty())
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Riwayat Peringatan</h4>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach ($participant->warnings->sortByDesc('issued_at') as $warning)
                <div class="px-6 py-4 flex items-start gap-4">
                    <span class="inline-flex shrink-0 px-2 py-0.5 rounded-full text-xs font-bold mt-0.5
                                 {{ match($warning->level) {
                                    'level_1' => 'bg-amber-100 text-amber-700',
                                    'level_2' => 'bg-red-100 text-red-700',
                                    'level_3' => 'bg-red-200 text-red-900',
                                    default   => 'bg-gray-100 text-gray-600',
                                } }}">
                        {{ strtoupper(str_replace('_', ' ', $warning->level)) }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-800">{{ $warning->reason }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ \Carbon\Carbon::parse($warning->issued_at)->translatedFormat('d M Y, H:i') }}
                        </p>
                    </div>
                    <span class="shrink-0 inline-flex px-2 py-0.5 rounded-full text-xs font-medium
                                 {{ $warning->status === 'active' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $warning->status === 'active' ? 'Aktif' : 'Selesai' }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Danger Zone ── --}}
    @if ($participant->status === 'active')
    <div class="bg-white rounded-xl border border-red-200 shadow-sm p-5 flex items-center justify-between gap-4">
        <div>
            <p class="text-sm font-semibold text-gray-800">Nonaktifkan Peserta</p>
            <p class="text-xs text-gray-500 mt-0.5">Peserta tidak dapat login setelah dinonaktifkan. Riwayat absensi tetap tersimpan.</p>
        </div>
        <form method="POST" action="{{ route('admin.participants.destroy', $participant) }}"
              onsubmit="return confirm('Yakin ingin menonaktifkan peserta ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" id="btn-nonaktifkan-peserta"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-500 hover:bg-red-600 rounded-lg shadow-sm transition">
                Nonaktifkan
            </button>
        </form>
    </div>
    @endif

    {{-- Photo Modal --}}
    <div x-show="photoModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" x-transition.opacity style="display: none;">
        <div class="relative bg-white rounded-xl shadow-2xl max-w-lg w-full overflow-hidden" @click.outside="photoModalOpen = false">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-800">Preview Foto</h3>
                <button @click="photoModalOpen = false" type="button" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4 flex justify-center bg-gray-100">
                <img :src="photoUrl" class="max-h-[60vh] object-contain rounded-lg shadow-sm" alt="Foto Absensi" />
            </div>
        </div>
    </div>

</div>

@endsection
