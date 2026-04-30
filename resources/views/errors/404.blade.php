<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Halaman Tidak Ditemukan | Sistem Wajib Lapor</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-900 flex items-center justify-center p-4">
    <div class="text-center max-w-sm w-full">
        <div class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-indigo-500/20 mx-auto mb-5">
            <svg class="h-10 w-10 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
        </div>
        <p class="text-7xl font-black text-indigo-400 mb-2">404</p>
        <h1 class="text-xl font-bold text-gray-200 mb-2">Halaman Tidak Ditemukan</h1>
        <p class="text-sm text-gray-400 mb-6">
            Halaman yang Anda cari tidak ada atau telah dipindahkan.
            Periksa kembali URL yang Anda masukkan.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ url()->previous() }}"
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-md border border-gray-700 text-sm font-medium text-slate-600 hover:bg-slate-100 transition">
                ← Kembali
            </a>
            <a href="{{ url('/') }}"
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-md bg-indigo-600 hover:bg-indigo-700 text-sm font-medium text-white transition">
                🏠 Beranda
            </a>
        </div>
        <p class="mt-8 text-xs text-gray-500">Sistem Wajib Lapor — Polrestabes Semarang</p>
    </div>
</body>
</html>


