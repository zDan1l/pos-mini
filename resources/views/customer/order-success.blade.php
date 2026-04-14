@extends('layouts.app')

@section('title', 'Pesanan Berhasil')

@section('content')
<div class="max-w-2xl mx-auto text-center">
    <div class="bg-white rounded-lg shadow-md p-8">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>

        <h2 class="text-2xl font-bold text-gray-800 mb-2">Pesanan Berhasil!</h2>
        <p class="text-gray-600 mb-6">Terima kasih telah memesan di Kantin Online</p>

        @if($pesanan->status_bayar === 'lunas')
            <div class="bg-green-50 text-green-700 px-4 py-2 rounded-lg mb-4 inline-flex items-center gap-2">
                <i class="ph ph-check-circle"></i>
                <span>Pembayaran Lunas</span>
            </div>
        @else
            <div class="bg-yellow-50 text-yellow-700 px-4 py-2 rounded-lg mb-4 inline-flex items-center gap-2">
                <i class="ph ph-clock"></i>
                <span>Menunggu konfirmasi pembayaran...</span>
            </div>
        @endif

        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-600 mb-1">Nomor Pesanan</p>
            <p class="text-lg font-bold">{{ $pesanan->payment_reference }}</p>
            <p class="text-sm text-gray-600 mt-2">Nama: {{ $pesanan->user ? $pesanan->user->name : 'Guest' }}</p>
            <p class="text-sm text-gray-600">Vendor: {{ $pesanan->vendor->nama_vendor }}</p>
        </div>

        <!-- QR Code Section -->
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <p class="text-sm text-gray-600 mb-3 text-center">Scan QR Code untuk melihat nomor pesanan</p>
            <div class="flex justify-center">
                <img src="{{ route('customer.qrcode', $pesanan->payment_reference) }}"
                     alt="QR Code Pesanan"
                     class="border-4 border-white rounded-lg shadow-sm w-48 h-48">
            </div>
            <p class="text-xs text-gray-500 mt-3 text-center">Scan untuk membaca: {{ $pesanan->payment_reference }}</p>
        </div>

        <div class="text-left border-t pt-4 mb-6">
            <h3 class="font-semibold mb-3">Detail Pesanan</h3>
            @foreach($pesanan->detailPesanan as $detail)
                <div class="flex justify-between py-2">
                    <span>{{ $detail->jumlah }}x {{ $detail->menu->nama_menu }}</span>
                    <span>{{ formatRupiah($detail->subtotal) }}</span>
                </div>
            @endforeach
            <div class="flex justify-between font-bold text-lg border-t pt-2 mt-2">
                <span>Total</span>
                <span class="text-orange-500">{{ formatRupiah($pesanan->total) }}</span>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('customer.index') }}" class="flex-1 bg-orange-500 text-white py-3 px-6 rounded-lg hover:bg-orange-600 transition-colors font-semibold">
                Pesan Lagi
            </a>
        </div>
    </div>
</div>
@endsection
