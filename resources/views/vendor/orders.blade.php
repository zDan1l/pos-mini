@extends('layouts.vendor')

@section('title', 'Pesanan')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Daftar Pesanan Lunas</h1>
    <p class="text-gray-600">Pesanan yang sudah dibayar untuk {{ $vendor->nama_vendor }}</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">No. Pesanan</th>
                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">QR Code</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Customer</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Metode</th>
                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">Total</th>
                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @if($orders->count() === 0)
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <i class="ph ph-shopping-cart text-4xl mb-2 block"></i>
                        <p class="font-medium">Belum ada pesanan lunas</p>
                    </td>
                </tr>
            @else
                @foreach($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <span class="font-mono text-sm">{{ $order->payment_reference }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button onclick="showQRCode('{{ $order->payment_reference }}', '{{ $order->payment_reference }}')"
                                    class="text-orange-500 hover:text-orange-600 p-2 rounded-lg hover:bg-orange-50 transition-colors"
                                    title="Lihat QR Code">
                                <i class="ph ph-qr-code text-2xl"></i>
                            </button>
                        </td>
                        <td class="px-6 py-4">{{ $order->user ? $order->user->name : 'Guest' }}</td>
                        <td class="px-6 py-4 text-gray-600 text-sm">{{ $order->timestamp->format('d M Y, H:i') }}</td>
                        <td class="px-6 py-4">
                            @php
                                $paymentBadgeClass = match($order->metode_bayar) {
                                    'qris' => 'bg-red-100 text-red-700',
                                    'virtual_account' => 'bg-blue-100 text-blue-700',
                                    'ewallet' => 'bg-green-100 text-green-700',
                                    default => 'bg-gray-100 text-gray-700'
                                };
                                $paymentLabel = match($order->metode_bayar) {
                                    'qris' => 'QRIS',
                                    'virtual_account' => 'Virtual Account',
                                    'ewallet' => 'E-Wallet',
                                    default => 'Midtrans'
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $paymentBadgeClass }}">
                                {{ $paymentLabel }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right font-semibold text-orange-600">{{ formatRupiah($order->total) }}</td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('vendor.order-detail', $order->idpesanan) }}" class="inline-flex items-center gap-1 text-orange-500 hover:text-orange-600">
                                <i class="ph ph-eye"></i>
                                <span>Detail</span>
                            </a>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

<!-- Modal QR Code -->
<div id="qrcodeModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-sm w-full mx-4 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">QR Code Pesanan</h3>
            <button onclick="closeQRCode()" class="text-gray-500 hover:text-gray-700">
                <i class="ph ph-x text-xl"></i>
            </button>
        </div>
        <div class="text-center">
            <p id="qrcodeOrderId" class="text-sm text-gray-600 mb-4"></p>
            <div class="bg-gray-50 p-4 rounded-lg inline-block">
                <img id="qrcodeImage" src="" alt="QR Code" class="w-48 h-48 mx-auto">
            </div>
            <p class="text-xs text-gray-500 mt-4">Scan untuk melihat nomor pesanan</p>
        </div>
        <div class="mt-6 flex gap-3">
            <a id="qrcodeDownloadLink" download="qrcode.png" class="flex-1 bg-orange-500 text-white py-2 px-4 rounded-lg hover:bg-orange-600 transition-colors text-center font-medium">
                <i class="ph ph-download mr-2"></i> Download
            </a>
            <button onclick="closeQRCode()" class="flex-1 bg-gray-200 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                Tutup
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showQRCode(content, orderId) {
        const qrcodeImage = document.getElementById('qrcodeImage');
        const qrcodeOrderId = document.getElementById('qrcodeOrderId');
        const qrcodeDownloadLink = document.getElementById('qrcodeDownloadLink');

        qrcodeImage.src = '/qrcode/' + encodeURIComponent(content);
        qrcodeOrderId.textContent = orderId;
        qrcodeDownloadLink.href = qrcodeImage.src;

        document.getElementById('qrcodeModal').classList.remove('hidden');
    }

    function closeQRCode() {
        document.getElementById('qrcodeModal').classList.add('hidden');
    }

    // Close modal on backdrop click
    document.getElementById('qrcodeModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeQRCode();
        }
    });
</script>
@endpush
@endsection
