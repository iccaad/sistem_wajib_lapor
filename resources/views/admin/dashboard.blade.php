@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Sistem Wajib Lapor Digital — Polrestabes Semarang')

@section('content')


{{-- ── Stat Cards ── --}}
<div x-data="dashboardModal()" class="relative">
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">

    {{-- Total Aktif --}}
    <div @click="openModal('aktif', 'Peserta Aktif')" role="button" class="col-span-1 bg-white rounded-2xl border border-brand-light/50 shadow-sm p-5 hover:shadow-md hover:bg-gray-50 transition-all cursor-pointer">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-light/20">
                <svg class="h-5 w-5 text-brand-accent" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                </svg>
            </div>
            <div>
                <p class="text-3xl font-black text-brand-primary leading-none">{{ $totalActive }}</p>
                <p class="text-[10px] uppercase tracking-[0.1em] font-black text-brand-secondary mt-1.5">Peserta Aktif</p>
            </div>
        </div>
    </div>

    {{-- Patuh --}}
    <div @click="openModal('patuh', 'Peserta Patuh')" role="button" class="col-span-1 bg-white rounded-2xl border border-emerald-100 shadow-sm p-5 hover:shadow-md hover:bg-gray-50 transition-all cursor-pointer">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50">
                <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <div>
                <p class="text-3xl font-black text-emerald-600 leading-none">{{ $totalCompliant }}</p>
                <p class="text-[10px] uppercase tracking-[0.1em] font-black text-brand-secondary mt-1.5">Patuh</p>
            </div>
        </div>
    </div>

    {{-- Berisiko --}}
    <div @click="openModal('berisiko', 'Peserta Berisiko')" role="button" class="col-span-1 bg-white rounded-2xl border border-amber-100 shadow-sm p-5 hover:shadow-md hover:bg-gray-50 transition-all cursor-pointer">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50">
                <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </div>
            <div>
                <p class="text-3xl font-black text-amber-600 leading-none">{{ $totalAtRisk }}</p>
                <p class="text-[10px] uppercase tracking-[0.1em] font-black text-brand-secondary mt-1.5">Berisiko</p>
            </div>
        </div>
    </div>

    {{-- Mangkir --}}
    <div @click="openModal('mangkir', 'Peserta Mangkir')" role="button" class="col-span-1 bg-white rounded-2xl border border-red-100 shadow-sm p-5 hover:shadow-md hover:bg-gray-50 transition-all cursor-pointer">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-50">
                <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
            </div>
            <div>
                <p class="text-3xl font-black text-red-600 leading-none">{{ $totalAbsent }}</p>
                <p class="text-[10px] uppercase tracking-[0.1em] font-black text-brand-secondary mt-1.5">Mangkir</p>
            </div>
        </div>
    </div>

    {{-- Selesai Segera --}}
    <div @click="openModal('segera_selesai', 'Selesai Segera')" role="button" class="col-span-1 bg-white rounded-2xl border border-brand-light/30 shadow-sm p-5 hover:shadow-md hover:bg-gray-50 transition-all cursor-pointer">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-light/10">
                <svg class="h-5 w-5 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5" />
                </svg>
            </div>
            <div>
                <p class="text-3xl font-black text-brand-primary leading-none">{{ $endingSoon }}</p>
                <p class="text-[10px] uppercase tracking-[0.1em] font-black text-brand-secondary mt-1.5">Selesai Segera</p>
            </div>
        </div>
    </div>

    {{-- Selesai --}}
    <div @click="openModal('selesai', 'Telah Selesai')" role="button" class="col-span-1 bg-white rounded-2xl border border-gray-200 shadow-sm p-5 hover:shadow-md hover:bg-gray-50 transition-all cursor-pointer">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gray-100">
                <svg class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <div>
                <p class="text-3xl font-black text-gray-800 leading-none">{{ $completed }}</p>
                <p class="text-[10px] uppercase tracking-[0.1em] font-black text-gray-500 mt-1.5">Selesai</p>
            </div>
        </div>
    </div>

</div>

{{-- ── Recent Participants Table ── --}}
@include('components.per-page-dropdown', ['route' => 'admin.dashboard', 'current' => $recentParticipants->perPage()])

{{-- ── Filter Bar ── --}}
@include('components.participant-filters', ['route' => 'admin.dashboard', 'admins' => $admins])

    @include('components.participant-table', [
        'participants' => $recentParticipants,
        'title' => 'Peserta Terbaru',
        'headerAction' => '<a href="' . route('admin.participants.index') . '" class="text-xs text-brand-accent hover:text-brand-secondary font-bold transition">Lihat Semua →</a>'
    ])

    {{-- Modal Alpine.js --}}
    <div x-show="modalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" @click="closeModal()"></div>

        {{-- Modal Dialog --}}
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="modalOpen" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-5xl"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 @click.stop>
                
                {{-- Header --}}
                <div class="bg-gray-50 px-4 py-4 sm:px-6 flex items-center justify-between border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900" x-text="modalTitle"></h3>
                    <div class="flex items-center gap-3">
                        {{-- Export Button --}}
                        <a :href="exportUrl" class="inline-flex items-center gap-2 rounded-lg bg-emerald-50 px-3 py-2 text-sm font-bold text-emerald-600 hover:bg-emerald-100 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m6.75 12-3-3m0 0-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                            Ekspor Excel
                        </a>
                        <button type="button" @click="closeModal()" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Body --}}
                <div class="p-4 sm:p-6 bg-white overflow-x-auto">
                    <div x-show="loading" class="flex justify-center items-center py-12">
                        <svg class="animate-spin h-8 w-8 text-brand-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <div x-show="!loading" x-html="modalContent"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function dashboardModal() {
        return {
            modalOpen: false,
            modalType: '',
            modalTitle: '',
            modalContent: '',
            loading: false,
            exportUrl: '#',
            
            openModal(type, title) {
                this.modalType = type;
                this.modalTitle = title;
                this.modalOpen = true;
                this.loading = true;
                this.modalContent = '';
                
                // Determine Export URL parameters based on type
                let params = new URLSearchParams();
                if (type === 'aktif') {
                    params.append('status_akun', 'aktif');
                } else if (['patuh', 'berisiko', 'mangkir'].includes(type)) {
                    params.append('status_akun', 'aktif');
                    params.append('tingkat_kepatuhan', type);
                } else if (['segera_selesai', 'selesai'].includes(type)) {
                    params.append('status_akun', 'aktif');
                    params.append('progress_pengawasan', type);
                }
                
                this.exportUrl = `{{ route('admin.reports.export') }}?${params.toString()}`;

                // Fetch HTML content
                fetch(`{{ route('admin.dashboard.participants-modal') }}?filter_type=${type}`)
                    .then(res => res.text())
                    .then(html => {
                        this.modalContent = html;
                        this.loading = false;
                    })
                    .catch(err => {
                        this.modalContent = '<div class="p-4 text-red-500 text-center font-bold">Gagal memuat data.</div>';
                        this.loading = false;
                    });
            },
            
            closeModal() {
                this.modalOpen = false;
                setTimeout(() => {
                    this.modalContent = '';
                }, 300);
            }
        }
    }
</script>
@endsection
