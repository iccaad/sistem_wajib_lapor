<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="description" content="Sistem Wajib Lapor Digital — Polrestabes Semarang">
    <meta name="theme-color" content="#021024">
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
<body class="h-full bg-brand-light/30 font-sans antialiased text-brand-primary">

<div class="flex flex-col min-h-screen">

    {{-- ═══ TOP HEADER ═══ --}}
    <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-brand-light shadow-sm">
        <div class="max-w-lg mx-auto px-4 h-14 flex items-center justify-between">
            {{-- Brand --}}
            <div class="flex items-center gap-2.5">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-transparent">
                    <img src="{{ asset('assets/images/logo-libas.png') }}" alt="LIBAS Logo" class="h-9 w-auto object-contain">
                </div>
                <div class="leading-tight">
                    <p class="text-sm font-bold text-brand-primary">Wajib Lapor</p>
                    <p class="text-xs text-brand-soft">Polrestabes Semarang</p>
                </div>
            </div>

            {{-- User info --}}
            <div class="text-right hidden sm:block">
                <p class="text-xs font-black text-brand-secondary">{{ Auth::user()->name }}</p>
                <p class="text-[10px] text-brand-soft font-black uppercase tracking-wider">NIK: {{ Auth::user()->nik }}</p>
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
                <div class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-4 shadow-sm">
                    <svg class="h-6 w-6 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <p class="text-sm text-emerald-900 font-black flex-1">{{ session('success') }}</p>
                    <button @click="show = false" class="text-emerald-400 hover:text-emerald-600 transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif
            @if(session('error'))
                <div class="flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3">
                    <svg class="h-5 w-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                    <p class="text-sm text-red-700 font-medium flex-1">{{ session('error') }}</p>
                    <button @click="show = false" class="text-red-400 hover:text-red-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif
            @if(session('warning'))
                <div class="flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
                    <svg class="h-5 w-5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                    <p class="text-sm text-amber-700 font-medium flex-1">{{ session('warning') }}</p>
                    <button @click="show = false" class="text-amber-400 hover:text-amber-600">
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
    <nav class="fixed bottom-0 inset-x-0 z-30 bg-white/90 backdrop-blur-lg border-t border-brand-light pb-safe shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
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
                   class="flex-1 flex flex-col items-center justify-center py-3 gap-1 transition-all duration-300
                          {{ $active ? 'text-brand-accent' : 'text-brand-soft hover:text-brand-secondary' }}">
                    <div class="relative">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="{{ $active ? '2.5' : '1.5' }}" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                        </svg>
                        @if($active)
                            <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-brand-accent shadow-[0_0_8px_rgba(84,131,179,0.5)]"></div>
                        @endif
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest">{{ $item['label'] }}</span>
                </a>
            @endforeach

            {{-- Logout --}}
            <form method="POST" action="{{ route('peserta.logout') }}" class="flex-1">
                @csrf
                <button type="submit"
                        class="w-full h-full flex flex-col items-center justify-center py-3 gap-1 text-brand-soft hover:text-red-600 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                    </svg>
                    <span class="text-[10px] font-black uppercase tracking-widest">Keluar</span>
                </button>
            </form>
        </div>
    </nav>

</div>

@stack('scripts')
</body>
</html>
