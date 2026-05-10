@extends('layouts.vendor')

@section('title', 'Kunjungan Toko')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Kunjungan Toko</h2>

        {{-- Step 1: Scanner --}}
        <div id="scanStep">
            <div class="text-center mb-4">
                <p class="text-gray-600">Scan barcode toko untuk memulai kunjungan</p>
            </div>

            <div id="scanner-container" class="relative mb-4">
                <div id="reader" class="w-full rounded-lg overflow-hidden" style="min-height: 400px;"></div>
                <div id="scanner-overlay" class="hidden absolute inset-0 bg-black bg-opacity-75 flex items-center justify-center">
                    <div class="text-white text-center">
                        <i class="ph ph-spinner animate-spin text-4xl mb-2"></i>
                        <p class="font-semibold">Mencari toko...</p>
                        <p class="text-sm text-gray-300 mt-1">Mohon tunggu sebentar</p>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button id="start-scan-btn" class="flex-1 bg-orange-500 text-white py-3 px-6 rounded-lg hover:bg-orange-600 transition-colors font-semibold">
                    <i class="ph ph-camera"></i> Mulai Scan
                </button>
                <button id="stop-scan-btn" class="hidden flex-1 bg-red-500 text-white py-3 px-6 rounded-lg hover:bg-red-600 transition-colors font-semibold">
                    <i class="ph ph-stop"></i> Berhenti
                </button>
            </div>

            <div id="error-container" class="hidden mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <div id="error-message" class="flex items-center gap-2"></div>
            </div>
        </div>

        {{-- Step 2: Store Info & Get Location --}}
        <div id="locationStep" class="hidden">
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <h3 class="font-semibold text-lg mb-2">{{ $storeInfo['nama_toko'] ?? '' }}</h3>
                <p class="text-sm text-gray-600 mb-1">{{ $storeInfo['alamat'] ?? '' }}</p>
                <p class="text-xs text-gray-500">Barcode: {{ $storeInfo['barcode'] ?? '' }}</p>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <h3 class="font-medium text-blue-800 mb-2">Ambil Lokasi Anda</h3>
                <p class="text-sm text-blue-600 mb-3">Pastikan Anda berada di lokasi toko. Ambil lokasi dengan akurasi terbaik.</p>

                <div id="locationStatus" class="text-sm text-gray-600 mb-3"></div>

                {{-- Location Loading Indicator --}}
                <div id="locationLoading" class="hidden mb-3 bg-white rounded-lg p-3 border border-blue-200">
                    <div class="flex items-center gap-3">
                        <i class="ph ph-spinner animate-spin text-xl text-blue-500"></i>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-blue-700">Mengambil lokasi dengan akurasi terbaik...</p>
                            <p class="text-xs text-blue-600" id="locationTimer">Mohon tunggu sebentar</p>
                        </div>
                    </div>
                    {{-- Progress bar --}}
                    <div class="mt-2 h-2 bg-blue-100 rounded-full overflow-hidden">
                        <div id="locationProgress" class="h-full bg-blue-500 transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="button" id="getLocationBtn" class="flex-1 bg-blue-500 text-white py-3 px-6 rounded-lg hover:bg-blue-600 transition-colors font-semibold">
                        <i class="ph ph-map-pin"></i> Ambil Lokasi Saya
                    </button>
                    <button type="button" id="cancelBtn" class="bg-gray-200 text-gray-700 py-3 px-6 rounded-lg hover:bg-gray-300 transition-colors">
                        <i class="ph ph-x"></i>
                    </button>
                </div>

                <div id="locationInfo" class="hidden mt-4 grid grid-cols-3 gap-4">
                    <div class="bg-white p-3 rounded border">
                        <p class="text-xs text-gray-500">Latitude</p>
                        <p class="font-semibold" id="displayLat">-</p>
                    </div>
                    <div class="bg-white p-3 rounded border">
                        <p class="text-xs text-gray-500">Longitude</p>
                        <p class="font-semibold" id="displayLng">-</p>
                    </div>
                    <div class="bg-white p-3 rounded border">
                        <p class="text-xs text-gray-500">Accuracy</p>
                        <p class="font-semibold" id="displayAccuracy">-</p>
                    </div>
                </div>

                <button type="button" id="submitVisitBtn" class="hidden w-full mt-4 bg-green-500 text-white py-3 px-6 rounded-lg hover:bg-green-600 transition-colors font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="ph ph-check-circle"></i> Konfirmasi Kunjungan
                </button>

                {{-- Loading indicator --}}
                <div id="submitLoading" class="hidden w-full mt-4 bg-gray-100 text-gray-600 py-3 px-6 rounded-lg flex items-center justify-center gap-2">
                    <i class="ph ph-spinner animate-spin text-lg"></i>
                    <span>Memproses kunjungan...</span>
                </div>
            </div>

            {{-- Store location info --}}
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h4 class="font-medium text-gray-700 mb-2">Lokasi Toko</h4>
                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Lat:</span>
                        <span class="ml-1">{{ $storeInfo['latitude'] ?? '' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Lng:</span>
                        <span class="ml-1">{{ $storeInfo['longitude'] ?? '' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Acc:</span>
                        <span class="ml-1">{{ number_format($storeInfo['accuracy'] ?? 0, 0) }}m</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 3: Result --}}
        <div id="resultStep" class="hidden">
            <div id="resultContent" class="text-center">
                {{-- Result will be inserted here --}}
            </div>

            <div class="mt-6 flex gap-3">
                <button id="visitAgainBtn" class="flex-1 bg-orange-500 text-white py-3 px-6 rounded-lg hover:bg-orange-600 transition-colors font-semibold">
                    <i class="ph ph-qr-code"></i> Kunjungi Toko Lain
                </button>
                <a href="{{ route('vendor.store-visit.history') }}" class="flex-1 bg-gray-200 text-gray-700 py-3 px-6 rounded-lg hover:bg-gray-300 transition-colors font-semibold text-center">
                    <i class="ph ph-clock-counter-clockwise"></i> Lihat Riwayat
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="{{ asset('assets/audio/scan-beep.js') }}"></script>
<script>
let html5QrcodeScanner = null;
let isScanning = false;
let currentStore = null;
const MAX_WAIT = 20000;
const TARGET_ACCURACY = 50;
const THRESHOLD = 100;

const storeInfo = @json($storeInfo ?? []);
let scanSound = new ScanSound();

// Beep sound - menggunakan audio file
function playBeep() {
    scanSound.playBeep();
}

// Scanner functions
function startScanner() {
    // Hide error when starting scanner manually
    document.getElementById('error-container').classList.add('hidden');

    html5QrcodeScanner = new Html5Qrcode("reader");

    const config = {
        fps: 10,
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.0
    };

    html5QrcodeScanner.start(
        { facingMode: "environment" },
        config,
        onScanSuccess,
        () => {}
    ).then(() => {
        isScanning = true;
        document.getElementById('start-scan-btn').classList.add('hidden');
        document.getElementById('stop-scan-btn').classList.remove('hidden');
    }).catch((err) => {
        showError('Gagal mengakses kamera: ' + err);
    });
}

function stopScanner() {
    if (html5QrcodeScanner && isScanning) {
        html5QrcodeScanner.stop().then(() => {
            isScanning = false;
            document.getElementById('start-scan-btn').classList.remove('hidden');
            document.getElementById('stop-scan-btn').classList.add('hidden');
        }).catch((err) => {
            console.error('Failed to stop scanner', err);
        });
    }
}

function onScanSuccess(decodedText) {
    playBeep();
    // Hide error when new scan is successful
    document.getElementById('error-container').classList.add('hidden');
    stopScanner();
    lookupStore(decodedText);
}

async function lookupStore(barcode) {
    const overlay = document.getElementById('scanner-overlay');
    const overlayText = overlay.querySelector('p');
    const overlayTitle = overlay.querySelector('.font-semibold');

    overlayTitle.textContent = 'Mencari toko...';
    overlayText.textContent = 'Barcode: ' + barcode;
    overlay.classList.remove('hidden');

    // Add timeout for fetch
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 15000);

    try {
        const response = await fetch(`{{ route('vendor.store-visit.lookup', ':barcode') }}`.replace(':barcode', barcode), {
            signal: controller.signal
        });
        clearTimeout(timeoutId);

        const data = await response.json();
        overlay.classList.add('hidden');

        if (data.success) {
            currentStore = data.data;
            showLocationStep();
        } else {
            // Play error sound for invalid barcode
            scanSound.playError();
            showError(data.message || 'Toko tidak ditemukan', 'invalid_barcode');
            startScanner();
        }
    } catch (error) {
        clearTimeout(timeoutId);
        overlay.classList.add('hidden');

        if (error.name === 'AbortError') {
            scanSound.playError();
            showError('Timeout - Server terlalu lama merespons. Silakan coba lagi.', 'timeout');
        } else {
            console.error('Lookup error:', error);
            scanSound.playError();
            showError('Gagal memproses scan. Periksa koneksi internet Anda.', 'network');
        }
        startScanner();
    }
}

function showLocationStep() {
    document.getElementById('scanStep').classList.add('hidden');
    document.getElementById('locationStep').classList.remove('hidden');

    // Update store info
    document.querySelector('#locationStep .font-semibold').textContent = currentStore.nama_toko;
    document.querySelector('#locationStep .text-gray-600').textContent = currentStore.alamat || '-';
    document.querySelector('#locationStep .text-gray-500').textContent = 'Barcode: ' + currentStore.barcode;
}

function getAccuratePosition() {
    return new Promise((resolve, reject) => {
        let bestResult = null;
        const startTime = Date.now();

        const statusEl = document.getElementById('locationStatus');
        const getBtn = document.getElementById('getLocationBtn');
        const loadingDiv = document.getElementById('locationLoading');
        const progressEl = document.getElementById('locationProgress');
        const timerEl = document.getElementById('locationTimer');

        // Show loading indicator
        statusEl.textContent = 'Memulai pengambilan lokasi...';
        loadingDiv.classList.remove('hidden');
        getBtn.disabled = true;

        // Progress bar update interval
        const progressInterval = setInterval(() => {
            const elapsed = Date.now() - startTime;
            const progress = Math.min((elapsed / MAX_WAIT) * 100, 100);
            progressEl.style.width = progress + '%';

            const remaining = Math.max(0, Math.ceil((MAX_WAIT - elapsed) / 1000));
            timerEl.textContent = `Estimasi: ${remaining} detik lagi`;
        }, 100);

        const watchId = navigator.geolocation.watchPosition(
            (position) => {
                const acc = position.coords.accuracy;

                if (!bestResult || acc < bestResult.coords.accuracy) {
                    bestResult = position;
                    updateLocationDisplay(bestResult);
                    statusEl.textContent = `Akurasi: ${Math.round(acc)} meter (target: ≤${TARGET_ACCURACY}m)`;

                    // Update color based on accuracy
                    if (acc <= TARGET_ACCURACY) {
                        statusEl.className = 'text-sm text-green-600 font-medium mb-3';
                    } else if (acc <= TARGET_ACCURACY * 2) {
                        statusEl.className = 'text-sm text-yellow-600 font-medium mb-3';
                    } else {
                        statusEl.className = 'text-sm text-orange-600 font-medium mb-3';
                    }
                }

                if (acc <= TARGET_ACCURACY) {
                    clearInterval(progressInterval);
                    navigator.geolocation.clearWatch(watchId);
                    loadingDiv.classList.add('hidden');
                    statusEl.textContent = `✓ Lokasi terkunci! Akurasi: ${Math.round(acc)} meter`;
                    statusEl.className = 'text-sm text-green-600 font-semibold mb-3';
                    getBtn.classList.add('hidden');
                    document.getElementById('submitVisitBtn').classList.remove('hidden');
                    resolve(bestResult);
                }

                if (Date.now() - startTime >= MAX_WAIT) {
                    clearInterval(progressInterval);
                    navigator.geolocation.clearWatch(watchId);
                    loadingDiv.classList.add('hidden');

                    if (bestResult) {
                        statusEl.textContent = `✓ Selesai! Akurasi terbaik: ${Math.round(bestResult.coords.accuracy)} meter`;
                        statusEl.className = 'text-sm text-blue-600 font-semibold mb-3';
                        getBtn.classList.add('hidden');
                        document.getElementById('submitVisitBtn').classList.remove('hidden');
                        resolve(bestResult);
                    } else {
                        statusEl.textContent = '✗ Gagal mendapatkan lokasi. Silakan coba lagi.';
                        statusEl.className = 'text-sm text-red-600 font-medium mb-3';
                        getBtn.disabled = false;
                        reject(new Error('Timeout'));
                    }
                }
            },
            (error) => {
                clearInterval(progressInterval);
                navigator.geolocation.clearWatch(watchId);
                loadingDiv.classList.add('hidden');

                let errorMsg = 'Gagal mengambil lokasi';
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        errorMsg = 'Akses lokasi ditolak. Mohon izinkan akses lokasi.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMsg = 'Lokasi tidak tersedia. Periksa GPS/Location Anda.';
                        break;
                    case error.TIMEOUT:
                        errorMsg = 'Timeout mengambil lokasi. Silakan coba lagi.';
                        break;
                }

                statusEl.textContent = '✗ ' + errorMsg;
                statusEl.className = 'text-sm text-red-600 font-medium mb-3';
                getBtn.disabled = false;
                reject(error);
            },
            { enableHighAccuracy: true, maximumAge: 0, timeout: MAX_WAIT }
        );
    });
}

function updateLocationDisplay(position) {
    document.getElementById('displayLat').textContent = position.coords.latitude.toFixed(8);
    document.getElementById('displayLng').textContent = position.coords.longitude.toFixed(8);
    document.getElementById('displayAccuracy').textContent = Math.round(position.coords.accuracy) + 'm';
    document.getElementById('locationInfo').classList.remove('hidden');

    window.currentPosition = position;
}

async function submitVisit() {
    if (!currentStore || !window.currentPosition) return;

    const pos = window.currentPosition.coords;
    const submitBtn = document.getElementById('submitVisitBtn');
    const loadingDiv = document.getElementById('submitLoading');

    // Show loading
    submitBtn.classList.add('hidden');
    loadingDiv.classList.remove('hidden');

    // Add timeout for fetch
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 15000);

    try {
        const response = await fetch('{{ route('vendor.store-visit.process') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            signal: controller.signal,
            body: JSON.stringify({
                barcode: currentStore.barcode,
                latitude: pos.latitude,
                longitude: pos.longitude,
                accuracy: pos.accuracy
            })
        });
        clearTimeout(timeoutId);

        const data = await response.json();

        if (data.success) {
            showResult(data.data);
        } else {
            // Hide loading and show button again
            loadingDiv.classList.add('hidden');
            submitBtn.classList.remove('hidden');
            showError(data.message || 'Gagal memproses kunjungan');
        }
    } catch (error) {
        clearTimeout(timeoutId);
        loadingDiv.classList.add('hidden');
        submitBtn.classList.remove('hidden');

        if (error.name === 'AbortError') {
            showError('Timeout - Server terlalu lama merespons. Silakan coba lagi.');
        } else {
            showError('Gagal mengirim data. Periksa koneksi internet Anda.');
        }
        console.error(error);
    }
}

function showResult(data) {
    document.getElementById('locationStep').classList.add('hidden');
    document.getElementById('resultStep').classList.remove('hidden');

    const isAccepted = data.status === 'diterima';

    // Play sound based on result
    if (isAccepted) {
        scanSound.playSuccess();
    } else {
        scanSound.playError();
    }
    const bgColor = isAccepted ? 'bg-green-50' : 'bg-red-50';
    const borderColor = isAccepted ? 'border-green-200' : 'border-red-200';
    const textColor = isAccepted ? 'text-green-700' : 'text-red-700';
    const icon = isAccepted ? 'ph-check-circle' : 'ph-x-circle';

    document.getElementById('resultContent').innerHTML = `
        <div class="${bgColor} border ${borderColor} rounded-lg p-6 mb-4">
            <i class="ph ${icon} text-6xl ${textColor} mb-4"></i>
            <h3 class="text-2xl font-bold ${textColor} mb-2">
                ${isAccepted ? 'Kunjungan Diterima!' : 'Kunjungan Ditolak'}
            </h3>
            <p class="text-gray-600 mb-4">${data.nama_toko}</p>
        </div>

        <div class="bg-gray-50 rounded-lg p-4 text-left">
            <h4 class="font-medium text-gray-700 mb-3">Detail Kunjungan</h4>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Jarak dari toko:</span>
                    <span class="font-semibold">${data.distance} meter</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Threshold:</span>
                    <span class="font-semibold">${data.threshold} meter</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Threshold efektif:</span>
                    <span class="font-semibold">${data.threshold_effective} meter</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Akurasi toko:</span>
                    <span class="font-semibold">${data.store_accuracy} meter</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Akurasi lokasi Anda:</span>
                    <span class="font-semibold">${data.visit_accuracy} meter</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Waktu:</span>
                    <span class="font-semibold">${data.visited_at}</span>
                </div>
            </div>
        </div>
    `;
}

function showError(message, type = 'error') {
    const errorContainer = document.getElementById('error-container');
    const errorMessage = document.getElementById('error-message');

    let icon = '';
    let bgClass = '';
    let borderClass = '';
    let textClass = '';

    switch (type) {
        case 'invalid_barcode':
            icon = '<i class="ph ph-barcode text-xl"></i>';
            bgClass = 'bg-red-50';
            borderClass = 'border-red-200';
            textClass = 'text-red-700';
            break;
        case 'timeout':
            icon = '<i class="ph ph-clock text-xl"></i>';
            bgClass = 'bg-yellow-50';
            borderClass = 'border-yellow-200';
            textClass = 'text-yellow-700';
            break;
        case 'network':
            icon = '<i class="ph ph-wifi-slash text-xl"></i>';
            bgClass = 'bg-orange-50';
            borderClass = 'border-orange-200';
            textClass = 'text-orange-700';
            break;
        default:
            icon = '<i class="ph ph-warning-circle text-xl"></i>';
            bgClass = 'bg-red-50';
            borderClass = 'border-red-200';
            textClass = 'text-red-700';
    }

    errorContainer.className = `mt-4 ${bgClass} border ${borderClass} ${textClass} px-4 py-3 rounded-lg flex items-center gap-2`;
    errorMessage.innerHTML = `${icon} <span>${message}</span>`;
    errorContainer.classList.remove('hidden');
}

function resetToScan() {
    document.getElementById('scanStep').classList.remove('hidden');
    document.getElementById('locationStep').classList.add('hidden');
    document.getElementById('resultStep').classList.add('hidden');
    document.getElementById('locationInfo').classList.add('hidden');
    document.getElementById('submitVisitBtn').classList.add('hidden');
    document.getElementById('submitLoading').classList.add('hidden');
    document.getElementById('locationLoading').classList.add('hidden');
    document.getElementById('getLocationBtn').classList.remove('hidden');
    document.getElementById('getLocationBtn').disabled = false;
    document.getElementById('locationStatus').textContent = '';
    document.getElementById('locationStatus').className = 'text-sm text-gray-600 mb-3';
    document.getElementById('locationProgress').style.width = '0%';
    currentStore = null;
    window.currentPosition = null;
    startScanner();
}

// Event listeners
document.getElementById('start-scan-btn').addEventListener('click', startScanner);
document.getElementById('stop-scan-btn').addEventListener('click', stopScanner);
document.getElementById('getLocationBtn').addEventListener('click', getAccuratePosition);
document.getElementById('submitVisitBtn').addEventListener('click', submitVisit);
document.getElementById('cancelBtn').addEventListener('click', resetToScan);
document.getElementById('visitAgainBtn').addEventListener('click', resetToScan);

// Auto-start scanner
startScanner();
</script>
@endpush
@endsection
