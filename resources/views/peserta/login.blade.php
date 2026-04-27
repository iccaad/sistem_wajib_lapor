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
    <body class="font-sans antialiased bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 min-h-screen">
        <div class="min-h-screen flex flex-col items-center justify-center px-4 sm:px-6">

            {{-- Header / Branding --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-600/20 border border-blue-500/30 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Sistem Wajib Lapor</h1>
                <p class="text-blue-300/80 text-sm mt-1">Polrestabes Semarang</p>
            </div>

            {{-- Login Card --}}
            <div class="w-full max-w-sm">
                <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl shadow-2xl p-8">
                    <h2 class="text-lg font-semibold text-white text-center mb-1">Login Peserta</h2>
                    <p class="text-blue-300/70 text-sm text-center mb-6">Masukkan NIK Anda untuk masuk</p>

                    <form method="POST" action="{{ route('peserta.login.submit') }}">
                        @csrf

                        {{-- NIK Input --}}
                        <div class="mb-5">
                            <label for="nik" class="block text-sm font-medium text-blue-200 mb-2">
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
                                class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-blue-300/50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 text-center text-lg tracking-widest @error('nik') border-red-400 ring-1 ring-red-400 @enderror"
                            >

                            @error('nik')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Submit Button --}}
                        <button
                            type="submit"
                            class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-500 active:bg-blue-700 text-white font-semibold rounded-xl transition duration-200 shadow-lg shadow-blue-600/30 hover:shadow-blue-500/40 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-slate-900"
                        >
                            Masuk
                        </button>
                    </form>
                </div>

                {{-- Footer Note --}}
                <p class="text-center text-blue-400/50 text-xs mt-6">
                    Hubungi petugas jika Anda mengalami kendala login.
                </p>
            </div>

        </div>
    </body>
</html>
