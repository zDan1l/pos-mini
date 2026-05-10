@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Pesanan Saya</h2>
            <a href="{{ route('customer.index') }}" class="text-orange-500 hover:text-orange-600 flex items-center gap-1">
                <i class="ph ph-arrow-left"></i> Kembali
            </a>
        </div>

        @if($orders->count() === 0)
            <div class="text-center py-12">
                <i class="ph ph-shopping-cart text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-600">Belum ada pesanan</p>
                <a href="{{ route('customer.index') }}" class="inline-block mt-4 bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600">
                    Mulai Pesan
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($orders as $pesanan)
                    <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="font-semibold text-lg">{{ $pesanan->payment_reference }}</span>
                                    @if($pesanan->status_bayar === 'lunas')
                                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-medium">
                                            Lunas
                                        </span>
                                    @else
                                        <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full text-xs font-medium">
                                            Pending
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600">
                                    <i class="ph ph-storefront"></i> {{ $pesanan->vendor->nama_vendor }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <i class="ph ph-calendar"></i> {{ $pesanan->timestamp->format('d M Y H:i') }}
                                </p>
                                <p class="text-sm font-semibold text-orange-500 mt-2">
                                    {{ formatRupiah($pesanan->total) }}
                                </p>
                            </div>
                            <div class="flex flex-col gap-2">
                                <a href="{{ route('customer.order-success', $pesanan->idpesanan) }}"
                                   class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 text-sm text-center">
                                    <i class="ph ph-qr-code"></i> Lihat QR
                                </a>
                            </div>
                        </div>

                        {{-- Show items preview --}}
                        <div class="mt-3 pt-3 border-t">
                            <p class="text-xs text-gray-500 mb-1">Item pesanan:</p>
                            <div class="flex flex-wrap gap-1">
                                @foreach($pesanan->detailPesanan->take(3) as $detail)
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">
                                        {{ $detail->jumlah }}x {{ $detail->menu->nama_menu }}
                                    </span>
                                @endforeach
                                @if($pesanan->detailPesanan->count() > 3)
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">
                                        +{{ $pesanan->detailPesanan->count() - 3 }} lainnya
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
