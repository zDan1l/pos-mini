@extends('layouts.admin')

@section('title', 'Kelola Vendor')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Kelola Vendor</h1>
        <p class="text-sm text-gray-600">Kelola data vendor dan akun login</p>
    </div>
    <a href="{{ route('admin.create-vendor') }}" class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-4 py-2 rounded-lg text-sm transition-all">
        <i class="ph ph-plus"></i>
        <span>Tambah Vendor</span>
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Vendor</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Akun Login</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Menu</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @if($vendors->count() === 0)
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                        <i class="ph ph-storefront text-2xl mb-1 block"></i>
                        Belum ada vendor
                    </td>
                </tr>
            @else
                @foreach($vendors as $vendor)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                    <i class="ph ph-storefront text-lg text-orange-500"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-sm text-gray-900">{{ $vendor->nama_vendor }}</p>
                                    <p class="text-xs text-gray-500">{{ $vendor->kode_vendor }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if($vendor->user)
                                <div class="flex items-center gap-2">
                                    <i class="ph ph-user-circle text-green-500"></i>
                                    <div>
                                        <p class="text-xs font-medium text-gray-900">{{ $vendor->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $vendor->user->email }}</p>
                                    </div>
                                </div>
                            @else
                                <a href="{{ route('admin.create-vendor-account', $vendor->idvendor) }}" class="text-xs text-orange-500 hover:text-orange-600 flex items-center gap-1">
                                    <i class="ph ph-plus-circle"></i> Buat Akun
                                </a>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs text-gray-900">{{ $vendor->menus->count() }} menu</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                @if($vendor->user)
                                    <a href="{{ route('admin.edit-vendor', $vendor->idvendor) }}" class="p-1.5 text-gray-600 hover:text-orange-500 hover:bg-orange-50 rounded-lg transition-all" title="Edit">
                                        <i class="ph ph-pencil text-sm"></i>
                                    </a>
                                @else
                                    <a href="{{ route('admin.edit-vendor', $vendor->idvendor) }}" class="p-1.5 text-gray-600 hover:text-orange-500 hover:bg-orange-50 rounded-lg transition-all" title="Edit Vendor">
                                        <i class="ph ph-pencil text-sm"></i>
                                    </a>
                                @endif
                                <form action="{{ route('admin.destroy-vendor', $vendor->idvendor) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus vendor ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-600 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all" title="Hapus">
                                        <i class="ph ph-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
@endsection
