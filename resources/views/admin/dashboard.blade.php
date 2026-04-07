@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
    <p class="text-gray-600">Selamat datang kembali, {{ auth()->user()->name }}!</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Vendor</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total_vendors'] }}</p>
            </div>
            <div class="w-14 h-14 bg-orange-100 rounded-2xl flex items-center justify-center">
                <i class="ph ph-storefront text-2xl text-orange-500"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Menu</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total_menus'] }}</p>
            </div>
            <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center">
                <i class="ph ph-bowl-food text-2xl text-blue-500"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Pesanan</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total_orders'] }}</p>
            </div>
            <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center">
                <i class="ph ph-shopping-cart text-2xl text-green-500"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Pendapatan</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ formatRupiah($stats['total_revenue']) }}</p>
            </div>
            <div class="w-14 h-14 bg-purple-100 rounded-2xl flex items-center justify-center">
                <i class="ph ph-currency-dollar text-2xl text-purple-500"></i>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100">
    <div class="p-6 border-b border-gray-100">
        <h2 class="text-lg font-bold text-gray-900">Pesanan Terbaru</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">No. Pesanan</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Customer</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Vendor</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @if($recentOrders->count() === 0)
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="ph ph-shopping-cart text-4xl mb-2 block"></i>
                            Belum ada pesanan
                        </td>
                    </tr>
                @else
                    @foreach($recentOrders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm">{{ $order->payment_reference }}</span>
                            </td>
                            <td class="px-6 py-4">{{ $order->user ? $order->user->name : 'Guest' }}</td>
                            <td class="px-6 py-4">{{ $order->vendor->nama_vendor }}</td>
                            <td class="px-6 py-4 text-gray-600 text-sm">{{ $order->timestamp->format('d M Y, H:i') }}</td>
                            <td class="px-6 py-4 text-right font-semibold text-orange-600">{{ formatRupiah($order->total) }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    @if($order->status_bayar === 'lunas') bg-green-100 text-green-700
                                    @elseif($order->status_bayar === 'pending') bg-yellow-100 text-yellow-700
                                    @else bg-red-100 text-red-700 @endif">
                                    {{ ucfirst($order->status_bayar) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
