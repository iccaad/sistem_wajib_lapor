{{-- Shared form fields for create/edit participant --}}
{{-- Expects $participant (nullable for create) --}}

{{-- OCR KTP Scanner — only on create form --}}
@if(isset($isCreateForm) && $isCreateForm)
<div class="mb-6" x-data="ktpScanner()">

    {{-- Section Header --}}
    <div class="md:col-span-2 mb-4">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1 border-b pb-2">Scan KTP (Opsional)</h3>
        <p class="text-xs text-gray-400">Scan KTP untuk mengisi otomatis NIK, Nama, dan Alamat peserta.</p>
    </div>

    {{-- Action Buttons --}}
    <div class="flex flex-wrap gap-3 mb-4">
        <button type="button" @click="openCamera()" id="btn-scan-ktp-camera"
                class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200"
                :class="cameraActive
                    ? 'bg-red-50 text-red-600 border border-red-200 hover:bg-red-100'
                    : 'bg-indigo-50 text-indigo-600 border border-indigo-200 hover:bg-indigo-100'"
                :disabled="processing">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z" />
            </svg>
            <span x-text="cameraActive ? 'Tutup Kamera' : 'Scan KTP via Kamera'"></span>
        </button>

        <label id="btn-scan-ktp-upload"
               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-200 hover:bg-emerald-100 transition-all duration-200 cursor-pointer"
               :class="processing ? 'opacity-50 pointer-events-none' : ''">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Z" />
            </svg>
            <span>Upload dari Galeri</span>
            <input type="file" accept="image/*" class="hidden" @change="handleFileUpload($event)" :disabled="processing">
        </label>
    </div>

    {{-- Camera / Preview Area --}}
    <div x-show="cameraActive || previewSrc" x-transition
         class="relative rounded-xl overflow-hidden border-2 border-dashed border-gray-200 bg-gray-50 mb-4">

        {{-- Video Stream --}}
        <div x-show="cameraActive && !previewSrc" class="relative">
            <video x-ref="videoEl" autoplay playsinline muted
                   class="w-full max-h-80 object-contain bg-black rounded-t-xl"></video>
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2">
                <button type="button" @click="captureFrame()" id="btn-capture-ktp"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-full shadow-lg transition-all duration-200 active:scale-95">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 3.75H6A2.25 2.25 0 0 0 3.75 6v1.5M16.5 3.75H18A2.25 2.25 0 0 1 20.25 6v1.5m0 9V18A2.25 2.25 0 0 1 18 20.25h-1.5m-9 0H6A2.25 2.25 0 0 1 3.75 18v-1.5" />
                    </svg>
                    Capture & Proses OCR
                </button>
            </div>
        </div>

        {{-- Image Preview --}}
        <div x-show="previewSrc" class="relative">
            <img :src="previewSrc" alt="KTP Preview" class="w-full max-h-80 object-contain bg-gray-100">
            <button type="button" @click="clearPreview()"
                    class="absolute top-2 right-2 p-1.5 bg-white/90 hover:bg-white rounded-full shadow-md transition">
                <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Hidden canvas for frame capture --}}
    <canvas x-ref="canvasEl" class="hidden"></canvas>

    {{-- Processing Indicator --}}
    <div x-show="processing" x-transition class="mb-4">
        <div class="flex items-center gap-3 p-4 bg-indigo-50 border border-indigo-200 rounded-lg">
            <svg class="h-5 w-5 text-indigo-500 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <div class="flex-1">
                <p class="text-sm font-medium text-indigo-700">Memproses OCR...</p>
                <p class="text-xs text-indigo-500 mt-0.5" x-text="progressText"></p>
            </div>
            <span class="text-sm font-bold text-indigo-600" x-text="progressPercent + '%'"></span>
        </div>
        <div class="mt-2 w-full bg-indigo-100 rounded-full h-1.5">
            <div class="bg-indigo-500 h-1.5 rounded-full transition-all duration-300" :style="'width: ' + progressPercent + '%'"></div>
        </div>
    </div>

    {{-- OCR Result Status --}}
    <div x-show="ocrDone && !processing" x-transition class="mb-4">
        <div class="flex items-center gap-3 p-4 rounded-lg"
             :class="ocrSuccess ? 'bg-emerald-50 border border-emerald-200' : 'bg-amber-50 border border-amber-200'">
            <template x-if="ocrSuccess">
                <svg class="h-5 w-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </template>
            <template x-if="!ocrSuccess">
                <svg class="h-5 w-5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </template>
            <div>
                <p class="text-sm font-medium" :class="ocrSuccess ? 'text-emerald-700' : 'text-amber-700'" x-text="ocrMessage"></p>
                <p class="text-xs mt-0.5" :class="ocrSuccess ? 'text-emerald-500' : 'text-amber-500'">Periksa dan koreksi data di bawah jika perlu.</p>
            </div>
        </div>
    </div>
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-6" x-data="periodCalculator()">

    {{-- Section: Identitas --}}
    <div class="md:col-span-2">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3 border-b pb-2">Identitas</h3>
    </div>

    {{-- Nama Lengkap --}}
    <div>
        <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
        <input type="text" id="full_name" name="full_name"
               value="{{ old('full_name', $participant->full_name ?? '') }}"
               required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('full_name') border-red-400 @enderror">
        @error('full_name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
    </div>

    {{-- NIK --}}
    <div>
        <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">NIK <span class="text-red-500">*</span></label>
        <input type="text" id="nik" name="nik"
               value="{{ old('nik', $participant->nik ?? '') }}"
               maxlength="16" inputmode="numeric" pattern="[0-9]*"
               required
               {{ isset($nikReadonly) && $nikReadonly ? 'readonly' : '' }}
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono tracking-wider focus:ring-indigo-500 focus:border-indigo-500 @error('nik') border-red-400 @enderror {{ isset($nikReadonly) && $nikReadonly ? 'bg-gray-50 cursor-not-allowed' : '' }}">
        @error('nik') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
    </div>

    {{-- Alamat --}}
    <div class="md:col-span-2">
        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Alamat (Kecamatan, Kel/Desa, RT/RW)</label>
        <textarea id="address" name="address" rows="2"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('address') border-red-400 @enderror">{{ old('address', $participant->address ?? '') }}</textarea>
        @error('address') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
    </div>

    {{-- Telepon --}}
    <div>
        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
        <input type="text" id="phone" name="phone"
               value="{{ old('phone', $participant->phone ?? '') }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('phone') border-red-400 @enderror">
        @error('phone') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
    </div>

    {{-- Section: Pelanggaran --}}
    <div class="md:col-span-2 mt-2">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3 border-b pb-2">Detail Pelanggaran</h3>
    </div>

    {{-- Jenis Pelanggaran --}}
    <div>
        <label for="violation_type_id" class="block text-sm font-medium text-gray-700 mb-1">Jenis Pelanggaran <span class="text-red-500">*</span></label>
        <select id="violation_type_id" name="violation_type_id" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('violation_type_id') border-red-400 @enderror">
            <option value="">-- Pilih Jenis Pelanggaran --</option>
            @foreach($violationTypes as $vt)
                <option value="{{ $vt->id }}" {{ old('violation_type_id', $participant->violation_type_id ?? '') == $vt->id ? 'selected' : '' }}>
                    {{ $vt->name }}
                </option>
            @endforeach
        </select>
        @error('violation_type_id') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
    </div>

    {{-- Catatan Kasus --}}
    <div class="md:col-span-2">
        <label for="case_notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan Kasus</label>
        <textarea id="case_notes" name="case_notes" rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('case_notes') border-red-400 @enderror">{{ old('case_notes', $participant->case_notes ?? '') }}</textarea>
        @error('case_notes') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
    </div>

    {{-- Section: Pengawasan --}}
    <div class="md:col-span-2 mt-2">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3 border-b pb-2">Pengawasan & Kuota</h3>
    </div>

    {{-- Tanggal Mulai --}}
    <div>
        <label for="supervision_start" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
        <input type="date" id="supervision_start" name="supervision_start"
               x-model="startDate" @change="calculateEndDate()"
               required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('supervision_start') border-red-400 @enderror">
        @error('supervision_start') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
    </div>

    {{-- Tipe Kuota --}}
    <div>
        <label for="quota_type" class="block text-sm font-medium text-gray-700 mb-1">Tipe Kuota <span class="text-red-500">*</span></label>
        <select id="quota_type" name="quota_type" x-model="quotaType" @change="calculateEndDate()" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('quota_type') border-red-400 @enderror">
            <option value="">-- Pilih --</option>
            <option value="weekly">Mingguan (Weekly)</option>
        </select>
        @error('quota_type') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
    </div>

    {{-- Jumlah Periode --}}
    <div>
        <label for="number_of_periods" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Periode <span class="text-red-500">*</span></label>
        <input type="number" id="number_of_periods" x-model="numberOfPeriods" @input="calculateEndDate()"
               min="1" max="100" placeholder="Misal: 3"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
        <p class="mt-1 text-xs text-gray-500">Isi ini untuk menghitung otomatis tanggal selesai.</p>
    </div>

    {{-- Tanggal Selesai --}}
    <div>
        <label for="supervision_end" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
        <input type="date" id="supervision_end" name="supervision_end"
               x-model="endDate" readonly
               required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50 cursor-not-allowed focus:ring-indigo-500 focus:border-indigo-500 @error('supervision_end') border-red-400 @enderror">
        @error('supervision_end') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
    </div>

    {{-- Jumlah Kuota --}}
    <div>
        <label for="quota_amount" class="block text-sm font-medium text-gray-700 mb-1">Target Kuota per Periode <span class="text-red-500">*</span></label>
        <input type="number" id="quota_amount" name="quota_amount"
               value="{{ old('quota_amount', $participant->quota_amount ?? '') }}"
               min="1" max="30" required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('quota_amount') border-red-400 @enderror">
        @error('quota_amount') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
    </div>

    {{-- Section: Lokasi Wajib Lapor --}}
    <div class="md:col-span-2 mt-2">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3 border-b pb-2">Lokasi Wajib Lapor</h3>
        <p class="text-xs text-gray-400 -mt-1 mb-3">Tetapkan satu lokasi wajib lapor untuk peserta ini. Semua absensi harus dilakukan di dalam radius lokasi ini.</p>
    </div>

    {{-- Single Location Selector --}}
    <div class="md:col-span-2 flex flex-col md:flex-row md:items-center gap-2 md:gap-4">
        <label for="location_id" class="text-sm font-medium text-gray-700 md:w-1/4 whitespace-nowrap">
            Lokasi Wajib Lapor <span class="text-red-500">*</span>
        </label>
        <div class="flex-1">
            <select name="location_id" id="location_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('location_id') border-red-400 @enderror">
                <option value="">-- Pilih Lokasi --</option>
                @foreach ($locations as $loc)
                    <option value="{{ $loc->id }}" {{ old('location_id', $participant->location_id ?? '') == $loc->id ? 'selected' : '' }}>
                        {{ $loc->name }} (±{{ $loc->radius_meters }}m)
                    </option>
                @endforeach
            </select>
            @error('location_id') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- Status --}}
    <div>
        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
        <select id="status" name="status" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('status') border-red-400 @enderror">
            <option value="active" {{ old('status', $participant->status ?? 'active') === 'active' ? 'selected' : '' }}>Aktif</option>
            <option value="inactive" {{ old('status', $participant->status ?? '') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
        </select>
        @error('status') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('periodCalculator', () => ({
        startDate: '{{ old('supervision_start', isset($participant) ? $participant->supervision_start->format('Y-m-d') : '') }}',
        quotaType: '{{ old('quota_type', $participant->quota_type ?? '') }}',
        numberOfPeriods: '',
        endDate: '{{ old('supervision_end', isset($participant) ? $participant->supervision_end->format('Y-m-d') : '') }}',

        init() {
            if (this.startDate && this.endDate && this.quotaType) {
                const start = new Date(this.startDate);
                const end = new Date(this.endDate);
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // +1 because inclusive boundaries

                if (this.quotaType === 'weekly') {
                    this.numberOfPeriods = Math.round(diffDays / 7);
                }
            }
        },

        calculateEndDate() {
            if (!this.startDate || !this.quotaType || !this.numberOfPeriods) {
                return;
            }

            const start = new Date(this.startDate);
            let daysToAdd = 0;

            if (this.quotaType === 'weekly') {
                daysToAdd = (parseInt(this.numberOfPeriods) * 7) - 1;
            }

            const end = new Date(start);
            end.setDate(end.getDate() + daysToAdd);

            this.endDate = end.toISOString().split('T')[0];
        }
    }));

    Alpine.data('ktpScanner', () => ({
        cameraActive: false,
        previewSrc: null,
        processing: false,
        ocrDone: false,
        ocrSuccess: false,
        ocrMessage: '',
        progressText: 'Menginisialisasi...',
        progressPercent: 0,
        mediaStream: null,

        /**
         * Toggle camera on/off. Uses back-facing camera (environment) for mobile.
         */
        async openCamera() {
            if (this.cameraActive) {
                this.stopCamera();
                return;
            }

            try {
                this.clearPreview();
                this.mediaStream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'environment', width: { ideal: 1920 }, height: { ideal: 1080 } }
                });
                this.$refs.videoEl.srcObject = this.mediaStream;
                this.cameraActive = true;
            } catch (err) {
                alert('Gagal mengakses kamera: ' + err.message + '\n\nPastikan browser memiliki izin kamera.');
            }
        },

        /**
         * Stop camera stream and release resources.
         */
        stopCamera() {
            if (this.mediaStream) {
                this.mediaStream.getTracks().forEach(track => track.stop());
                this.mediaStream = null;
            }
            if (this.$refs.videoEl) {
                this.$refs.videoEl.srcObject = null;
            }
            this.cameraActive = false;
        },

        /**
         * Capture current video frame to canvas, then run OCR.
         */
        captureFrame() {
            const video = this.$refs.videoEl;
            const canvas = this.$refs.canvasEl;

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0);

            this.previewSrc = canvas.toDataURL('image/jpeg', 0.92);
            this.stopCamera();
            this.runOcr(this.previewSrc);
        },

        /**
         * Handle file upload from gallery.
         */
        handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file) {
                return;
            }

            this.stopCamera();
            this.clearPreview();

            const reader = new FileReader();
            reader.onload = (e) => {
                this.previewSrc = e.target.result;
                this.runOcr(this.previewSrc);
            };
            reader.readAsDataURL(file);

            // Reset input so the same file can be re-selected
            event.target.value = '';
        },

        /**
         * Clear preview image and OCR status.
         */
        clearPreview() {
            this.previewSrc = null;
            this.ocrDone = false;
            this.ocrSuccess = false;
            this.ocrMessage = '';
            this.progressPercent = 0;
            this.progressText = 'Menginisialisasi...';
        },

        /**
         * Run Tesseract.js OCR on the given image source.
         * Extracts NIK, Nama, and Alamat fields and auto-fills the form.
         *
         * @param {string} imageSrc - Base64 data URL of the KTP image
         */
        async runOcr(imageSrc) {
            this.processing = true;
            this.ocrDone = false;
            this.progressPercent = 0;
            this.progressText = 'Memuat engine OCR...';

            try {
                const worker = await Tesseract.createWorker('ind', 1, {
                    logger: (m) => {
                        if (m.status === 'recognizing text') {
                            this.progressPercent = Math.round((m.progress || 0) * 100);
                            this.progressText = 'Mengenali teks...';
                        } else if (m.status === 'loading language traineddata') {
                            this.progressText = 'Memuat model bahasa...';
                            this.progressPercent = Math.round((m.progress || 0) * 30);
                        } else if (m.status === 'initializing api') {
                            this.progressText = 'Menginisialisasi OCR...';
                            this.progressPercent = 35;
                        }
                    }
                });

                const result = await worker.recognize(imageSrc);
                const text = result.data.text;

                await worker.terminate();

                this.parseKtpText(text);

            } catch (err) {
                console.error('OCR error:', err);
                this.ocrDone = true;
                this.ocrSuccess = false;
                this.ocrMessage = 'Gagal memproses OCR: ' + err.message;
            } finally {
                this.processing = false;
            }
        },

        /**
         * Parse raw OCR text from Indonesian KTP and extract fields.
         *
         * Tesseract reads KTP cards horizontally, so left-side labels often
         * mingle with right-side values on the same line. For example:
         *   'RT/RW : 002/004 Agama : ISLAM'
         *
         * This parser normalises the full text first, then uses targeted
         * regex patterns that capture only the value portion and stop
         * before the next label bleeds in.
         *
         * @param {string} text - Raw OCR text output
         */
/**
         * Parse raw OCR text from Indonesian KTP line-by-line.
         * Jauh lebih akurat dan kebal terhadap salah baca tanda baca.
         */
        parseKtpText(text) {
            // 1. Pecah teks menjadi array per baris
            const lines = text.split('\n').map(l => l.trim().toUpperCase()).filter(l => l.length > 0);

            let nik = '', nama = '', rtRw = '', kelDesa = '', kecamatan = '';

            lines.forEach(line => {
                // Bersihkan karakter aneh yang sering dibaca salah oleh OCR menjadi titik dua / garis miring
                let l = line.replace(/[;]/g, ':').replace(/[\|\\]/g, '/');

                // -- Ekstrak NIK --
                // Toleransi OCR: ubah huruf O jadi 0, huruf I/L jadi 1, hapus semua spasi
                let possibleNikLine = l.replace(/[O]/g, '0').replace(/[I|L]/g, '1').replace(/\s+/g, '');
                if ((l.includes('NIK') || /\d{14}/.test(possibleNikLine)) && !nik) {
                    const nikMatch = possibleNikLine.match(/(\d{16})/);
                    if (nikMatch) nik = nikMatch[1];
                }

                // -- Ekstrak NAMA --
                if (/NAM[AE]/i.test(l) && !nama) {
                    // Ambil apa saja setelah NAMA dan tanda baca separator apa pun
                    const match = l.match(/NAM[AE]\s*[:\.;]?\s*(.+)/);
                    if (match) {
                        // Hanya ambil huruf, spasi, dan tanda baca nama
                        nama = match[1].replace(/[^A-Z\s.,\-']/g, '').trim();
                    }
                }

                // -- Ekstrak RT/RW --
                if (!rtRw && !l.includes('LAHIR') && !l.includes('TGL')) {
                    let numSanitized = l.replace(/O/gi, '0');
                    const match = numSanitized.match(/(\d{2,3})\s*[\/\\|Il]\s*(\d{2,3})/);
                    if (match) {
                        rtRw = match[1] + '/' + match[2];
                    }
                }

                // -- Ekstrak KEL/DESA --
                if (/(?:KEL|DES[AE])/i.test(l) && !l.includes('JENIS') && !kelDesa) {
                    const match = l.match(/(?:KEL[\/]?DES[AE]|KEL|DESA)\s*[:\.;]?\s*(.+)/);
                    if (match) {
                        kelDesa = match[1].replace(/[^A-Z\s.\-]/g, '').trim();
                    }
                }

                // -- Ekstrak KECAMATAN --
                if (/KEC/i.test(l) && !kecamatan) {
                    const match = l.match(/KEC(?:[AE]M[AE]T[AE]N)?\s*[:\.;]?\s*(.+)/);
                    if (match) {
                        kecamatan = match[1].replace(/[^A-Z\s.\-]/g, '').trim();
                    }
                }
            });

            // 2. Gabungkan alamat sesuai format [Kecamatan], [Kel/Desa], [RT/RW]
            const addressParts = [kecamatan, kelDesa, rtRw].filter(p => p && p.length > 0);
            const combinedAddress = addressParts.join(', ');

            // 3. Auto-fill form fields
            let filledCount = 0;

            if (nik) {
                const nikEl = document.getElementById('nik');
                if (nikEl && !nikEl.readOnly) {
                    nikEl.value = nik;
                    nikEl.dispatchEvent(new Event('input', { bubbles: true }));
                    filledCount++;
                }
            }

            if (nama) {
                const namaEl = document.getElementById('full_name');
                if (namaEl) {
                    namaEl.value = nama;
                    namaEl.dispatchEvent(new Event('input', { bubbles: true }));
                    filledCount++;
                }
            }

            if (combinedAddress) {
                const addressEl = document.getElementById('address');
                if (addressEl) {
                    addressEl.value = combinedAddress;
                    addressEl.dispatchEvent(new Event('input', { bubbles: true }));
                    filledCount++;
                }
            }

            // Notifikasi UI
            this.ocrDone = true;
            if (filledCount > 0) {
                this.ocrSuccess = true;
                this.ocrMessage = `Berhasil mengekstrak data KTP (NIK, Nama, dan ${addressParts.length} data Alamat).`;
            } else {
                this.ocrSuccess = false;
                this.ocrMessage = 'Tidak dapat mendeteksi data KTP. Pastikan foto terang dan fokus.';
            }
        }
    }));
});
</script>
@endpush
