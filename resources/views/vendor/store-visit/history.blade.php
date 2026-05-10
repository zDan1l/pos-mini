@extends('layouts.vendor')

@section('title', 'Riwayat Kunjungan')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Riwayat Kunjungan</h2>
            <a href="{{ route('vendor.store-visit.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors flex items-center gap-2">
                <i class="ph ph-arrow-left"></i> Kembali
            </a>
        </div>

        @if($visits->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Toko</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jarak</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi Anda</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($visits as $visit)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="font-medium">{{ $visit->store->nama_toko }}</p>
                            <p class="text-xs text-gray-500">{{ $visit->store->barcode }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($visit->status === 'diterima')
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium">
                                <i class="ph ph-check-circle"></i> Diterima
                            </span>
                            @else
                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-medium">
                                <i class="ph ph-x-circle"></i> Ditolak
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ number_format($visit->distance_from_store, 2) }}m
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <p>{{ $visit->visit_latitude }}, {{ $visit->visit_longitude }}</p>
                            <p class="text-xs">Akurasi: {{ number_format($visit->visit_accuracy, 0) }}m</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $visit->visited_at->format('d M Y H:i') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $visits->appends(request()->query())->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <i class="ph ph-clock-counter-clockwise text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500">Belum ada riwayat kunjungan</p>
            <a href="{{ route('vendor.store-visit.visit') }}" class="inline-block mt-4 bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition-colors">
                Mulai Kunjungan
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
