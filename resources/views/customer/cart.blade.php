@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')
<div class="max-w-4xl mx-auto">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Keranjang Belanja</h2>

    @if(empty($cartItems))
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <i class="ph ph-shopping-cart text-6xl text-gray-300 mb-4 block"></i>
            <h3 class="text-xl font-medium text-gray-600 mb-2">Keranjang Kosong</h3>
            <p class="text-gray-500 mb-6">Anda belum memiliki item di keranjang</p>
            <a href="{{ route('customer.index') }}" class="inline-flex items-center gap-2 bg-orange-500 text-white py-3 px-6 rounded-xl hover:bg-orange-600 transition-colors">
                <i class="ph ph-plus"></i> Mulai Belanja
            </a>
        </div>
    @else
        <div class="grid md:grid-cols-3 gap-6">
            <div class="md:col-span-2">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Vendor: {{ $vendor->nama_vendor }}</h3>
                        <button onclick="clearCart()" class="text-red-500 hover:text-red-700 text-sm flex items-center gap-1">
                            <i class="ph ph-trash"></i> Kosongkan Keranjang
                        </button>
                    </div>

                    <div class="space-y-4">
                        @foreach($cartItems as $item)
                            <div class="flex items-center gap-4 p-4 border border-gray-100 rounded-xl">
                                @if($item['path_gambar'])
                                    <img src="/storage/{{ $item['path_gambar'] }}" alt="{{ $item['nama_menu'] }}" class="w-20 h-20 object-cover rounded-lg">
                                @else
                                    <div class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <i class="ph ph-bowl-food text-2xl text-gray-300"></i>
                                    </div>
                                @endif

                                <div class="flex-1">
                                    <h4 class="font-semibold">{{ $item['nama_menu'] }}</h4>
                                    <p class="text-orange-500 font-bold">{{ formatRupiah($item['harga']) }}</p>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button onclick="updateQty({{ $item['idmenu'] }}, {{ $item['qty'] - 1 }})"
                                            class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center">
                                        <i class="ph ph-minus"></i>
                                    </button>
                                    <span class="w-8 text-center font-medium">{{ $item['qty'] }}</span>
                                    <button onclick="updateQty({{ $item['idmenu'] }}, {{ $item['qty'] + 1 }})"
                                            class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center">
                                        <i class="ph ph-plus"></i>
                                    </button>
                                </div>

                                <div class="text-right w-24">
                                    <p class="font-bold text-orange-600">{{ formatRupiah($item['subtotal']) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="md:col-span-1">
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-4">
                    <h3 class="text-lg font-semibold mb-4">Ringkasan Pesanan</h3>

                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span>{{ formatRupiah($total) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Biaya Layanan</span>
                            <span>Rp 0</span>
                        </div>
                        <div class="border-t pt-2 mt-2">
                            <div class="flex justify-between font-bold text-lg">
                                <span>Total</span>
                                <span class="text-orange-500">{{ formatRupiah($total) }}</span>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('customer.checkout') }}" class="block w-full bg-orange-500 text-white text-center py-3 px-4 rounded-xl hover:bg-orange-600 transition-colors font-semibold">
                        Lanjut ke Pembayaran
                    </a>

                    <a href="{{ route('customer.index') }}" class="block w-full text-center text-gray-600 mt-4 hover:text-gray-800">
                        &larr; Kembali belanja
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    function updateQty(idmenu, jumlah) {
        if (jumlah < 0) return;

        $.ajax({
            url: '{{ route('customer.update-cart') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                idmenu: idmenu,
                jumlah: jumlah
            },
            success: function(response) {
                if (response.success) {
                    updateCartBadge(response.cart_count);
                    location.reload();
                }
            }
        });
    }

    function clearCart() {
        if (confirm('Yakin ingin mengosongkan keranjang?')) {
            $.ajax({
                url: '{{ route('customer.clear-cart') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        updateCartBadge(0);
                        location.reload();
                    }
                }
            });
        }
    }
</script>
@endpush
@endsection
