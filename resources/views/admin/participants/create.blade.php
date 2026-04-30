@extends('layouts.admin')

@section('title', 'Tambah Peserta')
@section('page-title', 'Tambah Peserta Baru')
@section('breadcrumb', 'Admin / Peserta / Tambah')

@section('content')

<div class="max-w-3xl">
    <div class="bg-gray-800 rounded-md border border-gray-700 shadow-md border-t-2 border-indigo-500 shadow-gray-950/50 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-700">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.participants.index') }}"
                   class="text-gray-400 hover:text-gray-400 transition">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </a>
                <h2 class="text-base font-semibold text-gray-200">Data Peserta Baru</h2>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.participants.store') }}" class="p-6"
              x-data="locationForm({{ old('quota_amount', 1) }}, {{ json_encode(old('location_ids', [''])) }})">
            @csrf
            @include('admin.participants._form', ['participant' => null])

            <div class="flex justify-end gap-3 mt-6 pt-5 border-t border-gray-700">
                <a href="{{ route('admin.participants.index') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-300 bg-gray-800 border border-gray-600 rounded-md hover:bg-gray-900 transition">
                    Batal
                </a>
                <button type="submit" id="btn-simpan-peserta"
                        class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md shadow-md border-t-2 border-indigo-500 shadow-gray-950/50 transition">
                    Simpan Peserta
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function locationForm(initialQuota, initialSlots) {
    return {
        quotaAmount: initialQuota,
        locationSlots: initialSlots.map(v => v ? String(v) : ''),
        init() {
            this.$watch('quotaAmount', (val) => {
                val = Math.max(1, Math.min(30, val || 1));
                while (this.locationSlots.length < val) this.locationSlots.push('');
                while (this.locationSlots.length > val) this.locationSlots.pop();
            });
            let q = this.quotaAmount || 1;
            while (this.locationSlots.length < q) this.locationSlots.push('');
            while (this.locationSlots.length > q) this.locationSlots.pop();
        }
    };
}
</script>
@endpush



