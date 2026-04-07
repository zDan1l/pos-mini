@extends('layouts.admin')

@section('title', 'Kelola Menu')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Kelola Menu</h1>
        <p class="text-gray-600">Kelola semua menu dari semua vendor</p>
    </div>
    <a href="{{ route('admin.create-menu') }}" class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-5 py-2.5 rounded-xl transition-all">
        <i class="ph ph-plus"></i>
        <span>Tambah Menu</span>
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @if($menus->count() === 0)
        <div class="col-span-full bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center text-gray-500">
            <i class="ph ph-bowl-food text-4xl mb-2 block"></i>
            Belum ada menu
        </div>
    @else
        @foreach($menus as $menu)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                @if($menu->path_gambar)
                    <img src="/storage/{{ $menu->path_gambar }}" alt="{{ $menu->nama_menu }}" class="w-full h-40 object-cover">
                @else
                    <div class="w-full h-40 bg-gray-100 flex items-center justify-center">
                        <i class="ph ph-bowl-food text-4xl text-gray-300"></i>
                    </div>
                @endif
                <div class="p-4">
                    <div class="flex items-start justify-between mb-2">
                        <h3 class="font-semibold text-gray-900">{{ $menu->nama_menu }}</h3>
                        @if($menu->is_available)
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full">Tersedia</span>
                        @else
                            <span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs rounded-full">Habis</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 mb-1">{{ $menu->vendor->nama_vendor }}</p>
                    <p class="font-bold text-orange-600 mb-3">{{ formatRupiah($menu->harga) }}</p>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.edit-menu', $menu->idmenu) }}" class="flex-1 text-center py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-all">
                            Edit
                        </a>
                        <form action="{{ route('admin.destroy-menu', $menu->idmenu) }}" method="POST" class="flex-1" onsubmit="return confirm('Yakin ingin menghapus menu ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-sm font-medium transition-all">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
