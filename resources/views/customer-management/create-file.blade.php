@extends('layouts.admin')

@section('title', 'Tambah Customer (File)')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Tambah Customer (File)</h1>
        <p class="text-gray-600">Simpan foto customer sebagai file di storage</p>
    </div>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6">
                <form id="customerForm" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama *</label>
                        <input type="text" name="name" id="name" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" name="email" id="email" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                        <input type="password" name="password" id="password" required minlength="6"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto Customer</label>
                        <div class="flex gap-3">
                            <div class="flex-1">
                                <div id="photoPreview" class="w-full aspect-square bg-gray-100 rounded-xl border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden">
                                    <div class="text-center text-gray-400">
                                        <i class="ph ph-camera text-4xl"></i>
                                        <p class="text-sm mt-2">Preview foto</p>
                                    </div>
                                </div>
                                <input type="hidden" name="photo_data" id="photo_data">
                                <input type="hidden" name="photo_mime_type" id="photo_mime_type">
                            </div>
                        </div>
                        <button type="button" id="btnOpenCamera" class="mt-3 w-full bg-orange-500 text-white py-3 px-6 rounded-xl hover:bg-orange-600 transition-colors font-semibold flex items-center justify-center gap-2">
                            <i class="ph ph-camera"></i>
                            Ambil Foto
                        </button>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="submit" id="btnSubmit" class="flex-1 bg-orange-500 text-white py-3 px-6 rounded-xl hover:bg-orange-600 transition-colors font-semibold">
                            <i class="ph ph-floppy-disk mr-2"></i> Simpan Data
                        </button>
                        <a href="{{ route('customer-management.index') }}" class="bg-gray-200 text-gray-700 py-3 px-6 rounded-xl hover:bg-gray-300 transition-colors font-semibold">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Kamera -->
    <div id="cameraModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full mx-4 overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold">Ambil Foto Customer</h3>
                <button type="button" id="btnCloseCamera" class="text-gray-500 hover:text-gray-700">
                    <i class="ph ph-x text-xl"></i>
                </button>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-2 gap-6">
                    <!-- Video Feed -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Live Video</label>
                        <div class="relative aspect-video bg-black rounded-lg overflow-hidden">
                            <video id="videoFeed" autoplay playsinline class="w-full h-full object-cover"></video>
                        </div>
                    </div>

                    <!-- Snapshot Preview -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Preview</label>
                        <div class="aspect-video bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center">
                            <canvas id="photoCanvas" class="hidden"></canvas>
                            <div id="snapshotPlaceholder" class="text-center text-gray-400">
                                <i class="ph ph-image text-4xl"></i>
                                <p class="text-sm mt-2">Hasil foto akan muncul di sini</p>
                            </div>
                            <img id="snapshotPreview" class="w-full h-full object-cover hidden">
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilihan Kamera</label>
                        <select id="cameraSelect" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500">
                            <option value="">Memuat kamera...</option>
                        </select>
                    </div>
                    <button type="button" id="btnCapture" class="bg-orange-500 text-white py-3 px-8 rounded-xl hover:bg-orange-600 transition-colors font-semibold flex items-center gap-2">
                        <i class="ph ph-camera"></i>
                        Ambil Foto
                    </button>
                </div>
            </div>

            <div class="p-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">
                <button type="button" id="btnCancelCamera" class="bg-gray-200 text-gray-700 py-2 px-6 rounded-lg hover:bg-gray-300 transition-colors">
                    Batal
                </button>
                <button type="button" id="btnSavePhoto" class="bg-green-500 text-white py-2 px-6 rounded-lg hover:bg-green-600 transition-colors font-semibold flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    <i class="ph ph-check"></i>
                    Simpan Foto
                </button>
            </div>
        </div>
    </div>
    
@push('scripts')
    <script>
        let stream = null;
        let capturedPhoto = null;
        
        // Elements
        const btnOpenCamera = document.getElementById('btnOpenCamera');
        const btnCloseCamera = document.getElementById('btnCloseCamera');
        const btnCancelCamera = document.getElementById('btnCancelCamera');
        const btnCapture = document.getElementById('btnCapture');
        const btnSavePhoto = document.getElementById('btnSavePhoto');
        const cameraModal = document.getElementById('cameraModal');
        const videoFeed = document.getElementById('videoFeed');
        const photoCanvas = document.getElementById('photoCanvas');
        const snapshotPreview = document.getElementById('snapshotPreview');
        const snapshotPlaceholder = document.getElementById('snapshotPlaceholder');
        const cameraSelect = document.getElementById('cameraSelect');
        const photoPreview = document.getElementById('photoPreview');
        const photoDataInput = document.getElementById('photo_data');
        const photoMimeTypeInput = document.getElementById('photo_mime_type');

        // Open camera modal
        btnOpenCamera.addEventListener('click', async () => {
            cameraModal.classList.remove('hidden');
            await getCameraDevices();
            await startCamera();
        });

        // Close camera modal
        function closeCameraModal() {
            cameraModal.classList.add('hidden');
            stopCamera();
        }

        btnCloseCamera.addEventListener('click', closeCameraModal);
        btnCancelCamera.addEventListener('click', closeCameraModal);

        // Get available cameras
        async function getCameraDevices() {
            try {
                const devices = await navigator.mediaDevices.enumerateDevices();
                const videoDevices = devices.filter(device => device.kind === 'videoinput');

                cameraSelect.innerHTML = '';
                videoDevices.forEach((device, index) => {
                    const option = document.createElement('option');
                    option.value = device.deviceId;
                    option.text = device.label || `Kamera ${index + 1}`;
                    cameraSelect.appendChild(option);
                });

                if (videoDevices.length > 0) {
                    startCamera(videoDevices[0].deviceId);
                }
            } catch (err) {
                console.error('Error getting camera devices:', err);
            }
        }

        // Start camera
        async function startCamera(deviceId = null) {
            stopCamera();

            const constraints = {
                video: {
                    facingMode: 'user',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            };

            if (deviceId) {
                constraints.video.deviceId = { exact: deviceId };
            }

            try {
                stream = await navigator.mediaDevices.getUserMedia(constraints);
                videoFeed.srcObject = stream;
            } catch (err) {
                console.error('Error accessing camera:', err);
                alert('Tidak dapat mengakses kamera. Pastikan Anda telah memberikan izin kamera.');
            }
        }

        // Stop camera
        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
        }

        // Change camera
        cameraSelect.addEventListener('change', () => {
            startCamera(cameraSelect.value);
        });

        // Capture photo
        btnCapture.addEventListener('click', () => {
            const context = photoCanvas.getContext('2d');
            photoCanvas.width = videoFeed.videoWidth;
            photoCanvas.height = videoFeed.videoHeight;
            context.drawImage(videoFeed, 0, 0);

            capturedPhoto = photoCanvas.toDataURL('image/jpeg', 0.8);

            snapshotPreview.src = capturedPhoto;
            snapshotPreview.classList.remove('hidden');
            snapshotPlaceholder.classList.add('hidden');
            btnSavePhoto.disabled = false;
        });

        // Save photo to form
        btnSavePhoto.addEventListener('click', () => {
            if (capturedPhoto) {
                // Update preview in form
                photoPreview.innerHTML = `<img src="${capturedPhoto}" class="w-full h-full object-cover">`;

                // Set form data
                photoDataInput.value = capturedPhoto;
                photoMimeTypeInput.value = 'image/jpeg';

                closeCameraModal();
            }
        });

        // Camera select change
        cameraSelect.addEventListener('change', () => {
            startCamera(cameraSelect.value);
        });

        // Form submission
        document.getElementById('customerForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!photoDataInput.value) {
                alert('Silakan ambil foto terlebih dahulu!');
                return;
            }

            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<i class="ph ph-spinner ph-spin mr-2"></i> Menyimpan...';

            try {
                const response = await fetch('{{ route('customer-management.store-file') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    window.location.href = result.redirect;
                } else {
                    alert(result.message || 'Terjadi kesalahan!');
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="ph ph-floppy-disk mr-2"></i> Simpan Data';
                }
            } catch (err) {
                console.error('Error:', err);
                alert('Terjadi kesalahan!');
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '<i class="ph ph-floppy-disk mr-2"></i> Simpan Data';
            }
        });
    </script>
@endpush
@endsection
