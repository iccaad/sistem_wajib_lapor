<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Akses Ditolak | Sistem Wajib Lapor</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-900 flex items-center justify-center p-4">
    <div class="text-center max-w-sm w-full">
        <div class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-red-500/20 mx-auto mb-5">
            <svg class="h-10 w-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.286Zm0 13.036h.008v.008H12v-.008Z" />
            </svg>
        </div>
        <p class="text-7xl font-black text-red-500 mb-2">403</p>
        <h1 class="text-xl font-bold text-gray-200 mb-2">Akses Ditolak</h1>
        <p class="text-sm text-gray-400 mb-6">
            Anda tidak memiliki izin untuk mengakses halaman ini.
            Pastikan Anda telah masuk dengan akun yang benar.
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


