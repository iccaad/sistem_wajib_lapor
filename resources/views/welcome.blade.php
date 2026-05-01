<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-brand-primary">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sistem Wajib Lapor - {{ config('app.name', 'Laravel') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full font-sans antialiased text-white bg-brand-primary flex flex-col selection:bg-brand-accent selection:text-white">
        
        <header class="w-full absolute top-0 left-0 right-0 z-50 p-6 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 flex items-center justify-center">
                    <img src="{{ asset('assets/images/logo-libas.png') }}" alt="LIBAS Logo" class="h-12 w-auto object-contain drop-shadow-xl">
                </div>
                <div class="font-bold text-xl tracking-wide text-white">SISTEM WAJIB LAPOR</div>
            </div>

            @if (Route::has('admin.login'))
                <nav class="hidden md:flex gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="font-semibold text-gray-300 hover:text-white transition focus:outline-none focus-visible:ring-2 focus-visible:ring-polri-gold rounded px-3 py-2">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('admin.login') }}" class="font-semibold text-white bg-brand-accent hover:bg-brand-accent/80 transition shadow-lg rounded-xl px-6 py-2.5">
                            Masuk
                        </a>
                        @if (Route::has('admin.register'))
                            <a href="{{ route('admin.register') }}" class="font-semibold text-brand-soft hover:text-white transition focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-accent rounded px-4 py-2">
                                Daftar
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

        <main class="flex-grow flex items-center justify-center px-6 relative overflow-hidden">
            <!-- Decorative Background Elements -->
            <div class="absolute inset-0 z-0 opacity-20">
                <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[60%] bg-brand-accent rounded-full mix-blend-screen filter blur-[120px]"></div>
                <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-brand-secondary rounded-full mix-blend-screen filter blur-[150px]"></div>
            </div>

            <div class="max-w-4xl w-full z-10 text-center space-y-8">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-secondary/50 border border-white/10 text-sm font-medium text-brand-light mb-4 backdrop-blur-sm">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-accent opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-brand-accent"></span>
                    </span>
                    Sistem Digital Resmi
                </div>

                <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight text-white drop-shadow-lg">
                    Lapor Cepat, <br />
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-accent to-brand-light">Aman & Terpadu.</span>
                </h1>
                
                <p class="text-lg md:text-xl text-brand-soft max-w-2xl mx-auto leading-relaxed">
                    Platform digital terpadu untuk pencatatan dan pemantauan sistem wajib lapor secara real-time, transparan, dan akuntabel.
                </p>

                <div class="pt-8 flex flex-col sm:flex-row justify-center items-center gap-4">
                    <a href="{{ route('admin.login') }}" class="w-full sm:w-auto px-10 py-4 bg-brand-accent hover:bg-brand-accent/80 text-white font-bold text-lg rounded-2xl shadow-xl hover:shadow-2xl transition transform hover:-translate-y-1 focus:outline-none focus:ring-4 focus:ring-brand-accent/50 text-center">
                        Masuk Ke Portal
                    </a>
                    <a href="#tentang" class="w-full sm:w-auto px-10 py-4 bg-brand-secondary/40 hover:bg-brand-secondary/60 text-white font-bold text-lg rounded-2xl border border-white/10 hover:border-white/20 shadow-md transition transform hover:-translate-y-1 focus:outline-none focus:ring-4 focus:ring-white/10 text-center backdrop-blur-md">
                        Pelajari Sistem
                    </a>
                </div>
            </div>
        </main>

        <footer class="w-full border-t border-white/5 py-8 text-center text-sm text-brand-soft z-10 bg-brand-primary">
            &copy; {{ date('Y') }} Sistem Wajib Lapor Polrestabes Semarang. Semua Hak Dilindungi.
        </footer>
    </body>
</html>
