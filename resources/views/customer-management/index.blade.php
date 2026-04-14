@extends('layouts.admin')

@section('title', 'Data Customer')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Data Customer</h1>
    <p class="text-gray-600">Kelola data customer dan foto</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
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

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Foto</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Nama</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Tipe Foto</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Terdaftar</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($customers as $customer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            @if($customer->photo_path || $customer->photo_blob)
                                <img src="{{ $customer->photo_url }}" alt="{{ $customer->name }}"
                                     class="w-12 h-12 rounded-lg object-cover">
                            @else
                                <div class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center">
                                    <i class="ph ph-user text-gray-400 text-xl"></i>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-600">#{{ $customer->id }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $customer->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $customer->email }}</td>
                        <td class="px-6 py-4">
                            @if($customer->photo_blob)
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-700">BLOB</span>
                            @elseif($customer->photo_path)
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-orange-100 text-orange-700">File</span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-600">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-600 text-sm">
                            {{ $customer->created_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('customer-management.edit', $customer->id) }}"
                                   class="text-blue-500 hover:text-blue-600 p-1 rounded hover:bg-blue-50"
                                   title="Edit">
                                    <i class="ph ph-pencil-simple text-lg"></i>
                                </a>
                                <button onclick="confirmDelete({{ $customer->id }}, '{{ $customer->name }}')"
                                        class="text-red-500 hover:text-red-600 p-1 rounded hover:bg-red-50"
                                        title="Hapus">
                                    <i class="ph ph-trash text-lg"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="ph ph-users text-4xl mb-2 block"></i>
                            <p>Belum ada data customer</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($customers->hasPages())
        <div class="p-6 border-t border-gray-100">
            {{ $customers->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
    function confirmDelete(id, name) {
        if (confirm('Apakah Anda yakin ingin menghapus customer "' + name + '"?')) {
            $.ajax({
                url: '{{ route('customer-management.index') }}/' + id,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        window.location.reload();
                    }
                },
                error: function(xhr) {
                    alert('Gagal menghapus customer');
                }
            });
        }
    }
</script>
@endpush
@endsection
