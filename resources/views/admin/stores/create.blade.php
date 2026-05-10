@extends('layouts.admin')

@section('title', 'Tambah Toko')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Tambah Toko Baru</h2>

        <form action="{{ route('admin.stores.store') }}" method="POST" id="storeForm">
            @csrf

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Toko</label>
                    <input type="text" name="nama_toko" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <textarea name="alamat" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"></textarea>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="font-medium text-blue-800 mb-2">Ambil Lokasi Toko</h3>
                    <p class="text-sm text-blue-600 mb-3">Klik tombol di bawah untuk mengambil lokasi toko saat ini. Pastikan akurasi yang didapat sudah baik.</p>

                    <div class="flex gap-2 mb-4">
                        <button type="button" id="getLocationBtn" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors flex items-center gap-2">
                            <i class="ph ph-map-pin"></i> Ambil Lokasi
                        </button>
                        <button type="button" id="refreshLocationBtn" class="hidden bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition-colors flex items-center gap-2">
                            <i class="ph ph-arrows-clockwise"></i> Refresh
                        </button>
                    </div>

                    <div id="locationStatus" class="text-sm text-gray-600 mb-3"></div>

                    <div id="locationInfo" class="hidden grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Latitude</label>
                            <input type="text" id="displayLat" readonly class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded text-sm">
                            <input type="hidden" name="latitude" id="latitude" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Longitude</label>
                            <input type="text" id="displayLng" readonly class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded text-sm">
                            <input type="hidden" name="longitude" id="longitude" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Accuracy</label>
                            <input type="text" id="displayAccuracy" readonly class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded text-sm">
                            <input type="hidden" name="accuracy" id="accuracy" required>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" id="submitBtn" disabled class="flex-1 bg-orange-500 text-white py-3 px-6 rounded-lg hover:bg-orange-600 transition-colors font-semibold disabled:bg-gray-400 disabled:cursor-not-allowed">
                        Simpan Toko
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
        const refreshBtn = document.getElementById('refreshLocationBtn');

        statusEl.textContent = 'Mengambil lokasi...';
        getBtn.disabled = true;

        const watchId = navigator.geolocation.watchPosition(
            (position) => {
                const acc = position.coords.accuracy;

                if (!bestResult || acc < bestResult.coords.accuracy) {
                    bestResult = position;
                    updateLocationDisplay(bestResult);
                    statusEl.textContent = `Akurasi saat ini: ${Math.round(acc)} meter`;
                }

                if (acc <= TARGET_ACCURACY) {
                    navigator.geolocation.clearWatch(watchId);
                    statusEl.textContent = `Lokasi terkunci! Akurasi: ${Math.round(acc)} meter`;
                    refreshBtn.classList.remove('hidden');
                    getBtn.classList.add('hidden');
                    document.getElementById('submitBtn').disabled = false;
                    resolve(bestResult);
                }

                if (Date.now() - startTime >= MAX_WAIT) {
                    navigator.geolocation.clearWatch(watchId);
                    if (bestResult) {
                        statusEl.textContent = `Timeout. Menggunakan akurasi terbaik: ${Math.round(bestResult.coords.accuracy)} meter`;
                        refreshBtn.classList.remove('hidden');
                        getBtn.classList.add('hidden');
                        document.getElementById('submitBtn').disabled = false;
                        resolve(bestResult);
                    } else {
                        statusEl.textContent = 'Gagal mendapatkan lokasi';
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

function updateLocationDisplay(position) {
    document.getElementById('displayLat').value = position.coords.latitude.toFixed(8);
    document.getElementById('displayLng').value = position.coords.longitude.toFixed(8);
    document.getElementById('displayAccuracy').value = Math.round(position.coords.accuracy) + 'm';
    document.getElementById('latitude').value = position.coords.latitude;
    document.getElementById('longitude').value = position.coords.longitude;
    document.getElementById('accuracy').value = position.coords.accuracy;
    document.getElementById('locationInfo').classList.remove('hidden');
}

document.getElementById('getLocationBtn').addEventListener('click', getAccuratePosition);
document.getElementById('refreshLocationBtn').addEventListener('click', getAccuratePosition);
</script>
@endpush
@endsection
