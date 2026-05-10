@extends('layouts.vendor')

@section('title', 'Kunjungan Toko')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Daftar Toko</h2>
            <div class="flex gap-3">
                <a href="{{ route('vendor.store-visit.visit') }}" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition-colors flex items-center gap-2">
                    <i class="ph ph-qr-code"></i> Scan Kunjungan
                </a>
                <a href="{{ route('vendor.store-visit.history') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors flex items-center gap-2">
                    <i class="ph ph-clock-counter-clockwise"></i> Riwayat
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barcode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Toko</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Koordinat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Kunjungan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($stores as $store)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-sm">{{ $store->barcode }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $store->nama_toko }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $store->alamat ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                            {{ $store->latitude }}, {{ $store->longitude }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-medium">
                                {{ $store->acceptedVisits()->count() }} kunjungan
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada data toko</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
