@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Sistem Wajib Lapor Digital — Polrestabes Semarang')

@section('content')



{{-- ── Stat Cards ── --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">

    {{-- Total Aktif --}}
    <div class="col-span-1 bg-white rounded-2xl border border-brand-light/50 shadow-sm p-5 hover:shadow-md transition-shadow">
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
    <div class="col-span-1 bg-white rounded-2xl border border-emerald-100 shadow-sm p-5 hover:shadow-md transition-shadow">
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
    <div class="col-span-1 bg-white rounded-2xl border border-amber-100 shadow-sm p-5 hover:shadow-md transition-shadow">
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
    <div class="col-span-1 bg-white rounded-2xl border border-red-100 shadow-sm p-5 hover:shadow-md transition-shadow">
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
    <div class="col-span-1 bg-white rounded-2xl border border-brand-light/30 shadow-sm p-5 hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-light/10">
                <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5" />
                </svg>
            </div>
            <div>
                <p class="text-3xl font-black text-brand-primary leading-none">{{ $endingSoon }}</p>
                <p class="text-[10px] uppercase tracking-[0.1em] font-black text-brand-secondary mt-1.5">Selesai Segera</p>
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

@endsection
