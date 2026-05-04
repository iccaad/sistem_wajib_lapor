<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" type="image/png" href="{{ asset('images/logo-libas.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-brand-primary antialiased bg-brand-primary">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-brand-primary via-brand-secondary to-brand-primary/90 p-4">
            <div class="mb-8 transform hover:scale-105 transition-transform duration-300">
                <a href="/">
                    <x-application-logo class="w-28 h-28 drop-shadow-2xl" />
                </a>
            </div>

            <div class="w-full sm:max-w-md px-8 py-10 bg-white/10 backdrop-blur-xl border border-white/20 shadow-[0_20px_50px_rgba(0,0,0,0.3)] overflow-hidden sm:rounded-2xl">
                {{ $slot }}
            </div>
            
            <p class="mt-8 text-sm text-brand-soft font-medium">
                &copy; {{ date('Y') }} Polrestabes Semarang — Wajib Lapor Digital
            </p>
        </div>
    </body>
</html>
