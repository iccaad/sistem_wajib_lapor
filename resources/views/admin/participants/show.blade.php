<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.participants.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </a>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Detail Peserta
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.participants.edit', $participant) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                    </svg>
                    Edit
                </a>
                @if ($participant->status === 'active')
                    <form method="POST" action="{{ route('admin.participants.destroy', $participant) }}"
                          onsubmit="return confirm('Yakin ingin menonaktifkan peserta ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                            Nonaktifkan
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Success Message --}}
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-lg flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <p class="text-sm text-emerald-700">{{ session('success') }}</p>
                </div>
            @endif

            {{-- Profile Card --}}
            <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-white">{{ $participant->full_name }}</h3>
                            <p class="text-indigo-200 text-sm font-mono mt-0.5">NIK: {{ $participant->nik }}</p>
                        </div>
                        @if ($participant->status === 'active')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-100 border border-emerald-400/30">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-500/20 text-red-100 border border-red-400/30">
                                <span class="w-2 h-2 rounded-full bg-red-400"></span>
                                Nonaktif
                            </span>
                        @endif
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs font-medium text-gray-500 uppercase">Alamat</p>
                            <p class="text-sm text-gray-800 mt-1">{{ $participant->address ?? '-' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs font-medium text-gray-500 uppercase">Telepon</p>
                            <p class="text-sm text-gray-800 mt-1">{{ $participant->phone ?? '-' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs font-medium text-gray-500 uppercase">Admin Pengawas</p>
                            <p class="text-sm text-gray-800 mt-1">{{ $participant->assignedAdmin->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stats Row --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Pelanggaran --}}
                <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Pelanggaran</p>
                            <p class="text-sm font-semibold text-gray-800">{{ $participant->violation_type }}</p>
                        </div>
                    </div>
                </div>

                {{-- Kuota --}}
                <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Kuota</p>
                            <p class="text-sm font-semibold text-gray-800">{{ $participant->quota_amount }}/{{ $participant->quota_type === 'weekly' ? 'minggu' : 'bulan' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Kehadiran --}}
                <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Total Kehadiran</p>
                            <p class="text-sm font-semibold text-gray-800">{{ $participant->attendanceLogs->count() }} kali</p>
                        </div>
                    </div>
                </div>

                {{-- Peringatan --}}
                <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Peringatan</p>
                            <p class="text-sm font-semibold text-gray-800">{{ $participant->warnings->count() }} peringatan</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Supervision Details --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Periode Pengawasan --}}
                <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6">
                    <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Periode Pengawasan</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Mulai</span>
                            <span class="text-sm font-medium text-gray-800">{{ $participant->supervision_start->format('d F Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Selesai</span>
                            <span class="text-sm font-medium text-gray-800">{{ $participant->supervision_end->format('d F Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Sisa Hari</span>
                            <span class="text-sm font-semibold {{ $participant->getRemainingDays() > 30 ? 'text-emerald-600' : ($participant->getRemainingDays() > 7 ? 'text-amber-600' : 'text-red-600') }}">
                                {{ $participant->getRemainingDays() }} hari
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Catatan Kasus --}}
                <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6">
                    <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Catatan Kasus</h4>
                    <p class="text-sm text-gray-700 leading-relaxed">
                        {{ $participant->case_notes ?? 'Tidak ada catatan.' }}
                    </p>
                </div>
            </div>

            {{-- Attendance Periods Table --}}
            @if ($participant->attendancePeriods->isNotEmpty())
            <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Periode Kehadiran</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Periode</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Target</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Hadir</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($participant->attendancePeriods->sortByDesc('period_start') as $period)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 text-sm text-gray-600">{{ ucfirst($period->period_type) }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-600">
                                        {{ $period->period_start->format('d/m/Y') }} — {{ $period->period_end->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-600">{{ $period->target_count }}</td>
                                    <td class="px-6 py-3 text-sm font-medium {{ $period->isFulfilled() ? 'text-emerald-600' : 'text-red-600' }}">
                                        {{ $period->attended_count }}
                                    </td>
                                    <td class="px-6 py-3">
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

        </div>
    </div>
</x-app-layout>
