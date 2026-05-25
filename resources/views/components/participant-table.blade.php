{{--
    Reusable participant table.
    @param \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection $participants – Collection/Paginator of participants
    @param bool   $showDeactivate – Whether to show the deactivation button/form (default: false)
    @param string $emptyText – Text to display when there are no participants (default: 'Belum ada peserta terdaftar.')
    @param string|null $title – Box title (optional)
    @param string|null $headerAction – Box header actions HTML/link (optional)
--}}
@php
    $showDeactivate = $showDeactivate ?? false;
    $emptyText = $emptyText ?? 'Belum ada peserta terdaftar.';
@endphp

<div class="bg-white rounded-2xl border border-brand-light shadow-sm overflow-hidden">
    @if(isset($title))
        <div class="flex items-center justify-between px-6 py-5 border-b border-brand-light/50 bg-brand-light/5">
            <h2 class="text-sm font-bold text-brand-primary uppercase tracking-wider">{{ $title }}</h2>
            @if(isset($headerAction))
                {!! $headerAction !!}
            @endif
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-brand-light/10">
                <tr>
                    <th class="px-5 py-4 text-left text-[10px] font-black text-brand-secondary uppercase tracking-[0.1em]">Nama</th>
                    <th class="px-5 py-4 text-left text-[10px] font-black text-brand-secondary uppercase tracking-[0.1em] hidden md:table-cell">Admin</th>
                    <th class="px-5 py-4 text-left text-[10px] font-black text-brand-secondary uppercase tracking-[0.1em] hidden md:table-cell">Masa Pengawasan</th>
                    <th class="px-5 py-4 text-center text-[10px] font-black text-brand-secondary uppercase tracking-[0.1em] hidden md:table-cell">Jumlah Periode</th>
                    <th class="px-5 py-4 text-center text-[10px] font-black text-brand-secondary uppercase tracking-[0.1em] hidden md:table-cell">Kuota per Periode</th>
                    <th class="px-5 py-4 text-left text-[10px] font-black text-brand-secondary uppercase tracking-[0.1em]">Periode Ini</th>
                    <th class="px-5 py-4 text-right text-[10px] font-black text-brand-secondary uppercase tracking-[0.1em]">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($participants as $p)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg {{ $p->status === 'active' ? 'bg-brand-light text-brand-secondary' : 'bg-brand-light/30 text-brand-soft' }} text-xs font-black uppercase shadow-sm">
                                    {{ substr($p->full_name, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-black text-brand-primary leading-none truncate">{{ $p->full_name }}</p>
                                    <p class="text-[10px] text-brand-secondary mt-1 uppercase font-black tracking-tight opacity-70">{{ $p->nik }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 hidden md:table-cell">
                            <span class="text-sm text-gray-500 font-medium">{{ $p->assignedAdmin?->name ?? '—' }}</span>
                        </td>
                        <td class="px-5 py-4 hidden md:table-cell">
                            <div class="text-xs text-gray-500 font-medium leading-5">
                                <div>{{ $p->supervision_start ? $p->supervision_start->format('d/m/Y') : '—' }}</div>
                                <div>s/d {{ $p->supervision_end ? $p->supervision_end->format('d/m/Y') : '—' }}</div>
                            </div>
                        </td>
                        <td class="px-5 py-4 hidden md:table-cell text-center">
                            <span class="text-sm text-gray-700 font-medium">{{ $p->attendancePeriods->count() ?: '—' }}</span>
                        </td>
                        <td class="px-5 py-4 hidden md:table-cell text-center">
                            <span class="text-sm text-gray-700 font-medium">{{ $p->quota_amount }}×/{{ $p->quota_type === 'weekly' ? 'Minggu' : 'Bulan' }}</span>
                        </td>
                        <td class="px-5 py-4">
                            @php
                                $currentPeriod = $p->attendancePeriods
                                    ->first(fn($per) => today()->between($per->period_start, $per->period_end))
                                    ?? $p->attendancePeriods->where('period_start', '<=', today())->sortByDesc('period_start')->first()
                                    ?? $p->attendancePeriods->first();
                            @endphp
                            @if ($currentPeriod)
                                <div class="flex items-center gap-2 max-w-[120px]">
                                    <div class="flex-1 bg-brand-light rounded-full h-1.5">
                                        <div class="h-1.5 rounded-full {{ $currentPeriod->isFulfilled() ? 'bg-emerald-500' : 'bg-brand-accent' }}"
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
                        <td class="px-5 py-4 text-right">
                            @if($showDeactivate)
                                {{-- Hidden DELETE form for deactivation --}}
                                <form id="deactivate-form-{{ $p->id }}"
                                      method="POST" action="{{ route('admin.participants.destroy', $p) }}"
                                      class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @endif

                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.participants.show', $p) }}"
                                   class="p-2 rounded-lg text-brand-accent hover:bg-brand-accent/10 transition-all duration-200"
                                   title="Detail">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </a>
                                <a href="{{ route('admin.participants.edit', $p) }}"
                                   class="p-2 rounded-lg text-orange-500 hover:bg-orange-50 transition-all duration-200"
                                   title="Edit">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </a>
                                @if($showDeactivate)
                                    @if($p->status === 'active')
                                        <button type="button"
                                                @click="openConfirm('{{ addslashes($p->full_name) }}', {{ $p->id }})"
                                                class="p-2 rounded-lg text-red-500 hover:bg-red-50 transition-all duration-200"
                                                title="Nonaktifkan">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1 0 12.728 12.728M12 3v9" />
                                            </svg>
                                        </button>
                                    @else
                                        <div class="p-2 text-gray-300" title="Sudah Nonaktif">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                                            </svg>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center text-sm text-gray-400">
                            <svg class="h-10 w-10 mx-auto mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                            </svg>
                            {{ $emptyText }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($participants instanceof \Illuminate\Contracts\Pagination\Paginator && $participants->hasPages())
        <div class="px-6 py-4 border-t border-brand-light/50 no-print">
            {{ $participants->withQueryString()->links() }}
        </div>
    @endif
</div>
