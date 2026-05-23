{{--
    Reusable participant filter bar.
    @param string $route   – Named route for the form action
    @param \Illuminate\Database\Eloquent\Collection $admins – Admin users for dropdown
    @param bool   $showSearch – Whether to show the search input (default: false)
--}}
@php
    $showSearch = $showSearch ?? false;
    $hasActiveFilters = request('date_from') || request('date_to') || request('admin_id');
@endphp

<div class="bg-white rounded-2xl border border-brand-light/50 shadow-sm p-4 mb-5 no-print">
    <form method="GET" action="{{ route($route) }}" id="filter-form">
        {{-- Preserve existing query params that are NOT part of this filter --}}
        @foreach(request()->except(['date_from', 'date_to', 'admin_id', 'page', 'search']) as $key => $value)
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

            {{-- Row 2: Filter inputs --}}
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
                    <label for="filter-date-from" class="block text-xs font-medium text-gray-500 mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" id="filter-date-from" value="{{ request('date_from') }}"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand-accent/20 focus:border-brand-accent transition-all duration-200 cursor-pointer">
                </div>

                {{-- Date To --}}
                <div class="min-w-0 sm:w-44">
                    <label for="filter-date-to" class="block text-xs font-medium text-gray-500 mb-1">Sampai Tanggal</label>
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
                    @if(request('date_from'))
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5" />
                            </svg>
                            Dari: {{ request('date_from') }}
                        </span>
                    @endif
                    @if(request('date_to'))
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5" />
                            </svg>
                            Sampai: {{ request('date_to') }}
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
