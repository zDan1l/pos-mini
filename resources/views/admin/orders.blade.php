@extends('layouts.admin')

@section('title', 'Pesanan')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Daftar Pesanan</h1>
    <p class="text-gray-600">Semua pesanan dari semua vendor</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">No. Pesanan</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Customer</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Vendor</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Metode</th>
                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">Total</th>
                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @if($orders->count() === 0)
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                        <i class="ph ph-shopping-cart text-4xl mb-2 block"></i>
                        Belum ada pesanan
                    </td>
                </tr>
            @else
                @foreach($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <span class="font-mono text-sm">{{ $order->payment_reference }}</span>
                        </td>
                        <td class="px-6 py-4">{{ $order->user ? $order->user->name : 'Guest' }}</td>
                        <td class="px-6 py-4">{{ $order->vendor->nama_vendor }}</td>
                        <td class="px-6 py-4 text-gray-600 text-sm">{{ $order->timestamp->format('d M Y, H:i') }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                @if($order->metode_bayar === 'qris') bg-red-100 text-red-700
                                @else bg-blue-100 text-blue-700 @endif">
                                {{ $order->metode_bayar === 'qris' ? 'QRIS' : 'VA' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right font-semibold text-orange-600">{{ formatRupiah($order->total) }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                @if($order->status_bayar === 'lunas') bg-green-100 text-green-700
                                @elseif($order->status_bayar === 'pending') bg-yellow-100 text-yellow-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ ucfirst($order->status_bayar) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('admin.order-detail', $order->idpesanan) }}" class="inline-flex items-center gap-1 text-orange-500 hover:text-orange-600">
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
@endsection
