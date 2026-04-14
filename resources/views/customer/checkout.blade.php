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
                    <p class="text-sm text-gray-500 mt-2">Kosongkan untuk otomatis terdaftar sebagai Guest</p>
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

            <!-- Metode Pembayaran Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Metode Pembayaran</h3>
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
                    <i class="ph ph-info text-2xl text-blue-500 mt-0.5"></i>
                    <div class="text-blue-700">
                        <p class="font-medium">Pembayaran Midtrans</p>
                        <p class="text-sm mt-1">Anda akan diarahkan ke halaman pembayaran Midtrans yang aman. Tersedia berbagai metode pembayaran seperti QRIS, Virtual Account, E-Wallet, dan lainnya.</p>
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap gap-2">
                    <span class="px-3 py-1 bg-gray-100 rounded-full text-xs text-gray-600">QRIS</span>
                    <span class="px-3 py-1 bg-gray-100 rounded-full text-xs text-gray-600">GoPay</span>
                    <span class="px-3 py-1 bg-gray-100 rounded-full text-xs text-gray-600">OVO</span>
                    <span class="px-3 py-1 bg-gray-100 rounded-full text-xs text-gray-600">Dana</span>
                    <span class="px-3 py-1 bg-gray-100 rounded-full text-xs text-gray-600">ShopeePay</span>
                    <span class="px-3 py-1 bg-gray-100 rounded-full text-xs text-gray-600">VA BCA</span>
                    <span class="px-3 py-1 bg-gray-100 rounded-full text-xs text-gray-600">VA Mandiri</span>
                    <span class="px-3 py-1 bg-gray-100 rounded-full text-xs text-gray-600">VA BNI</span>
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
                    <i class="ph ph-credit-card mr-2"></i> Bayar Sekarang
                </button>

                <a href="{{ route('customer.cart') }}" class="block w-full text-center text-gray-600 mt-4 hover:text-gray-800">
                    &larr; Kembali ke Keranjang
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script type="text/javascript"
  src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
  data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
    let pesananId = null;

    function processPayment() {
        const customerName = $('#customer_name').val();
        const btnPay = $('#btn-pay');

        btnPay.prop('disabled', true).html('<i class="ph ph-spinner ph-spin mr-2"></i> Memproses...');

        $.ajax({
            url: '{{ route('customer.process-payment') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                customer_name: customerName
            },
            success: function(response) {
                if (response.success && response.snap_token) {
                    // Save pesanan ID for redirect after payment
                    pesananId = response.pesanan_id;

                    // Open Midtrans Snap popup
                    snap.pay(response.snap_token, {
                        onSuccess: function(result) {
                            // Redirect to order success page with QR Code
                            if (pesananId) {
                                window.location.href = '/order/success/' + pesananId;
                            } else {
                                window.location.href = '{{ route('customer.index') }}';
                            }
                        },
                        onPending: function(result) {
                            // For pending status, also redirect to success page
                            // The page will check status and show appropriate message
                            if (pesananId) {
                                window.location.href = '/order/success/' + pesananId;
                            } else {
                                window.location.href = '{{ route('customer.index') }}';
                            }
                        },
                        onError: function(result) {
                            alert('Pembayaran gagal. Silakan coba lagi.');
                            btnPay.prop('disabled', false).html('<i class="ph ph-credit-card mr-2"></i> Bayar Sekarang');
                        },
                        onClose: function() {
                            // User closed the popup without completing payment
                            if (pesananId) {
                                // Redirect to payment page to check status
                                window.location.href = '/payment/' + pesananId;
                            } else {
                                btnPay.prop('disabled', false).html('<i class="ph ph-credit-card mr-2"></i> Bayar Sekarang');
                            }
                        }
                    });
                } else {
                    alert('Gagal memproses pembayaran');
                    btnPay.prop('disabled', false).html('<i class="ph ph-credit-card mr-2"></i> Bayar Sekarang');
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON;
                alert(error.message || 'Terjadi kesalahan');
                btnPay.prop('disabled', false).html('<i class="ph ph-credit-card mr-2"></i> Bayar Sekarang');
            }
        });
    }
</script>
@endpush
@endsection
