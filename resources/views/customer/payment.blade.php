@extends('layouts.app')

@section('title', 'Pembayaran')

@section('content')
<div class="max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Pembayaran</h2>

    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="text-center mb-6">
            <p class="text-gray-600 mb-2">Nomor Pesanan</p>
            <p class="text-xl font-bold">{{ $pesanan->payment_reference }}</p>
        </div>

        <div class="border-t border-b py-4 mb-6">
            <div class="flex justify-between mb-2">
                <span class="text-gray-600">Total Pembayaran</span>
                <span class="font-bold text-xl text-orange-500">{{ formatRupiah($pesanan->total) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Metode Pembayaran</span>
                <span class="font-medium">Midtrans (QRIS, VA, E-Wallet)</span>
            </div>
        </div>

        @if($pesanan->status_bayar === 'pending')
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                <div class="flex items-center gap-3">
                    <i class="ph ph-info text-2xl text-blue-500"></i>
                    <div class="text-blue-700">
                        <p class="font-medium">Menunggu Pembayaran</p>
                        <p class="text-sm">Pesanan Anda sedang diproses. Silakan selesaikan pembayaran jika halaman Midtrans terbuka.</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="text-center">
            <p class="text-gray-600 mb-4">Status Pembayaran</p>
            <span class="inline-block px-4 py-2 rounded-full text-sm font-medium
                @if($pesanan->status_bayar === 'pending') bg-yellow-100 text-yellow-700
                @elseif($pesanan->status_bayar === 'lunas') bg-green-100 text-green-700
                @else bg-red-100 text-red-700 @endif">
                @if($pesanan->status_bayar === 'pending') Menunggu Pembayaran
                @elseif($pesanan->status_bayar === 'lunas') Lunas
                @else Expired @endif
            </span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold mb-4">Rincian Pesanan</h3>
        <div class="space-y-2 mb-4">
            @foreach($pesanan->detailPesanan as $detail)
                <div class="flex justify-between text-sm">
                    <span>{{ $detail->jumlah }}x {{ $detail->menu->nama_menu }}</span>
                    <span>{{ formatRupiah($detail->subtotal) }}</span>
                </div>
            @endforeach
        </div>
        <div class="border-t pt-2 flex justify-between font-bold">
            <span>Total</span>
            <span>{{ formatRupiah($pesanan->total) }}</span>
        </div>
    </div>

    <div class="mt-6">
        @if($pesanan->status_bayar === 'lunas')
            <a href="{{ route('customer.order-success', $pesanan->idpesanan) }}" class="block w-full text-center py-3 px-4 bg-green-500 hover:bg-green-600 text-white rounded-xl transition-colors">
                <i class="ph ph-check-circle mr-2"></i> Lihat Detail Pesanan
            </a>
        @else
            <div class="flex gap-3">
                <a href="{{ route('customer.index') }}" class="flex-1 text-center py-3 px-4 border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                    <i class="ph ph-house mr-2"></i> Kembali ke Beranda
                </a>
                <button onclick="checkStatus()" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white py-3 px-4 rounded-xl transition-colors">
                    <i class="ph ph-arrows-clockwise mr-2"></i> Cek Status
                </button>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function checkStatus() {
        window.location.reload();
    }
</script>
@endpush
@endsection
