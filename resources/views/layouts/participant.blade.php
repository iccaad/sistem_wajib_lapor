<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="description" content="Sistem Wajib Lapor Digital — Polrestabes Semarang">
    <meta name="theme-color" content="#2563eb">
    <title>@yield('title', 'Dashboard') — Wajib Lapor</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('head')

    <style>
        /* Safe area insets for iOS notch/home bar */
        .pb-safe { padding-bottom: max(1rem, env(safe-area-inset-bottom)); }
        .bottom-safe { bottom: env(safe-area-inset-bottom, 0); }
    </style>
</head>
<body class="h-full bg-gray-900 font-sans antialiased">

<div class="flex flex-col min-h-screen">

    {{-- ═══ TOP HEADER ═══ --}}
    <header class="sticky top-0 z-30 bg-gray-950 border-b-4 border-gold-500 shadow-md">
        <div class="max-w-lg mx-auto px-4 h-14 flex items-center justify-between">
            {{-- Brand --}}
            <div class="flex items-center gap-2.5">
                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-gradient-to-br from-gold-400 to-gold-600 shadow-md border-t-2 border-indigo-500 shadow-gray-950/50">
                    <svg class="h-4 w-4 text-gray-100" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                </div>
                <div class="leading-tight">
                    <p class="text-sm font-bold text-white tracking-wide">Wajib Lapor</p>
                    <p class="text-[10px] text-gray-500 font-medium">POLRESTABES SEMARANG</p>
                </div>
            </div>

            {{-- User info --}}
            <div class="text-right hidden sm:block">
                <p class="text-xs font-semibold text-white">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500 font-mono">NIK: {{ Auth::user()->nik }}</p>
            </div>
        </div>
    </header>

    {{-- ═══ FLASH MESSAGES ═══ --}}
    @if (session('success') || session('error') || session('warning'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="max-w-lg mx-auto w-full px-4 pt-3">
            @if(session('success'))
                <div class="flex items-center gap-3 rounded-md border border-emerald-200 bg-emerald-500/20 px-4 py-3">
                    <svg class="h-5 w-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <p class="text-sm text-emerald-400 font-medium flex-1">{{ session('success') }}</p>
                    <button @click="show = false" class="text-emerald-400 hover:text-emerald-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif
            @if(session('error'))
                <div class="flex items-center gap-3 rounded-md border border-red-200 bg-red-500/20 px-4 py-3">
                    <svg class="h-5 w-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                    <p class="text-sm text-red-400 font-medium flex-1">{{ session('error') }}</p>
                    <button @click="show = false" class="text-red-400 hover:text-red-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif
            @if(session('warning'))
                <div class="flex items-center gap-3 rounded-md border border-amber-200 bg-amber-500/20 px-4 py-3">
                    <svg class="h-5 w-5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                    <p class="text-sm text-amber-400 font-medium flex-1">{{ session('warning') }}</p>
                    <button @click="show = false" class="text-amber-400 hover:text-amber-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif
        </div>
    @endif

    {{-- ═══ MAIN CONTENT ═══ --}}
    <main class="flex-1 max-w-lg mx-auto w-full px-4 py-5 pb-28">
        @yield('content')
    </main>

    {{-- ═══ BOTTOM NAVIGATION ═══ --}}
    <nav class="fixed bottom-0 inset-x-0 z-30 bg-gray-800 border-t border-gray-700 pb-safe">
        <div class="max-w-lg mx-auto flex">
            @php
                $navItems = [
                    ['route' => 'peserta.dashboard', 'label' => 'Dashboard', 'icon' => 'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25'],
                    ['route' => 'peserta.absence', 'label' => 'Absensi', 'icon' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
                    ['route' => 'peserta.history', 'label' => 'Riwayat', 'icon' => 'M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z'],
                ];
            @endphp

            @foreach ($navItems as $item)
                @php $active = request()->routeIs($item['route']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex-1 flex flex-col items-center justify-center py-3 gap-1 transition-colors
                          {{ $active ? 'text-blue-600' : 'text-gray-500 hover:text-slate-600' }}">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="{{ $active ? '2' : '1.5' }}" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                    </svg>
                    <span class="text-xs font-{{ $active ? 'semibold' : 'medium' }}">{{ $item['label'] }}</span>
                </a>
            @endforeach

            {{-- Logout --}}
            <form method="POST" action="{{ route('peserta.logout') }}" class="flex-1">
                @csrf
                <button type="submit"
                        class="w-full flex flex-col items-center justify-center py-3 gap-1 text-gray-500 hover:text-red-500 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                    </svg>
                    <span class="text-xs font-medium">Keluar</span>
                </button>
            </form>
        </div>
    </nav>

</div>

@stack('scripts')
</body>
</html>


