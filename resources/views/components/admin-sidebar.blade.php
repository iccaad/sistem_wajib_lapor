{{--
    Admin sidebar navigation component.
    Shares Alpine.js state (sidebarOpen, sidebarCollapsed) with parent layout.
--}}
<aside :class="{
            'translate-x-0': sidebarOpen,
            '-translate-x-full': !sidebarOpen,
            'lg:w-60': !sidebarCollapsed,
            'lg:w-20': sidebarCollapsed
       }"
       class="fixed inset-y-0 left-0 z-40 flex flex-col bg-brand-primary transition-all duration-300 ease-in-out lg:static lg:translate-x-0 shadow-2xl">

    {{-- Logo --}}
    <div class="flex h-16 shrink-0 items-center gap-3 px-5 border-b border-white/10">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-transparent">
            <img src="{{ asset('assets/images/logo-libas.png') }}" alt="LIBAS Logo" class="h-10 w-auto object-contain">
        </div>
        <div class="min-w-0" x-show="!sidebarCollapsed" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <p class="truncate text-sm font-semibold text-white leading-tight">Wajib Lapor</p>
            <p class="truncate text-xs text-gray-400">Panel Admin</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-1">
        @php
            $navLinks = [
                ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25'],
                ['route' => 'admin.participants.index', 'label' => 'Peserta', 'icon' => 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z'],
                ['route' => 'admin.locations.index', 'label' => 'Lokasi', 'icon' => 'M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z'],
                ['route' => 'admin.violation-types.index', 'label' => 'Jenis Pelanggaran', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
                ['route' => 'admin.reports.index', 'label' => 'Laporan', 'icon' => 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z'],
            ];
        @endphp

        @foreach ($navLinks as $link)
            @php $active = request()->routeIs($link['route'] . '*'); @endphp
            <a href="{{ route($link['route']) }}"
               class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200
                      {{ $active
                         ? 'bg-brand-secondary text-white shadow-lg shadow-black/20'
                         : 'text-brand-soft hover:bg-white/5 hover:text-white' }}"
               :class="sidebarCollapsed ? 'justify-center px-2' : ''"
               title="{{ $link['label'] }}">
                <svg class="h-6 w-6 shrink-0 {{ $active ? 'text-brand-accent' : 'text-brand-soft group-hover:text-white' }}"
                     fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}" />
                </svg>
                <span x-show="!sidebarCollapsed" class="truncate">{{ $link['label'] }}</span>
            </a>
        @endforeach

        {{-- Super Admin Only: Akun Admin --}}
        @if(auth()->user()->is_root_super_admin || auth()->user()->role === 'super_admin')
            @php $activeAccounts = request()->routeIs('admin.accounts.*'); @endphp
            <div class="pt-3 mt-3 border-t border-white/10">
                <p class="px-3 mb-2 text-[10px] font-bold uppercase tracking-widest text-brand-soft/50" x-show="!sidebarCollapsed">Super Admin</p>
                <a href="{{ route('admin.accounts.index') }}"
                   class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200
                          {{ $activeAccounts
                             ? 'bg-brand-secondary text-white shadow-lg shadow-black/20'
                             : 'text-brand-soft hover:bg-white/5 hover:text-white' }}"
                   :class="sidebarCollapsed ? 'justify-center px-2' : ''"
                   title="Akun Admin">
                    <svg class="h-6 w-6 shrink-0 {{ $activeAccounts ? 'text-brand-accent' : 'text-brand-soft group-hover:text-white' }}"
                         fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                    <span x-show="!sidebarCollapsed" class="truncate">Akun Admin</span>
                </a>
            </div>
        @endif
    </nav>

    {{-- Admin info + logout --}}
    <div class="shrink-0 border-t border-white/10 p-4 bg-black/10">
        <a href="{{ route('admin.profile.edit') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 mb-2 hover:bg-white/5 transition-all duration-200 cursor-pointer group" :class="sidebarCollapsed ? 'justify-center px-0' : ''" title="Edit Profil">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-accent text-brand-primary text-xs font-bold uppercase shadow-sm group-hover:scale-105 transition-transform duration-200">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div class="min-w-0" x-show="!sidebarCollapsed">
                <p class="truncate text-sm font-medium text-white group-hover:text-brand-accent transition-colors duration-200">{{ auth()->user()->name }}</p>
                <p class="truncate text-xs text-gray-400 font-mono mt-0.5">{{ auth()->user()->email }}</p>
            </div>
        </a>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-brand-soft hover:bg-red-500/10 hover:text-red-400 transition-colors"
                    :class="sidebarCollapsed ? 'justify-center px-2' : ''"
                    title="Keluar">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                </svg>
                <span x-show="!sidebarCollapsed">Keluar</span>
            </button>
        </form>
    </div>
</aside>
