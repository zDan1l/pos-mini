@extends('layouts.app')

@section('title', 'Data Customer')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Data Customer</h2>
                    <p class="text-sm text-gray-500 mt-1">Kelola data customer dan foto</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('customer-management.create-blob') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors flex items-center gap-2">
                        <i class="ph ph-camera-plus"></i>
                        <span>Tambah (BLOB)</span>
                    </a>
                    <a href="{{ route('customer-management.create-file') }}" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition-colors flex items-center gap-2">
                        <i class="ph ph-image"></i>
                        <span>Tambah (File)</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Foto</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">ID</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Nama</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Email</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Tipe Foto</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Terdaftar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 px-4">
                                    @if($customer->photo_path || $customer->photo_blob)
                                        <img src="{{ $customer->photo_url }}" alt="{{ $customer->name }}"
                                             class="w-12 h-12 rounded-lg object-cover">
                                    @else
                                        <div class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center">
                                            <i class="ph ph-user text-gray-400 text-xl"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-gray-600">#{{ $customer->id }}</td>
                                <td class="py-3 px-4 font-medium text-gray-800">{{ $customer->name }}</td>
                                <td class="py-3 px-4 text-gray-600">{{ $customer->email }}</td>
                                <td class="py-3 px-4">
                                    @if($customer->photo_blob)
                                        <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full">BLOB</span>
                                    @elseif($customer->photo_path)
                                        <span class="bg-orange-100 text-orange-700 text-xs px-2 py-1 rounded-full">File</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-gray-600 text-sm">
                                    {{ $customer->created_at->format('d M Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-500">
                                    <i class="ph ph-users text-4xl mb-2 block"></i>
                                    <p>Belum ada data customer</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($customers->hasPages())
                <div class="mt-6">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
