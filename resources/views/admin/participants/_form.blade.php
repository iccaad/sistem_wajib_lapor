{{-- Shared form fields for create/edit participant --}}
{{-- Expects $participant (nullable for create) --}}

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
        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
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
            <option value="monthly">Bulanan (Monthly)</option>
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
    <div class="md:col-span-2">
        <label for="location_id" class="block text-sm font-medium text-gray-700 mb-1">
            Lokasi Wajib Lapor <span class="text-red-500">*</span>
        </label>
        <select name="location_id" id="location_id" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('location_id') border-red-400 @enderror">
            <option value="">-- Pilih Lokasi --</option>
            @foreach ($locations as $loc)
                <option value="{{ $loc->id }}" {{ old('location_id', $participant->location_id ?? '') == $loc->id ? 'selected' : '' }}>
                    {{ $loc->name }} — {{ $loc->address ? Str::limit($loc->address, 40) : 'Tidak ada alamat' }} (±{{ $loc->radius_meters }}m)
                </option>
            @endforeach
        </select>
        @error('location_id') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
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
                } else if (this.quotaType === 'monthly') {
                    this.numberOfPeriods = Math.round(diffDays / 30);
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
            } else if (this.quotaType === 'monthly') {
                daysToAdd = (parseInt(this.numberOfPeriods) * 30) - 1;
            }

            const end = new Date(start);
            end.setDate(end.getDate() + daysToAdd);

            this.endDate = end.toISOString().split('T')[0];
        }
    }));
});
</script>
@endpush
