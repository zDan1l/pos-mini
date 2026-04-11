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
                    <tr class="hover:bg-gray-50 @if(!$vendor->user) bg-orange-50/50 @endif">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="relative">
                                    <div class="w-12 h-12 {{ $vendor->user ? 'bg-green-100' : 'bg-gray-100' }} rounded-xl flex items-center justify-center">
                                        <i class="ph ph-storefront text-xl {{ $vendor->user ? 'text-green-600' : 'text-gray-400' }}"></i>
                                    </div>
                                    @if($vendor->user)
                                        <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 rounded-full flex items-center justify-center">
                                            <i class="ph ph-check text-white text-xs"></i>
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold text-sm text-gray-900">{{ $vendor->nama_vendor }}</p>
                                    <p class="text-xs text-gray-500">{{ $vendor->kode_vendor }}</p>
                                    @if($vendor->user)
                                        <span class="inline-flex items-center gap-1 text-xs text-green-600 mt-1">
                                            <i class="ph ph-check-circle"></i> Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs text-orange-600 mt-1">
                                            <i class="ph ph-warning-circle"></i> Belum ada akun
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if($vendor->user)
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="ph ph-user text-green-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $vendor->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $vendor->user->email }}</p>
                                    </div>
                                </div>
                            @else
                                <a href="{{ route('admin.create-vendor-account', $vendor->idvendor) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-orange-50 hover:bg-orange-100 text-orange-600 rounded-lg text-xs font-medium transition-all">
                                    <i class="ph ph-plus-circle"></i>
                                    <span>Buat Akun</span>
                                </a>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1 text-sm text-gray-700">
                                <i class="ph ph-bowl-food text-orange-400"></i>
                                {{ $vendor->menus->count() }} menu
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.edit-vendor', $vendor->idvendor) }}" class="p-2 text-gray-600 hover:text-orange-500 hover:bg-orange-50 rounded-lg transition-all" title="Edit Vendor">
                                    <i class="ph ph-pencil-simple text-base"></i>
                                </a>
                                @if($vendor->user)
                                    <a href="{{ route('admin.edit-vendor', $vendor->idvendor) }}" class="p-2 text-gray-600 hover:text-blue-500 hover:bg-blue-50 rounded-lg transition-all" title="Edit Akun">
                                        <i class="ph ph-user-gear text-base"></i>
                                    </a>
                                @endif
                                <form action="{{ route('admin.destroy-vendor', $vendor->idvendor) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus vendor ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-600 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all" title="Hapus">
                                        <i class="ph ph-trash text-base"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
    @if($vendors->count() > 0)
        <div class="px-4 py-3 bg-gray-50 border-t text-xs text-gray-500 flex items-center justify-between">
            <span>Total {{ $vendors->count() }} vendor</span>
            <span class="flex items-center gap-4">
                <span class="flex items-center gap-1"><i class="ph ph-check-circle text-green-500"></i> {{ $vendors->whereNotNull('user_id')->count() }} punya akun</span>
                <span class="flex items-center gap-1"><i class="ph ph-warning-circle text-orange-500"></i> {{ $vendors->whereNull('user_id')->count() }} belum punya akun</span>
            </span>
        </div>
    @endif
</div>
@endsection
