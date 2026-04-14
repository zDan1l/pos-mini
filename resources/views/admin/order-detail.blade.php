@extends('layouts.admin')

@section('title', 'Detail Pesanan')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.orders') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-orange-500">
        <i class="ph ph-arrow-left"></i>
        <span>Kembali ke Daftar Pesanan</span>
    </a>
    <h1 class="text-2xl font-bold text-gray-900 mt-4">Detail Pesanan</h1>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <!-- Order Info -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-gray-900">Informasi Pesanan</h2>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($pesanan->status_bayar === 'lunas') bg-green-100 text-green-700
                    @elseif($pesanan->status_bayar === 'pending') bg-yellow-100 text-yellow-700
                    @else bg-red-100 text-red-700 @endif">
                    {{ ucfirst($pesanan->status_bayar) }}
                </span>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500 mb-1">No. Pesanan</p>
                    <p class="font-mono font-semibold">{{ $pesanan->payment_reference }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Waktu Pesanan</p>
                    <p class="font-semibold">{{ $pesanan->timestamp->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Customer</p>
                    <p class="font-semibold">{{ $pesanan->user ? $pesanan->user->name : 'Guest' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Vendor</p>
                    <p class="font-semibold">{{ $pesanan->vendor->nama_vendor }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Metode Pembayaran</p>
                    @php
                        $paymentLabel = match($pesanan->metode_bayar) {
                            'qris' => 'QRIS',
                            'virtual_account' => 'Virtual Account',
                            'ewallet' => 'E-Wallet',
                            default => 'Midtrans'
                        };
                    @endphp
                    <p class="font-semibold">{{ $paymentLabel }}</p>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Item Pesanan</h2>
            <div class="space-y-4">
                @foreach($pesanan->detailPesanan as $detail)
                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl">
                        @if($detail->menu->path_gambar)
                            <img src="/storage/{{ $detail->menu->path_gambar }}" alt="{{ $detail->menu->nama_menu }}" class="w-16 h-16 object-cover rounded-lg">
                        @else
                            <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                <i class="ph ph-bowl-food text-2xl text-gray-400"></i>
                            </div>
                        @endif
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">{{ $detail->menu->nama_menu }}</p>
                            <p class="text-sm text-gray-500">{{ $detail->jumlah }} x {{ formatRupiah($detail->harga) }}</p>
                        </div>
                        <p class="font-semibold text-gray-900">{{ formatRupiah($detail->subtotal) }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Total -->
    <div class="lg:col-span-1 space-y-6">
        <!-- QR Code Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 text-center">QR Code Pesanan</h2>
            <div class="text-center">
                <div class="bg-gray-50 p-4 rounded-xl inline-block mb-4">
                    <img src="{{ route('customer.qrcode', $pesanan->payment_reference) }}"
                         alt="QR Code {{ $pesanan->payment_reference }}"
                         class="w-40 h-40 mx-auto">
                </div>
                <p class="text-sm text-gray-600 mb-1">{{ $pesanan->payment_reference }}</p>
                <p class="text-xs text-gray-500">Scan untuk melihat nomor pesanan</p>
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Ringkasan Pembayaran</h2>

            <div class="space-y-3">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal</span>
                    <span>{{ formatRupiah($pesanan->total) }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Biaya Layanan</span>
                    <span>Rp 0</span>
                </div>
                <div class="border-t pt-3 mt-3">
                    <div class="flex justify-between font-bold text-lg">
                        <span>Total</span>
                        <span class="text-orange-600">{{ formatRupiah($pesanan->total) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
