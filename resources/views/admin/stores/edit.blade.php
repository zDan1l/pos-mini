@extends('layouts.admin')

@section('title', 'Edit Toko')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Toko</h2>

        <form action="{{ route('admin.stores.update', $store->idtoko) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Barcode</label>
                    <input type="text" value="{{ $store->barcode }}" readonly class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Toko</label>
                    <input type="text" name="nama_toko" value="{{ $store->nama_toko }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <textarea name="alamat" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">{{ $store->alamat }}</textarea>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="font-medium text-blue-800 mb-2">Lokasi Toko</h3>
                    <p class="text-sm text-blue-600 mb-3">Perbarui lokasi toko dengan mengambil koordinat baru.</p>

                    <div class="flex gap-2 mb-4">
                        <button type="button" id="getLocationBtn" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors flex items-center gap-2">
                            <i class="ph ph-arrows-clockwise"></i> Update Lokasi
                        </button>
                    </div>

                    <div id="locationStatus" class="text-sm text-gray-600 mb-3"></div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Latitude</label>
                            <input type="number" step="any" name="latitude" value="{{ $store->latitude }}" required class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-orange-500" id="latitude">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Longitude</label>
                            <input type="number" step="any" name="longitude" value="{{ $store->longitude }}" required class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-orange-500" id="longitude">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Accuracy (m)</label>
                            <input type="number" step="any" name="accuracy" value="{{ $store->accuracy }}" required class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-orange-500" id="accuracy">
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-orange-500 text-white py-3 px-6 rounded-lg hover:bg-orange-600 transition-colors font-semibold">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.stores.index') }}" class="flex-1 bg-gray-200 text-gray-700 py-3 px-6 rounded-lg hover:bg-gray-300 transition-colors font-semibold text-center">
                        Batal
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const MAX_WAIT = 20000;
const TARGET_ACCURACY = 50;

function getAccuratePosition() {
    return new Promise((resolve, reject) => {
        let bestResult = null;
        const startTime = Date.now();

        const statusEl = document.getElementById('locationStatus');
        const getBtn = document.getElementById('getLocationBtn');

        statusEl.textContent = 'Mengambil lokasi...';
        getBtn.disabled = true;

        const watchId = navigator.geolocation.watchPosition(
            (position) => {
                const acc = position.coords.accuracy;

                if (!bestResult || acc < bestResult.coords.accuracy) {
                    bestResult = position;
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                    document.getElementById('accuracy').value = position.coords.accuracy;
                    statusEl.textContent = `Akurasi saat ini: ${Math.round(acc)} meter`;
                }

                if (acc <= TARGET_ACCURACY) {
                    navigator.geolocation.clearWatch(watchId);
                    statusEl.textContent = `Lokasi terkunci! Akurasi: ${Math.round(acc)} meter`;
                    getBtn.disabled = false;
                    resolve(bestResult);
                }

                if (Date.now() - startTime >= MAX_WAIT) {
                    navigator.geolocation.clearWatch(watchId);
                    if (bestResult) {
                        statusEl.textContent = `Timeout. Menggunakan akurasi terbaik: ${Math.round(bestResult.coords.accuracy)} meter`;
                        getBtn.disabled = false;
                        resolve(bestResult);
                    } else {
                        statusEl.textContent = 'Gagal mendapatkan lokasi';
                        getBtn.disabled = false;
                        reject(new Error('Timeout'));
                    }
                }
            },
            (error) => {
                navigator.geolocation.clearWatch(watchId);
                statusEl.textContent = 'Error: ' + error.message;
                getBtn.disabled = false;
                reject(error);
            },
            { enableHighAccuracy: true, maximumAge: 0, timeout: MAX_WAIT }
        );
    });
}

document.getElementById('getLocationBtn').addEventListener('click', getAccuratePosition);
</script>
@endpush
@endsection
