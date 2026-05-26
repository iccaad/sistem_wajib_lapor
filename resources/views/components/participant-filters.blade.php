{{--
    Reusable participant filter bar.
    @param string $route   – Named route for the form action
    @param \Illuminate\Database\Eloquent\Collection $admins – Admin users for dropdown
    @param bool   $showSearch – Whether to show the search input (default: false)
--}}
@php
    $showSearch = $showSearch ?? false;
    $hasActiveFilters = request('date_from') || request('date_to') || request('admin_id')
        || request('status_akun') || request('tingkat_kepatuhan') || request('progress_pengawasan');

    $kepatuhanLabels = [
        'patuh' => 'Patuh',
        'berisiko' => 'Berisiko',
        'mangkir' => 'Mangkir',
    ];
    $progressLabels = [
        'segera_selesai' => 'Segera Selesai',
        'selesai' => 'Selesai',
    ];
@endphp

<div class="bg-white rounded-2xl border border-brand-light/50 shadow-sm p-4 mb-5 no-print">
    <form method="GET" action="{{ route($route) }}" id="filter-form">
        {{-- Preserve existing query params that are NOT part of this filter --}}
        @foreach(request()->except(['date_from', 'date_to', 'admin_id', 'status_akun', 'tingkat_kepatuhan', 'progress_pengawasan', 'page', 'search']) as $key => $value)
            @if(!is_null($value) && $value !== '')
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach

        <div class="flex flex-col gap-3">
            {{-- Row 1: Filter label --}}
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4 text-brand-accent" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" />
                </svg>
                <span class="text-xs font-bold text-brand-secondary uppercase tracking-wider">Filter Data</span>
            </div>

            {{-- Row 2: Dynamic stat filters (3 dropdowns) --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                {{-- Status Akun --}}
                <div>
                    <label for="filter-status-akun" class="block text-xs font-medium text-gray-500 mb-1">Status Akun</label>
                    <select name="status_akun" id="filter-status-akun"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand-accent/20 focus:border-brand-accent bg-white transition-all duration-200 cursor-pointer">
                        <option value="">Semua</option>
                        <option value="aktif" {{ request('status_akun') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="tidak_aktif" {{ request('status_akun') === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>

                {{-- Tingkat Kepatuhan --}}
                <div>
                    <label for="filter-tingkat-kepatuhan" class="block text-xs font-medium text-gray-500 mb-1">Tingkat Kepatuhan</label>
                    <select name="tingkat_kepatuhan" id="filter-tingkat-kepatuhan"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand-accent/20 focus:border-brand-accent bg-white transition-all duration-200 cursor-pointer">
                        <option value="">Semua</option>
                        <option value="patuh" {{ request('tingkat_kepatuhan') === 'patuh' ? 'selected' : '' }}>Patuh</option>
                        <option value="berisiko" {{ request('tingkat_kepatuhan') === 'berisiko' ? 'selected' : '' }}>Berisiko</option>
                        <option value="mangkir" {{ request('tingkat_kepatuhan') === 'mangkir' ? 'selected' : '' }}>Mangkir</option>
                    </select>
                </div>

                {{-- Progress Pengawasan --}}
                <div>
                    <label for="filter-progress-pengawasan" class="block text-xs font-medium text-gray-500 mb-1">Progress Pengawasan</label>
                    <select name="progress_pengawasan" id="filter-progress-pengawasan"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand-accent/20 focus:border-brand-accent bg-white transition-all duration-200 cursor-pointer">
                        <option value="">Semua</option>
                        <option value="segera_selesai" {{ request('progress_pengawasan') === 'segera_selesai' ? 'selected' : '' }}>Segera Selesai</option>
                        <option value="selesai" {{ request('progress_pengawasan') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
            </div>

            {{-- Row 3: Date / Admin / Search filter inputs --}}
            <div class="flex flex-col sm:flex-row sm:items-end gap-3">

                {{-- Search (optional, used on participants index) --}}
                @if($showSearch)
                    <div class="flex-1 min-w-0 sm:max-w-xs">
                        <label for="filter-search" class="block text-xs font-medium text-gray-500 mb-1">Cari</label>
                        <input type="text" name="search" id="filter-search" value="{{ request('search') }}"
                               placeholder="Nama atau NIK..."
                               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand-accent/20 focus:border-brand-accent transition-all duration-200">
                    </div>
                @endif

                {{-- Date From --}}
                <div class="min-w-0 sm:w-44">
                    <label for="filter-date-from" class="block text-xs font-medium text-gray-500 mb-1">Dari Tanggal Dibuat</label>
                    <input type="date" name="date_from" id="filter-date-from" value="{{ request('date_from') }}"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand-accent/20 focus:border-brand-accent transition-all duration-200 cursor-pointer">
                </div>

                {{-- Date To --}}
                <div class="min-w-0 sm:w-44">
                    <label for="filter-date-to" class="block text-xs font-medium text-gray-500 mb-1">Sampai Tanggal Dibuat</label>
                    <input type="date" name="date_to" id="filter-date-to" value="{{ request('date_to') }}"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand-accent/20 focus:border-brand-accent transition-all duration-200 cursor-pointer">
                </div>

                {{-- Admin Dropdown --}}
                <div class="min-w-0 sm:w-52">
                    <label for="filter-admin-id" class="block text-xs font-medium text-gray-500 mb-1">Polsek / Satuan (Admin)</label>
                    <select name="admin_id" id="filter-admin-id"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand-accent/20 focus:border-brand-accent bg-white transition-all duration-200 cursor-pointer">
                        <option value="">Semua Admin</option>
                        @foreach($admins as $admin)
                            <option value="{{ $admin->id }}" {{ request('admin_id') == $admin->id ? 'selected' : '' }}>
                                {{ $admin->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Buttons --}}
                <div class="flex items-end gap-2 shrink-0">
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-bold text-white bg-brand-accent hover:bg-brand-accent/80 rounded-lg shadow-sm transition-all duration-200 transform active:scale-95">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                        Filter
                    </button>

                    @if($hasActiveFilters || request('search'))
                        <a href="{{ route($route) }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-bold text-brand-soft hover:text-red-600 border border-gray-200 rounded-lg hover:bg-red-50 transition-all duration-200">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Reset
                        </a>
                    @endif
                </div>
            </div>

            {{-- Active filter badges --}}
            @if($hasActiveFilters)
                <div class="flex flex-wrap items-center gap-2 pt-1">
                    <span class="text-xs text-gray-400">Filter aktif:</span>
                    @if(request('status_akun'))
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ request('status_akun') === 'aktif' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-red-50 text-red-700 border border-red-100' }}">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0" />
                            </svg>
                            Status: {{ request('status_akun') === 'aktif' ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    @endif
                    @if(request('tingkat_kepatuhan'))
                        @php
                            $kepatuhanColors = [
                                'patuh' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                'berisiko' => 'bg-amber-50 text-amber-700 border-amber-100',
                                'mangkir' => 'bg-red-50 text-red-700 border-red-100',
                            ];
                        @endphp
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium border {{ $kepatuhanColors[request('tingkat_kepatuhan')] ?? 'bg-gray-50 text-gray-700 border-gray-100' }}">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            Kepatuhan: {{ $kepatuhanLabels[request('tingkat_kepatuhan')] ?? request('tingkat_kepatuhan') }}
                        </span>
                    @endif
                    @if(request('progress_pengawasan'))
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-brand-light/30 text-brand-primary border border-brand-light/50">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5" />
                            </svg>
                            Progress: {{ $progressLabels[request('progress_pengawasan')] ?? request('progress_pengawasan') }}
                        </span>
                    @endif
                    @if(request('date_from'))
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5" />
                            </svg>
                            Dibuat Dari: {{ request('date_from') }}
                        </span>
                    @endif
                    @if(request('date_to'))
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5" />
                            </svg>
                            Dibuat Sampai: {{ request('date_to') }}
                        </span>
                    @endif
                    @if(request('admin_id'))
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0" />
                            </svg>
                            {{ $admins->firstWhere('id', request('admin_id'))?->name ?? 'Admin' }}
                        </span>
                    @endif
                </div>
            @endif
        </div>
    </form>
</div>

