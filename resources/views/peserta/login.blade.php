<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Login Peserta — {{ config('app.name', 'Sistem Wajib Lapor') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-brand-primary min-h-screen selection:bg-brand-accent selection:text-white">
        <div class="min-h-screen flex flex-col items-center justify-center px-4 sm:px-6 bg-gradient-to-br from-brand-primary via-brand-secondary to-brand-primary/95 p-4">

            {{-- Header / Branding --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 mb-4">
                    <img src="{{ asset('assets/images/logo-libas.png') }}" alt="LIBAS Logo" class="h-20 w-auto object-contain drop-shadow-2xl">
                </div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Sistem Wajib Lapor</h1>
                <p class="text-brand-light text-sm mt-1">Polrestabes Semarang</p>
            </div>

            {{-- Login Card --}}
            <div class="w-full max-w-sm">
                <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl shadow-2xl p-8">
                    <h2 class="text-lg font-semibold text-white text-center mb-1">Login Peserta</h2>
                    <p class="text-brand-soft text-sm text-center mb-6">Masukkan NIK Anda untuk masuk</p>

                    <form method="POST" action="{{ route('peserta.login.submit') }}">
                        @csrf

                        {{-- NIK Input --}}
                        <div class="mb-5">
                            <label for="nik" class="block text-sm font-medium text-brand-light mb-2">
                                Nomor Induk Kependudukan (NIK)
                            </label>
                            <input
                                type="text"
                                id="nik"
                                name="nik"
                                value="{{ old('nik') }}"
                                maxlength="16"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                placeholder="Masukkan 16 digit NIK"
                                autofocus
                                required
                                class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-brand-soft/50 focus:outline-none focus:ring-2 focus:ring-brand-accent focus:border-transparent transition duration-200 text-center text-lg tracking-widest @error('nik') border-red-400 ring-1 ring-red-400 @enderror"
                            >

                            @error('nik')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Submit Button --}}
                        <button
                            type="submit"
                            class="w-full py-3 px-4 bg-brand-accent hover:bg-brand-accent/80 active:bg-brand-secondary text-white font-semibold rounded-xl transition duration-200 shadow-lg shadow-black/20 focus:outline-none focus:ring-2 focus:ring-brand-light focus:ring-offset-2 focus:ring-offset-brand-primary"
                        >
                            Masuk
                        </button>
                    </form>
                </div>

                {{-- Footer Note --}}
                <p class="text-center text-brand-soft/50 text-xs mt-6">
                    Hubungi petugas jika Anda mengalami kendala login.
                </p>
            </div>

        </div>
    </body>
</html>
