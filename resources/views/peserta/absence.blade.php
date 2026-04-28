@extends('layouts.participant')

@section('title', 'Absensi')

@section('content')

{{-- Validation errors --}}
@if ($errors->has('attendance'))
    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3">
        <div class="flex items-start gap-2">
            <svg class="h-5 w-5 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
            <div>
                <p class="text-sm font-semibold text-red-800">Absensi Gagal</p>
                <p class="text-sm text-red-700 mt-0.5">{{ $errors->first('attendance') }}</p>
            </div>
        </div>
    </div>
@endif

{{-- Alpine.js Absensi Form --}}
<div x-data="absensiForm()" x-init="init()">

    {{-- ── Step 1: GPS ── --}}
    <div x-show="step === 'gps'" class="space-y-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-5 py-5">
            <div class="text-center mb-5">
                <div class="inline-flex items-center justify-center h-14 w-14 rounded-full bg-blue-100 mb-3">
                    <svg class="h-7 w-7 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                    </svg>
                </div>
                <h3 class="font-bold text-slate-800 text-lg">Verifikasi Lokasi</h3>
                <p class="text-sm text-slate-500 mt-1">Pastikan Anda berada di lokasi wajib lapor yang telah ditentukan.</p>
            </div>

            {{-- GPS status --}}
            <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 mb-4 text-sm">
                <template x-if="gpsState === 'idle'">
                    <p class="text-slate-500 text-center">Tekan tombol di bawah untuk mendeteksi lokasi Anda.</p>
                </template>
                <template x-if="gpsState === 'loading'">
                    <div class="flex items-center justify-center gap-2 text-blue-600">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span>Mendeteksi lokasi...</span>
                    </div>
                </template>
                <template x-if="gpsState === 'success'">
                    <div>
                        <div class="flex items-center gap-2 text-emerald-600 font-semibold mb-2">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            </svg>
                            Lokasi terdeteksi
                        </div>
                        <p class="text-xs text-slate-500 font-mono" x-text="`Koordinat: ${lat?.toFixed(6)}, ${lng?.toFixed(6)}`"></p>
                        <p class="text-xs text-slate-500" x-text="`Akurasi: ±${Math.round(accuracy || 0)}m`"></p>

                        {{-- Within/Outside radius indicator --}}
                        <template x-if="distanceToLocation !== null">
                            <div class="mt-2">
                                <template x-if="withinRadius">
                                    <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-emerald-50 border border-emerald-200">
                                        <span class="text-lg">✅</span>
                                        <div>
                                            <p class="text-sm font-semibold text-emerald-700">Anda di dalam area wajib lapor</p>
                                            <p class="text-xs text-emerald-600" x-text="`Jarak: ${Math.round(distanceToLocation)}m dari lokasi`"></p>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!withinRadius">
                                    <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-red-50 border border-red-200">
                                        <span class="text-lg">❌</span>
                                        <div>
                                            <p class="text-sm font-semibold text-red-700">Anda di luar area wajib lapor</p>
                                            <p class="text-xs text-red-600" x-text="`Jarak: ${Math.round(distanceToLocation)}m (perlu ≤ ${locationRadius}m)`"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>
                <template x-if="gpsState === 'error'">
                    <div class="flex items-center gap-2 text-red-600 text-sm">
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                        </svg>
                        <span x-text="gpsError"></span>
                    </div>
                </template>
            </div>

            {{-- Required location for this check-in --}}
            @if ($location)
                <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-xl">
                    <p class="text-xs font-semibold text-blue-800 uppercase tracking-wider mb-1">📍 Lokasi Wajib Lapor</p>
                    <p class="text-sm font-medium text-slate-800">{{ $location->name }}</p>
                    <p class="text-xs text-blue-700 mt-2">
                        Anda harus berada dalam radius <strong>{{ $location->radius_meters }}m</strong> dari lokasi ini.
                    </p>
                    @if ($location->address)
                        <p class="text-xs text-slate-500 mt-1">{{ $location->address }}</p>
                    @endif
                </div>
            @endif

            <button type="button" @click="detectGPS()"
                    :disabled="gpsState === 'loading'"
                    :class="gpsState === 'loading' ? 'opacity-60 cursor-not-allowed' : 'hover:bg-blue-700 active:scale-95'"
                    class="w-full py-3.5 rounded-xl bg-blue-600 text-white font-semibold text-sm transition-all duration-150">
                <span x-show="gpsState !== 'loading'">📍 Deteksi Lokasi GPS</span>
                <span x-show="gpsState === 'loading'">Mendeteksi...</span>
            </button>

            <button type="button" @click="step = 'camera'"
                    x-show="gpsState === 'success'"
                    x-cloak
                    :disabled="!withinRadius"
                    :class="!withinRadius ? 'opacity-50 cursor-not-allowed bg-slate-400' : 'bg-emerald-600 hover:bg-emerald-700 active:scale-95'"
                    class="mt-3 w-full py-3.5 rounded-xl text-white font-semibold text-sm transition-all duration-150">
                Lanjut → Foto Selfie
            </button>
        </div>
    </div>

    {{-- ── Step 2: Camera ── --}}
    <div x-show="step === 'camera'" x-cloak class="space-y-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-5 py-5">
            <div class="text-center mb-5">
                <div class="inline-flex items-center justify-center h-14 w-14 rounded-full bg-emerald-100 mb-3">
                    <svg class="h-7 w-7 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                    </svg>
                </div>
                <h3 class="font-bold text-slate-800 text-lg">Foto Selfie</h3>
                <p class="text-sm text-slate-500 mt-1">Ambil foto selfie dengan wajah terlihat jelas. Pastikan pencahayaan cukup.</p>
            </div>

            {{-- Video preview or captured image --}}
            <div class="relative rounded-xl overflow-hidden bg-slate-900 mb-4" style="aspect-ratio: 4/3;">
                <video id="camera-video" autoplay playsinline muted
                       class="w-full h-full object-cover"
                       x-show="!photoTaken"></video>
                <img id="photo-preview"
                     class="w-full h-full object-cover"
                     x-show="photoTaken"
                     :src="photoDataUrl"
                     alt="Foto selfie" />
                {{-- Camera not started overlay --}}
                <div class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900/70 text-white"
                     x-show="!cameraStarted && !photoTaken">
                    <svg class="h-10 w-10 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                    </svg>
                    <p class="text-sm opacity-70">Kamera belum aktif</p>
                </div>
            </div>
            {{-- Hidden canvas for capture --}}
            <canvas id="photo-canvas" class="hidden"></canvas>

            {{-- Camera controls --}}
            <div class="space-y-2">
                <button type="button" @click="startCamera()"
                        x-show="!cameraStarted && !photoTaken"
                        class="w-full py-3.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-semibold text-sm transition-all active:scale-95">
                    📷 Aktifkan Kamera
                </button>

                <button type="button" @click="takePhoto()"
                        x-show="cameraStarted && !photoTaken"
                        x-cloak
                        class="w-full py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm transition-all active:scale-95">
                    🤳 Ambil Foto
                </button>

                <template x-if="photoTaken">
                    <div class="flex gap-2">
                        <button type="button" @click="retakePhoto()"
                                class="flex-1 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm transition-all active:scale-95">
                            🔄 Ulang Foto
                        </button>
                        <button type="button" @click="step = 'confirm'"
                                class="flex-1 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm transition-all active:scale-95">
                            Lanjut ✓
                        </button>
                    </div>
                </template>
            </div>

            <button type="button" @click="step = 'gps'" class="mt-3 text-sm text-blue-600 hover:text-blue-800 w-full text-center">
                ← Kembali
            </button>
        </div>
    </div>

    {{-- ── Step 3: Confirm & Submit ── --}}
    <div x-show="step === 'confirm'" x-cloak class="space-y-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-5 py-5">
            <div class="text-center mb-5">
                <div class="inline-flex items-center justify-center h-14 w-14 rounded-full bg-indigo-100 mb-3">
                    <svg class="h-7 w-7 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                </div>
                <h3 class="font-bold text-slate-800 text-lg">Konfirmasi Absensi</h3>
                <p class="text-sm text-slate-500 mt-1">Periksa kembali sebelum mengirim.</p>
            </div>

            {{-- Summary --}}
            <div class="bg-slate-50 rounded-xl divide-y divide-slate-100 mb-5">
                <div class="px-4 py-3 flex justify-between text-sm">
                    <span class="text-slate-500">Tanggal</span>
                    <span class="font-semibold text-slate-800">{{ today()->translatedFormat('l, d F Y') }}</span>
                </div>
                <div class="px-4 py-3 flex justify-between text-sm">
                    <span class="text-slate-500">Koordinat GPS</span>
                    <span class="font-mono text-xs text-slate-700" x-text="`${lat?.toFixed(6)}, ${lng?.toFixed(6)}`"></span>
                </div>
                <div class="px-4 py-3 flex justify-between text-sm">
                    <span class="text-slate-500">Akurasi</span>
                    <span class="font-semibold text-slate-800" x-text="`±${Math.round(accuracy || 0)}m`"></span>
                </div>
                <div class="px-4 py-3">
                    <p class="text-xs text-slate-500 mb-2">Foto Selfie:</p>
                    <img :src="photoDataUrl" alt="Selfie" class="w-24 h-24 object-cover rounded-lg border border-slate-200 mx-auto">
                </div>
            </div>

            {{-- Hidden real form --}}
            <form method="POST" action="{{ route('peserta.absence.store') }}" enctype="multipart/form-data"
                  x-ref="submitForm" id="absence-form">
                @csrf
                <input type="hidden" name="latitude"  :value="lat">
                <input type="hidden" name="longitude" :value="lng">
                <input type="hidden" name="accuracy"  :value="accuracy">
                <input type="file"   name="photo"     x-ref="photoInput" class="hidden" accept="image/*">
            </form>

            <button type="button" @click="submitAbsence()"
                    :disabled="submitting"
                    :class="submitting ? 'opacity-60 cursor-not-allowed' : 'hover:bg-indigo-700 active:scale-95'"
                    class="w-full py-4 rounded-xl bg-indigo-600 text-white font-bold text-base shadow-lg shadow-indigo-200 transition-all duration-150">
                <span x-show="!submitting">✅ Kirim Absensi</span>
                <div x-show="submitting" class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Mengirim...
                </div>
            </button>

            <button type="button" @click="step = 'camera'" class="mt-3 text-sm text-blue-600 hover:text-blue-800 w-full text-center">
                ← Ulangi Foto
            </button>
        </div>
    </div>

    {{-- Step indicator --}}
    <div class="flex justify-center gap-2 mt-5">
        <div class="h-2 rounded-full transition-all duration-300" :class="step === 'gps'     ? 'w-8 bg-blue-600' : 'w-2 bg-slate-200'"></div>
        <div class="h-2 rounded-full transition-all duration-300" :class="step === 'camera'  ? 'w-8 bg-blue-600' : 'w-2 bg-slate-200'"></div>
        <div class="h-2 rounded-full transition-all duration-300" :class="step === 'confirm' ? 'w-8 bg-blue-600' : 'w-2 bg-slate-200'"></div>
    </div>

</div>

@endsection

@push('scripts')
<script>
function absensiForm() {
    return {
        step: 'gps',
        // GPS
        gpsState: 'idle',   // idle | loading | success | error
        gpsError: '',
        lat: null,
        lng: null,
        accuracy: null,
        distanceToLocation: null,
        withinRadius: false,
        locationLat: {{ $location ? (float) $location->latitude : 'null' }},
        locationLng: {{ $location ? (float) $location->longitude : 'null' }},
        locationRadius: {{ $location ? $location->radius_meters : 0 }},
        // Camera
        cameraStarted: false,
        photoTaken: false,
        photoDataUrl: '',
        photoBlob: null,
        videoStream: null,
        // Submit
        submitting: false,

        init() {
            // Nothing needed on init
        },

        // ── GPS ──────────────────────────────────────────────
        detectGPS() {
            if (!navigator.geolocation) {
                this.gpsState = 'error';
                this.gpsError = 'Browser ini tidak mendukung GPS. Gunakan browser modern.';
                return;
            }
            this.gpsState = 'loading';
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.lat      = pos.coords.latitude;
                    this.lng      = pos.coords.longitude;
                    this.accuracy = pos.coords.accuracy;
                    this.gpsState = 'success';

                    // Calculate distance to required location (Haversine)
                    if (this.locationLat !== null && this.locationLng !== null) {
                        this.distanceToLocation = this.haversine(this.lat, this.lng, this.locationLat, this.locationLng);
                        this.withinRadius = this.distanceToLocation <= this.locationRadius;
                    }
                },
                (err) => {
                    this.gpsState = 'error';
                    switch (err.code) {
                        case err.PERMISSION_DENIED:
                            this.gpsError = 'Akses GPS ditolak. Izinkan lokasi di pengaturan browser.'; break;
                        case err.POSITION_UNAVAILABLE:
                            this.gpsError = 'Sinyal GPS tidak tersedia. Coba pindah ke area terbuka.'; break;
                        case err.TIMEOUT:
                            this.gpsError = 'Waktu deteksi GPS habis. Coba lagi.'; break;
                        default:
                            this.gpsError = 'Gagal mendeteksi GPS. Coba lagi.';
                    }
                },
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
            );
        },

        // Haversine distance in meters (client-side)
        haversine(lat1, lng1, lat2, lng2) {
            const R = 6371000;
            const toRad = d => d * Math.PI / 180;
            const dLat = toRad(lat2 - lat1);
            const dLng = toRad(lng2 - lng1);
            const a = Math.sin(dLat/2)**2 + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLng/2)**2;
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        },

        // ── Camera ───────────────────────────────────────────
        async startCamera() {
            try {
                this.videoStream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 960 } }
                });
                const video = document.getElementById('camera-video');
                video.srcObject = this.videoStream;
                this.cameraStarted = true;
            } catch (e) {
                alert('Gagal mengakses kamera: ' + e.message + '. Pastikan izin kamera diberikan.');
            }
        },

        takePhoto() {
            const video  = document.getElementById('camera-video');
            const canvas = document.getElementById('photo-canvas');
            canvas.width  = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');
            // Mirror front camera
            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1);
            ctx.drawImage(video, 0, 0);
            ctx.setTransform(1, 0, 0, 1, 0, 0);

            this.photoDataUrl = canvas.toDataURL('image/jpeg', 0.85);
            canvas.toBlob((blob) => {
                this.photoBlob = blob;
            }, 'image/jpeg', 0.85);
            this.photoTaken = true;

            // Stop camera stream
            if (this.videoStream) {
                this.videoStream.getTracks().forEach(t => t.stop());
                this.videoStream = null;
            }
        },

        retakePhoto() {
            this.photoTaken   = false;
            this.photoDataUrl = '';
            this.photoBlob    = null;
            this.cameraStarted = false;
            const video = document.getElementById('camera-video');
            video.srcObject = null;
        },

        // ── Submit ───────────────────────────────────────────
        async submitAbsence() {
            if (!this.photoBlob) {
                alert('Foto selfie diperlukan.'); return;
            }
            this.submitting = true;

            // Attach photo blob to the hidden file input
            const dt = new DataTransfer();
            dt.items.add(new File([this.photoBlob], 'selfie.jpg', { type: 'image/jpeg' }));
            this.$refs.photoInput.files = dt.files;

            this.$refs.submitForm.submit();
        },
    };
}
</script>
@endpush
