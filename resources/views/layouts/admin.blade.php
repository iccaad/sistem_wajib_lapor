<!DOCTYPE html>
<html lang="id" class="h-full bg-brand-light/30">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Panel Admin — Sistem Wajib Lapor Digital Polrestabes Semarang">
    <title>@yield('title', 'Dashboard') — Admin Wajib Lapor</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo-libas.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('head')
</head>
<body class="h-full font-sans antialiased">

<div x-data="{ 
        sidebarOpen: false, 
        sidebarCollapsed: localStorage.getItem('sidebar-collapsed') === 'false' ? false : true 
    }" 
    x-init="$watch('sidebarCollapsed', value => localStorage.setItem('sidebar-collapsed', value))"
    class="flex h-full">

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
         class="fixed inset-0 z-30 bg-gray-900/60 lg:hidden"
         style="display:none;"></div>

    {{-- Sidebar panel component --}}
    <x-admin-sidebar />

    {{-- ═══════════════ MAIN CONTENT ═══════════════ --}}
    <div class="flex flex-1 flex-col min-w-0 overflow-hidden">

        {{-- Top header bar --}}
        <header class="sticky top-0 z-20 flex h-16 shrink-0 items-center gap-4 border-b border-brand-light bg-white/80 backdrop-blur-md px-4 sm:px-6 shadow-sm">

            {{-- Mobile sidebar toggle --}}
            <button @click="sidebarOpen = true" class="lg:hidden text-gray-400 hover:text-gray-600 transition">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>

            {{-- Desktop Sidebar Collapse Toggle --}}
            <button @click="sidebarCollapsed = !sidebarCollapsed" class="hidden lg:flex items-center justify-center h-8 w-8 rounded-lg bg-gray-100 text-gray-400 hover:text-gray-600 hover:bg-gray-200 transition">
                <svg class="h-5 w-5 transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </button>

            {{-- Page title / breadcrumb --}}
            <div class="flex-1 min-w-0">
                <h1 class="text-base font-semibold text-gray-900 truncate">
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
                    <div class="flex items-center gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3">
                        <svg class="h-5 w-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <p class="text-sm text-emerald-700 flex-1">{{ session('success') }}</p>
                        <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="flex items-center gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3">
                        <svg class="h-5 w-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                        <p class="text-sm text-red-700 flex-1">{{ session('error') }}</p>
                        <button @click="show = false" class="ml-auto text-red-400 hover:text-red-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                @endif
                @if (session('warning'))
                    <div class="flex items-center gap-3 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3">
                        <svg class="h-5 w-5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                        <p class="text-sm text-amber-700 flex-1">{{ session('warning') }}</p>
                        <button @click="show = false" class="ml-auto text-amber-400 hover:text-amber-600">
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
