{{--
    Shared Leaflet map form partial for Location create/edit.
    Requires:
      - $location (null for create, Location model for edit)
      - $formAction (string, POST URL)
      - $method ('POST' for create, 'PUT' for edit)
--}}
@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #location-map { height: 380px; border-radius: 8px; z-index: 1; }
    </style>
@endpush

<div class="max-w-3xl"
     x-data="{
        lat: {{ $location->latitude ?? -6.9667 }},
        lng: {{ $location->longitude ?? 110.4167 }},
        radius: {{ $location->radius_meters ?? 100 }},
        hasCoords: {{ $location ? 'true' : 'false' }},
        circle: null,
        marker: null,
        map: null,

        init() {
            this.map = L.map('location-map').setView([this.lat, this.lng], {{ $location ? 15 : 13 }});
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(this.map);

            @if ($location)
                this.placeMarker(this.lat, this.lng);
            @endif

            this.map.on('click', (e) => {
                this.lat = parseFloat(e.latlng.lat.toFixed(7));
                this.lng = parseFloat(e.latlng.lng.toFixed(7));
                this.hasCoords = true;
                this.placeMarker(this.lat, this.lng);
            });
        },

        placeMarker(lat, lng) {
            if (this.marker) this.map.removeLayer(this.marker);
            if (this.circle) this.map.removeLayer(this.circle);

            this.marker = L.marker([lat, lng]).addTo(this.map);
            this.circle = L.circle([lat, lng], {
                radius: this.radius,
                color: '#6366f1',
                fillColor: '#6366f1',
                fillOpacity: 0.15,
                weight: 2
            }).addTo(this.map);
        },

        updateRadius(val) {
            this.radius = parseInt(val);
            if (this.circle && this.hasCoords) {
                this.circle.setRadius(this.radius);
            }
        },

        useMyLocation() {
            if (!navigator.geolocation) return alert('Browser tidak mendukung GPS.');
            navigator.geolocation.getCurrentPosition((pos) => {
                this.lat = parseFloat(pos.coords.latitude.toFixed(7));
                this.lng = parseFloat(pos.coords.longitude.toFixed(7));
                this.hasCoords = true;
                this.map.setView([this.lat, this.lng], 16);
                this.placeMarker(this.lat, this.lng);
            }, () => alert('Gagal mendapatkan lokasi. Pastikan GPS diizinkan.'));
        }
     }">

    <div class="bg-gray-800 rounded-md border border-gray-700 shadow-md border-t-2 border-indigo-500 shadow-gray-950/50 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-700">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.locations.index') }}" class="text-gray-400 hover:text-gray-400 transition">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </a>
                <h2 class="text-base font-semibold text-gray-200">
                    {{ $location ? 'Edit Lokasi: ' . $location->name : 'Tambah Lokasi Baru' }}
                </h2>
            </div>
        </div>

        <form method="POST" action="{{ $formAction }}" class="p-6 space-y-5">
            @csrf
            @if ($method === 'PUT') @method('PUT') @endif

            {{-- Name & Address --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="loc-name" class="block text-sm font-medium text-gray-300 mb-1">Nama Lokasi <span class="text-red-500">*</span></label>
                    <input type="text" id="loc-name" name="name"
                           value="{{ old('name', $location->name ?? '') }}"
                           required
                           class="w-full px-3 py-2 text-sm border border-gray-600 rounded-md focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-400 @enderror">
                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="loc-address" class="block text-sm font-medium text-gray-300 mb-1">Alamat</label>
                    <input type="text" id="loc-address" name="address"
                           value="{{ old('address', $location->address ?? '') }}"
                           class="w-full px-3 py-2 text-sm border border-gray-600 rounded-md focus:ring-indigo-500 focus:border-indigo-500 @error('address') border-red-400 @enderror">
                    @error('address') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Radius slider --}}
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">
                    Radius Deteksi: <span class="font-bold text-indigo-400" x-text="radius + 'm'"></span>
                </label>
                <input type="range" name="radius_meters"
                       min="50" max="500" step="10"
                       x-model="radius"
                       @input="updateRadius($event.target.value)"
                       class="w-full accent-indigo-600">
                <div class="flex justify-between text-xs text-gray-400 mt-1">
                    <span>50m (ketat)</span><span>500m (longgar)</span>
                </div>
                @error('radius_meters') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Map --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium text-gray-300">Koordinat (klik peta untuk memilih)</label>
                    <button type="button" @click="useMyLocation()"
                            class="inline-flex items-center gap-1.5 text-xs font-medium text-indigo-400 hover:text-indigo-800 transition">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                        </svg>
                        Gunakan Lokasi Saya
                    </button>
                </div>

                <div id="location-map" x-init="$nextTick(() => init())"
                     class="border border-gray-700 rounded-md overflow-hidden"></div>

                {{-- Hidden lat/lng fields filled by map click --}}
                <input type="hidden" name="latitude" :value="lat">
                <input type="hidden" name="longitude" :value="lng">

                <div x-show="hasCoords"
                     class="mt-2 flex gap-4 text-xs text-gray-400 font-mono">
                    <span>Lat: <span class="text-gray-200" x-text="lat"></span></span>
                    <span>Lng: <span class="text-gray-200" x-text="lng"></span></span>
                </div>
                <p x-show="!hasCoords" class="mt-2 text-xs text-red-500">
                    * Klik pada peta untuk memilih koordinat lokasi.
                </p>
                @error('latitude') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                @error('longitude') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-700">
                <a href="{{ route('admin.locations.index') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-300 bg-gray-800 border border-gray-600 rounded-md hover:bg-gray-900 transition">Batal</a>
                <button type="submit"
                        :disabled="!hasCoords"
                        :class="hasCoords ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-indigo-300 cursor-not-allowed'"
                        class="px-5 py-2 text-sm font-medium text-white rounded-md shadow-md border-t-2 border-indigo-500 shadow-gray-950/50 transition">
                    {{ $location ? 'Simpan Perubahan' : 'Tambah Lokasi' }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush


