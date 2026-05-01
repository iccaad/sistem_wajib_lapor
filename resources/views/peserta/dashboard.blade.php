@extends('layouts.participant')

@section('title', 'Dashboard')

@section('content')

{{-- ── SECTION 1: Warning Banners ── --}}
@foreach ($activeWarnings as $warning)
    @if ($warning->level === 'level_3')
        <div class="mb-4 rounded-xl bg-red-700 text-white px-4 py-4 shadow-lg">
            <div class="flex items-start gap-3">
                <span class="text-2xl">🚨</span>
                <div>
                    <p class="font-bold text-sm uppercase tracking-wide">PERINGATAN KRITIS — LEVEL 3</p>
                    <p class="text-sm mt-1 text-red-100">{{ $warning->reason }}</p>
                    <p class="text-xs mt-2 font-semibold text-red-200">⚠ WAJIB HADIR LANGSUNG KE POLRES SEGERA</p>
                </div>
            </div>
        </div>
    @elseif ($warning->level === 'level_2')
        <div class="mb-4 rounded-xl bg-red-50 border border-red-200 px-4 py-4">
            <div class="flex items-start gap-3">
                <span class="text-xl">⚠️</span>
                <div>
                    <p class="font-bold text-sm text-red-800">Peringatan Level 2 — Mangkir</p>
                    <p class="text-sm text-red-700 mt-1">{{ $warning->reason }}</p>
                </div>
            </div>
        </div>
    @elseif ($warning->level === 'level_1')
        <div class="mb-4 rounded-xl bg-amber-50 border border-amber-200 px-4 py-4">
            <div class="flex items-start gap-3">
                <span class="text-xl">⏰</span>
                <div>
                    <p class="font-bold text-sm text-amber-800">Peringatan Level 1 — Hampir Habis</p>
                    <p class="text-sm text-amber-700 mt-1">{{ $warning->reason }}</p>
                </div>
            </div>
        </div>
    @endif
@endforeach

{{-- ── SECTION 2: Participant Status Card ── --}}
<div class="bg-white rounded-2xl border border-brand-light shadow-sm overflow-hidden mb-4">
    <div class="bg-gradient-to-br from-brand-primary via-brand-secondary to-brand-primary px-5 py-6 border-b border-white/10">
        <p class="text-brand-accent text-[10px] font-bold uppercase tracking-[0.2em] mb-1">Data Peserta</p>
        <h2 class="text-xl font-bold leading-tight {{ $participant->hasCompletedAllPeriods() ? 'text-emerald-400' : 'text-white' }}">{{ $participant->full_name }}</h2>
        <div class="mt-2 flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-white/20 text-white border border-white/20">
                {{ $participant->violationType->name ?? '—' }}
            </span>
            @if ($isActive)
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-100 border border-emerald-400/30">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span> AKTIF
                </span>
            @else
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-500/20 text-gray-200 border border-gray-400/30">
                    SELESAI
                </span>
            @endif
        </div>
    </div>

    <div class="px-5 py-4 grid grid-cols-2 gap-3">
        <div class="bg-brand-light/20 rounded-xl p-4 border border-brand-light/30">
            <p class="text-[10px] text-brand-secondary uppercase font-black tracking-wider mb-1">Mulai</p>
            <p class="text-base font-black text-brand-primary">
                {{ $participant->supervision_start->translatedFormat('d M Y') }}
            </p>
        </div>
        <div class="bg-brand-light/20 rounded-xl p-4 border border-brand-light/30">
            <p class="text-[10px] text-brand-secondary uppercase font-black tracking-wider mb-1">Selesai</p>
            <p class="text-base font-black text-brand-primary">
                {{ $participant->supervision_end->translatedFormat('d M Y') }}
            </p>
        </div>
        <div class="bg-brand-light/20 rounded-xl p-4 col-span-2 border border-brand-light/30">
            <p class="text-[10px] text-brand-secondary uppercase font-black tracking-wider mb-1">Sisa Hari Pengawasan</p>
            <p class="text-xl font-bold {{ $remainingDays <= 7 ? 'text-red-600' : ($remainingDays <= 30 ? 'text-amber-600' : 'text-emerald-600') }}">
                {{ $remainingDays }} hari lagi
            </p>
        </div>
    </div>
</div>

{{-- ── SECTION 3: Period Progress ── --}}
@if ($currentPeriod)
    <div class="bg-white rounded-2xl border border-brand-light shadow-sm px-5 py-5 mb-4">
        <p class="text-[10px] text-brand-soft font-bold uppercase tracking-widest mb-1">Progres Periode Ini</p>
        <p class="text-xs text-slate-400 mb-3">
            {{ $currentPeriod->period_start->format('d/m/Y') }} s.d. {{ $currentPeriod->period_end->format('d/m/Y') }}
        </p>

        {{-- Progress bar --}}
        @php $pct = min(100, round($attendedCount / max(1, $currentPeriod->target_count) * 100)); @endphp
        <div class="flex items-center justify-between mb-2">
            <span class="text-2xl font-black {{ $quotaFull ? 'text-emerald-600' : 'text-brand-primary' }}">
                {{ $attendedCount }} <span class="text-lg text-brand-soft font-normal">/ {{ $currentPeriod->target_count }}</span>
            </span>
            <span class="text-sm font-bold {{ $quotaFull ? 'text-emerald-600' : 'text-brand-accent' }}">{{ $pct }}%</span>
        </div>
        <div class="w-full bg-brand-light/30 rounded-full h-3.5 overflow-hidden shadow-inner">
            <div class="h-3.5 rounded-full transition-all duration-700 ease-out {{ $quotaFull ? 'bg-emerald-500' : 'bg-brand-accent' }} shadow-lg shadow-brand-accent/20"
                 style="width: {{ $pct }}%"></div>
        </div>
        <p class="text-xs text-brand-secondary mt-3 font-bold">
            @if ($quotaFull)
                <span class="text-emerald-600">🎉 Target periode ini sudah terpenuhi!</span>
            @else
                Masih perlu <span class="font-black text-brand-accent">{{ $remainingCount }} kali</span> kehadiran lagi
            @endif
        </p>
    </div>
@else
    <div class="bg-slate-50 rounded-2xl border border-slate-200 px-5 py-5 mb-4 text-center">
        <p class="text-sm text-slate-500">Tidak ada periode absensi aktif saat ini.</p>
    </div>
@endif

{{-- ── SECTION 4: Absence Button ── --}}
<div class="mb-4">
    @php
        $canAbsent = $isActive && !$hasAbsentToday && !$quotaFull && $currentPeriod;
    @endphp

    @if ($canAbsent)
        <a href="{{ route('peserta.absence') }}"
           id="btn-absensi-sekarang"
           class="flex items-center justify-center gap-4 w-full py-6 rounded-2xl
                  bg-gradient-to-r from-brand-accent via-brand-secondary to-brand-accent bg-[length:200%_auto] hover:bg-right
                  text-white text-lg font-black shadow-2xl shadow-brand-accent/40
                  hover:shadow-brand-accent/50 hover:-translate-y-1 active:scale-[0.98]
                  transition-all duration-500 ease-out">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            Absensi Sekarang
        </a>
    @elseif ($hasAbsentToday)
        <div class="flex items-center justify-center gap-3 w-full py-5 rounded-2xl bg-emerald-50 border-2 border-emerald-200 shadow-lg shadow-emerald-500/10">
            <svg class="h-7 w-7 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <span class="text-lg font-black text-emerald-700">Sudah Absen Hari Ini ✓</span>
        </div>
    @elseif ($quotaFull)
        <div class="flex items-center justify-center gap-3 w-full py-5 rounded-2xl bg-emerald-50 border-2 border-emerald-200 shadow-lg shadow-emerald-500/10">
            <span class="text-lg font-black text-emerald-700 text-center px-4 leading-tight">🎉 Target Periode Ini Sudah Terpenuhi ✓</span>
        </div>
    @else
        <div class="flex items-center justify-center gap-3 w-full py-5 rounded-2xl bg-brand-light/20 border border-brand-light text-brand-soft">
            <span class="text-base font-bold">Masa pengawasan sudah selesai</span>
        </div>
    @endif
</div>

{{-- ── SECTION 5: Assigned Location ── --}}
@if ($location && $isActive)
    <div class="bg-white rounded-2xl border border-brand-light shadow-sm overflow-hidden mb-4">
        <div class="px-5 py-4 bg-brand-light/10 border-b border-brand-light/30">
            <div class="flex items-center justify-between">
                <p class="text-sm font-bold text-brand-primary">📍 Lokasi Wajib Lapor</p>
                <span class="text-[10px] font-bold text-brand-accent bg-brand-accent/10 px-2 py-0.5 rounded-full uppercase tracking-wider">
                    Aktif
                </span>
            </div>
        </div>
        <div class="relative">
            <div id="next-location-map" class="w-full h-44" style="z-index: 1;"></div>
            <button type="button" id="btn-refresh-location" class="absolute bottom-3 right-3 z-[1000] bg-white p-2 rounded-full shadow-md border border-slate-200 text-slate-600 hover:text-polri-navy transition" title="Perbarui Lokasi Saya">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                </svg>
            </button>
        </div>
        <div class="px-5 py-3">
            <p class="font-semibold text-slate-800">{{ $location->name }}</p>
            @if ($location->address)
                <p class="text-xs text-slate-500 mt-0.5">{{ $location->address }}</p>
            @endif
            <p class="text-xs text-slate-400 mt-1">Radius: {{ $location->radius_meters }}m</p>
        </div>
    </div>
@endif

{{-- ── SECTION 6: Recent Attendance ── --}}
<div class="bg-white rounded-2xl border border-brand-light shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-brand-light/30 flex items-center justify-between">
        <p class="text-sm font-bold text-brand-primary uppercase tracking-wider">Riwayat Terakhir</p>
        <a href="{{ route('peserta.history') }}" class="text-xs font-bold text-brand-accent hover:text-brand-secondary transition">
            Lihat Semua →
        </a>
    </div>

    @if ($recentLogs->isNotEmpty())
        <div class="divide-y divide-slate-100">
            @foreach ($recentLogs as $log)
                <div class="px-5 py-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-800">
                            {{ \Carbon\Carbon::parse($log->attendance_date)->translatedFormat('l, d M Y') }}
                        </p>
                        <p class="text-xs text-slate-400">
                            {{ $log->attendance_time }} • {{ $log->location?->name ?? '—' }}
                        </p>
                    </div>
                    @if ($log->status === 'manual_override')
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                            Manual
                        </span>
                    @else
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                            ✓ Hadir
                        </span>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="px-5 py-8 text-center text-sm text-slate-400">
            Belum ada catatan absensi.
        </div>
    @endif
</div>

@endsection

@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    @if (isset($location) && $location && $isActive)
    const mapEl = document.getElementById('next-location-map');
    if (!mapEl) return;

    const loc = {
        name: @json($location->name),
        lat: {{ (float) $location->latitude }},
        lng: {{ (float) $location->longitude }},
        radius: {{ $location->radius_meters }},
        address: @json($location->address ?? ''),
    };

    const map = L.map('next-location-map', { zoomControl: true }).setView([loc.lat, loc.lng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    L.circle([loc.lat, loc.lng], {
        radius: loc.radius,
        color: '#2563eb',
        fillColor: '#2563eb',
        fillOpacity: 0.15,
        weight: 2,
    }).addTo(map);

    L.marker([loc.lat, loc.lng])
        .bindPopup(`<b>${loc.name}</b><br>${loc.address}<br>Radius: ${loc.radius}m`)
        .addTo(map);

    let userMarker = null;
    let userCircle = null;

    function detectUserLocation() {
        if (!navigator.geolocation) return;
        
        const btn = document.getElementById('btn-refresh-location');
        if(btn) btn.classList.add('animate-pulse');

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                if(btn) btn.classList.remove('animate-pulse');
                const userLat = pos.coords.latitude;
                const userLng = pos.coords.longitude;
                const acc = pos.coords.accuracy;

                if (userMarker) map.removeLayer(userMarker);
                if (userCircle) map.removeLayer(userCircle);

                // Blue pulsing dot for user location
                const userIcon = L.divIcon({
                    className: '',
                    html: '<div style="width:14px;height:14px;background:#3b82f6;border:3px solid #fff;border-radius:50%;box-shadow:0 0 6px rgba(59,130,246,0.6);"></div>',
                    iconSize: [14, 14],
                    iconAnchor: [7, 7],
                });

                userMarker = L.marker([userLat, userLng], { icon: userIcon })
                    .bindPopup(`<b>Lokasi Anda</b><br>Akurasi: ±${Math.round(acc)}m`)
                    .addTo(map);

                // Accuracy circle
                userCircle = L.circle([userLat, userLng], {
                    radius: acc,
                    color: '#3b82f6',
                    fillColor: '#3b82f6',
                    fillOpacity: 0.08,
                    weight: 1,
                    dashArray: '4',
                }).addTo(map);

                // Fit map to show both location and user
                const bounds = L.latLngBounds([[loc.lat, loc.lng], [userLat, userLng]]);
                map.fitBounds(bounds.pad(0.3));
            },
            () => { 
                if(btn) btn.classList.remove('animate-pulse');
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 } // maximumAge 0 forces fresh read
        );
    }

    // Auto run on load
    detectUserLocation();

    // Bind button
    const btn = document.getElementById('btn-refresh-location');
    if (btn) {
        btn.addEventListener('click', detectUserLocation);
    }
    @endif
})();
</script>
@endpush
