@extends('layouts.vendor')

@section('title', 'QR Scanner')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Scan QR Code Pesanan</h2>

            {{-- Scanner Container --}}
            <div id="scanner-container" class="relative">
                <div id="reader" class="w-full rounded-lg overflow-hidden" style="min-height: 400px;"></div>
                <div id="scanner-overlay" class="hidden absolute inset-0 bg-black bg-opacity-75 flex items-center justify-center">
                    <div class="text-white text-center">
                        <i class="ph ph-spinner animate-spin text-4xl mb-2"></i>
                        <p>Memproses...</p>
                    </div>
                </div>
            </div>

            {{-- Scanner Controls --}}
            <div class="mt-4 flex gap-3">
                <button id="start-scan-btn" class="flex-1 bg-orange-500 text-white py-3 px-6 rounded-lg hover:bg-orange-600 transition-colors font-semibold">
                    <i class="ph ph-camera"></i> Mulai Scan
                </button>
                <button id="stop-scan-btn" class="hidden flex-1 bg-red-500 text-white py-3 px-6 rounded-lg hover:bg-red-600 transition-colors font-semibold">
                    <i class="ph ph-stop"></i> Berhenti
                </button>
            </div>

            {{-- Result Display --}}
            <div id="result-container" class="hidden mt-6">
                <div class="border-t pt-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Detail Pesanan</h3>

                    <div class="space-y-4">
                        {{-- Order Info --}}
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <span class="font-semibold text-lg">{{ $vendor->nama_vendor }}</span>
                                @if(isset($result) && $result['status_bayar'] === 'lunas')
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium">
                                        <i class="ph ph-check-circle"></i> Lunas
                                    </span>
                                @else
                                    <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm font-medium">
                                        <i class="ph ph-clock"></i> Pending
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600">No. Pesanan: <span class="font-semibold" id="order-ref"></span></p>
                            <p class="text-sm text-gray-600">Pelanggan: <span id="customer-name"></span></p>
                            <p class="text-sm text-gray-600">Waktu: <span id="order-time"></span></p>
                            <p class="text-sm text-gray-600">Metode: <span id="payment-method"></span></p>
                        </div>

                        {{-- Items --}}
                        <div class="border rounded-lg p-4">
                            <h4 class="font-semibold mb-3">Item Pesanan:</h4>
                            <div id="order-items" class="space-y-2">
                                {{-- Items will be inserted here --}}
                            </div>
                            <div class="flex justify-between font-bold text-lg border-t mt-3 pt-3">
                                <span>Total</span>
                                <span class="text-orange-500" id="order-total"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="mt-4 flex gap-3">
                        <button id="scan-another-btn" class="flex-1 bg-orange-500 text-white py-3 px-6 rounded-lg hover:bg-orange-600 transition-colors font-semibold">
                            <i class="ph ph-qr-code"></i> Scan Lagi
                        </button>
                        <a href="{{ route('vendor.orders') }}" class="flex-1 bg-gray-200 text-gray-700 py-3 px-6 rounded-lg hover:bg-gray-300 transition-colors font-semibold text-center">
                            <i class="ph ph-list-dashes"></i> Lihat Semua Pesanan
                        </a>
                    </div>
                </div>
            </div>

            {{-- Error Message --}}
            <div id="error-container" class="hidden mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <p class="flex items-center gap-2">
                    <i class="ph ph-warning-circle text-xl"></i>
                    <span id="error-message"></span>
                </p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
    let html5QrcodeScanner = null;
    let isScanning = false;

    // Beep sound
    function playBeep() {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();

        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);

        oscillator.frequency.value = 1500; // even higher pitch for maximum audibility
        oscillator.type = 'sine';

        // Maximum volume (1.0 is the max before distortion)
        gainNode.gain.setValueAtTime(1.0, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);

        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.2); // longer duration for better audibility
    }

    // Start scanner
    function startScanner() {
        const startBtn = document.getElementById('start-scan-btn');
        const stopBtn = document.getElementById('stop-scan-btn');
        const resultContainer = document.getElementById('result-container');
        const errorContainer = document.getElementById('error-container');

        resultContainer.classList.add('hidden');
        errorContainer.classList.add('hidden');

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
            onScanFailure
        ).then(() => {
            isScanning = true;
            startBtn.classList.add('hidden');
            stopBtn.classList.remove('hidden');
        }).catch((err) => {
            showError('Gagal mengakses kamera: ' + err);
        });
    }

    // Stop scanner
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

    // On scan success
    function onScanSuccess(decodedText, decodedResult) {
        // Play beep sound
        playBeep();

        // Stop scanner
        stopScanner();

        // Look up order
        lookupOrder(decodedText);
    }

    // On scan failure (called repeatedly when no QR code is detected)
    function onScanFailure(error) {
        // Suppress console errors for continuous scanning
    }

    // Look up order
    async function lookupOrder(orderRef) {
        const overlay = document.getElementById('scanner-overlay');
        const resultContainer = document.getElementById('result-container');
        const errorContainer = document.getElementById('error-container');

        overlay.classList.remove('hidden');
        resultContainer.classList.add('hidden');
        errorContainer.classList.add('hidden');

        try {
            const response = await fetch(`{{ route('vendor.scanner-lookup', ':orderRef') }}`.replace(':orderRef', orderRef));
            const data = await response.json();

            overlay.classList.add('hidden');

            if (data.success) {
                displayOrderResult(data.data);
            } else {
                showError(data.message || 'Pesanan tidak ditemukan');
            }
        } catch (error) {
            overlay.classList.add('hidden');
            showError('Gagal memproses scan. Silakan coba lagi.');
            console.error('Lookup error:', error);
        }
    }

    // Display order result
    function displayOrderResult(order) {
        const resultContainer = document.getElementById('result-container');

        document.getElementById('order-ref').textContent = order.payment_reference;
        document.getElementById('customer-name').textContent = order.customer_name;
        document.getElementById('order-time').textContent = order.timestamp;
        document.getElementById('payment-method').textContent = order.metode_bayar.toUpperCase();

        // Display items
        const itemsContainer = document.getElementById('order-items');
        itemsContainer.innerHTML = '';

        order.items.forEach(item => {
            const itemDiv = document.createElement('div');
            itemDiv.className = 'flex justify-between py-2 border-b last:border-b-0';
            itemDiv.innerHTML = `
                <span>${item.jumlah}x ${item.nama_menu}</span>
                <span>${formatRupiah(item.subtotal)}</span>
            `;
            itemsContainer.appendChild(itemDiv);
        });

        document.getElementById('order-total').textContent = formatRupiah(order.total);

        resultContainer.classList.remove('hidden');
    }

    // Show error
    function showError(message) {
        const errorContainer = document.getElementById('error-container');
        document.getElementById('error-message').textContent = message;
        errorContainer.classList.remove('hidden');
    }

    // Event listeners
    document.getElementById('start-scan-btn').addEventListener('click', startScanner);
    document.getElementById('stop-scan-btn').addEventListener('click', stopScanner);
    document.getElementById('scan-another-btn').addEventListener('click', () => {
        document.getElementById('result-container').classList.add('hidden');
        startScanner();
    });
    </script>
@endpush
@endsection
