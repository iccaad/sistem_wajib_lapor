{{-- Shared form fields for create/edit participant --}}
{{-- Expects $participant (nullable for create) --}}

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

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
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono tracking-wider focus:ring-indigo-500 focus:border-indigo-500 @error('nik') border-red-400 @enderror">
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
        <label for="violation_type" class="block text-sm font-medium text-gray-700 mb-1">Jenis Pelanggaran <span class="text-red-500">*</span></label>
        <input type="text" id="violation_type" name="violation_type"
               value="{{ old('violation_type', $participant->violation_type ?? '') }}"
               required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('violation_type') border-red-400 @enderror">
        @error('violation_type') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
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
               value="{{ old('supervision_start', isset($participant) ? $participant->supervision_start->format('Y-m-d') : '') }}"
               required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('supervision_start') border-red-400 @enderror">
        @error('supervision_start') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
    </div>

    {{-- Tanggal Selesai --}}
    <div>
        <label for="supervision_end" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
        <input type="date" id="supervision_end" name="supervision_end"
               value="{{ old('supervision_end', isset($participant) ? $participant->supervision_end->format('Y-m-d') : '') }}"
               required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('supervision_end') border-red-400 @enderror">
        @error('supervision_end') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
    </div>

    {{-- Tipe Kuota --}}
    <div>
        <label for="quota_type" class="block text-sm font-medium text-gray-700 mb-1">Tipe Kuota <span class="text-red-500">*</span></label>
        <select id="quota_type" name="quota_type" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('quota_type') border-red-400 @enderror">
            <option value="">-- Pilih --</option>
            <option value="weekly" {{ old('quota_type', $participant->quota_type ?? '') === 'weekly' ? 'selected' : '' }}>Mingguan (Weekly)</option>
            <option value="monthly" {{ old('quota_type', $participant->quota_type ?? '') === 'monthly' ? 'selected' : '' }}>Bulanan (Monthly)</option>
        </select>
        @error('quota_type') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
    </div>

    {{-- Jumlah Kuota --}}
    <div>
        <label for="quota_amount" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Kuota <span class="text-red-500">*</span></label>
        <input type="number" id="quota_amount" name="quota_amount"
               value="{{ old('quota_amount', $participant->quota_amount ?? '') }}"
               min="1" max="30" required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('quota_amount') border-red-400 @enderror">
        @error('quota_amount') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
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
