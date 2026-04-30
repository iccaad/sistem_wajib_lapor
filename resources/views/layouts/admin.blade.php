<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Panel Admin — Sistem Wajib Lapor Digital Polrestabes Semarang">
    <title>@yield('title', 'Dashboard') — Admin Wajib Lapor</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('head')
</head>
<body class="h-full font-sans antialiased">

<div x-data="{ sidebarOpen: false }" class="flex h-full">

    {{-- ═══════════════ SIDEBAR ═══════════════ --}}
    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 z-30 bg-gray-950/60 lg:hidden"
         style="display:none;"></div>

    {{-- Sidebar panel --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-gray-950 border-t-4 border-gold-500 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0">

        {{-- Logo --}}
        <div class="flex h-16 shrink-0 items-center gap-3 px-6 border-b border-gray-800">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md bg-gradient-to-br from-gold-400 to-gold-600 shadow-md">
                <svg class="h-5 w-5 text-gray-100" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                </svg>
            </div>
            <div class="min-w-0">
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
                    ['route' => 'admin.reports.index', 'label' => 'Laporan', 'icon' => 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z'],
                ];
            @endphp

            @foreach ($navLinks as $link)
                @php $active = request()->routeIs($link['route'] . '*'); @endphp
                <a href="{{ route($link['route']) }}"
                   class="group flex items-center gap-3 rounded-md px-3 py-2.5 text-sm font-medium transition-colors
                          {{ $active
                             ? 'bg-indigo-600 text-white'
                             : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                         fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}" />
                    </svg>
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>

        {{-- Admin info + logout --}}
        <div class="shrink-0 border-t border-gray-700/50 p-4">
            <div class="flex items-center gap-3 rounded-md px-3 py-2 mb-2">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-500/200 text-white text-xs font-bold uppercase">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="min-w-0">
                    <p class="truncate text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                    <p class="truncate text-xs text-gray-400">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-2 rounded-md px-3 py-2 text-sm text-gray-400 hover:bg-gray-800 hover:text-white transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- ═══════════════ MAIN CONTENT ═══════════════ --}}
    <div class="flex flex-1 flex-col min-w-0 overflow-hidden">

        {{-- Top header bar --}}
        <header class="sticky top-0 z-20 flex h-16 shrink-0 items-center gap-4 border-b border-gray-700 bg-gray-800 px-4 sm:px-6 shadow-md border-t-2 border-indigo-500 shadow-gray-950/50">

            {{-- Mobile sidebar toggle --}}
            <button @click="sidebarOpen = true" class="lg:hidden text-gray-400 hover:text-gray-400 transition">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>

            {{-- Page title / breadcrumb --}}
            <div class="flex-1 min-w-0">
                <h1 class="text-base font-semibold text-gray-100 truncate">
                    @yield('page-title', 'Dashboard')
                </h1>
                @hasSection('breadcrumb')
                    <p class="text-xs text-gray-400 mt-0.5">@yield('breadcrumb')</p>
                @endif
            </div>

            {{-- Right side info --}}
            <div class="hidden sm:flex items-center gap-2 text-xs text-gray-400">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5" />
                </svg>
                {{ now()->translatedFormat('d M Y') }}
            </div>
        </header>

        {{-- Flash messages --}}
        @if (session('success') || session('error') || session('warning'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="mx-4 sm:mx-6 mt-4">
                @if (session('success'))
                    <div class="flex items-center gap-3 rounded-md border border-emerald-200 bg-emerald-500/20 px-4 py-3">
                        <svg class="h-5 w-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <p class="text-sm text-emerald-400 flex-1">{{ session('success') }}</p>
                        <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="flex items-center gap-3 rounded-md border border-red-200 bg-red-500/20 px-4 py-3">
                        <svg class="h-5 w-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                        <p class="text-sm text-red-400 flex-1">{{ session('error') }}</p>
                        <button @click="show = false" class="ml-auto text-red-400 hover:text-red-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                @endif
                @if (session('warning'))
                    <div class="flex items-center gap-3 rounded-md border border-amber-200 bg-amber-500/20 px-4 py-3">
                        <svg class="h-5 w-5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                        <p class="text-sm text-amber-400 flex-1">{{ session('warning') }}</p>
                        <button @click="show = false" class="ml-auto text-amber-400 hover:text-amber-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                @endif
            </div>
        @endif

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto px-4 sm:px-6 py-6">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>


