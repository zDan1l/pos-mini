@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="max-w-4xl mx-auto">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Checkout</h2>

    <div class="grid md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <!-- Customer Name -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Informasi Customer</h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Anda (Opsional)</label>
                    <input type="text" id="customer_name" name="customer_name"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                           placeholder="Kosongkan untuk menjadi Guest_0000001">
                    <p class="text-sm text-gray-500 mt-2">Kosongkan untuk otomatis terdaftar sebagai Guest_0000001</p>
                </div>
            </div>

            <!-- Pesanan Summary -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Ringkasan Pesanan</h3>
                <p class="text-gray-600 mb-4"><strong>Vendor:</strong> {{ $vendor->nama_vendor }}</p>

                <div class="space-y-3">
                    @foreach($cartItems as $item)
                        <div class="flex justify-between items-center py-2 border-b">
                            <div class="flex items-center gap-3">
                                @if($item['path_gambar'])
                                    <img src="/storage/{{ $item['path_gambar'] }}" alt="{{ $item['nama_menu'] }}" class="w-12 h-12 object-cover rounded">
                                @else
                                    <div class="w-12 h-12 bg-gray-200 rounded"></div>
                                @endif
                                <div>
                                    <p class="font-medium">{{ $item['nama_menu'] }}</p>
                                    <p class="text-sm text-gray-500">{{ $item['qty'] }} x {{ formatRupiah($item['harga']) }}</p>
                                </div>
                            </div>
                            <p class="font-semibold">{{ formatRupiah($item['subtotal']) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Metode Pembayaran -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Metode Pembayaran</h3>

                <div class="space-y-3">
                    <label class="flex items-center p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="radio" name="metode_bayar" value="qris" checked class="mr-3">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center text-white font-bold text-xs">QRIS</div>
                            <div>
                                <p class="font-medium">QRIS</p>
                                <p class="text-sm text-gray-500">Scan QR untuk membayar</p>
                            </div>
                        </div>
                    </label>

                    <label class="flex items-center p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="radio" name="metode_bayar" value="bank_transfer" class="mr-3">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center text-white font-bold text-xs">VA</div>
                            <div>
                                <p class="font-medium">Virtual Account</p>
                                <p class="text-sm text-gray-500">Transfer melalui VA BCA/Mandiri/BNI</p>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="md:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                <h3 class="text-lg font-semibold mb-4">Total Pembayaran</h3>

                <div class="space-y-2 mb-6">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span>{{ formatRupiah($total) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Biaya Layanan</span>
                        <span>Rp 0</span>
                    </div>
                    <div class="border-t pt-2 mt-2">
                        <div class="flex justify-between font-bold text-xl">
                            <span>Total</span>
                            <span class="text-orange-500">{{ formatRupiah($total) }}</span>
                        </div>
                    </div>
                </div>

                <button onclick="processPayment()" id="btn-pay" class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 px-4 rounded-xl transition-colors font-semibold disabled:bg-gray-400 disabled:cursor-not-allowed">
                    Bayar Sekarang
                </button>

                <a href="{{ route('customer.cart') }}" class="block w-full text-center text-gray-600 mt-4 hover:text-gray-800">
                    &larr; Kembali ke Keranjang
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function processPayment() {
        const metodeBayar = $('input[name="metode_bayar"]:checked').val();
        const customerName = $('#customer_name').val();
        const btnPay = $('#btn-pay');

        if (!metodeBayar) {
            alert('Pilih metode pembayaran');
            return;
        }

        btnPay.prop('disabled', true).text('Memproses...');

        $.ajax({
            url: '{{ route('customer.process-payment') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                metode_bayar: metodeBayar,
                customer_name: customerName
            },
            success: function(response) {
                if (response.success) {
                    window.location.href = response.redirect_url;
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON;
                alert(error.message || 'Terjadi kesalahan');
                btnPay.prop('disabled', false).text('Bayar Sekarang');
            }
        });
    }
</script>
@endpush
@endsection
