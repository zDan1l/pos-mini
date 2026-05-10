@extends('layouts.admin')

@section('title', 'Daftar Toko')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Daftar Toko</h2>
        <a href="{{ route('admin.stores.create') }}" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition-colors flex items-center gap-2">
            <i class="ph ph-plus"></i> Tambah Toko
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barcode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Toko</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Latitude</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Longitude</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Accuracy</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
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
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $store->latitude }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $store->longitude }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $store->accuracy }}m</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.stores.barcode', $store->idtoko) }}" target="_blank" class="text-blue-500 hover:text-blue-700" title="Cetak Barcode">
                                <i class="ph ph-qr-code text-lg"></i>
                            </a>
                            <a href="{{ route('admin.stores.edit', $store->idtoko) }}" class="text-yellow-500 hover:text-yellow-700" title="Edit">
                                <i class="ph ph-pencil-simple text-lg"></i>
                            </a>
                            <form action="{{ route('admin.stores.destroy', $store->idtoko) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus toko ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700" title="Hapus">
                                    <i class="ph ph-trash text-lg"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">Belum ada data toko</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
