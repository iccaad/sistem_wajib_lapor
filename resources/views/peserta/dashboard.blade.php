<x-peserta-layout>
    <x-slot name="title">Dashboard Peserta</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-800">
            Dashboard Peserta
        </h2>
    </x-slot>

    {{-- Welcome Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5">
            <h3 class="text-lg font-semibold text-white">
                Selamat datang, {{ Auth::user()->name }}!
            </h3>
            <p class="text-blue-100 text-sm mt-1">
                Anda berhasil masuk ke sistem wajib lapor.
            </p>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                {{-- Name --}}
                <div class="bg-slate-50 rounded-lg p-4">
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Nama Lengkap</p>
                    <p class="text-lg font-semibold text-slate-800 mt-1">{{ Auth::user()->name }}</p>
                </div>

                {{-- NIK --}}
                <div class="bg-slate-50 rounded-lg p-4">
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">NIK</p>
                    <p class="text-lg font-semibold text-slate-800 mt-1 tracking-wider">{{ Auth::user()->nik }}</p>
                </div>

                {{-- Status --}}
                <div class="bg-slate-50 rounded-lg p-4">
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Status</p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="inline-flex w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-lg font-semibold text-emerald-600">Aktif</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Note --}}
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="flex gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
            </svg>
            <div>
                <p class="text-sm font-medium text-blue-800">Informasi</p>
                <p class="text-sm text-blue-700 mt-0.5">
                    Fitur absensi dan riwayat kehadiran akan tersedia pada tahap berikutnya.
                </p>
            </div>
        </div>
    </div>

</x-peserta-layout>
