@extends('layouts.admin')

@section('title', 'Edit Menu')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.menus') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-orange-500">
        <i class="ph ph-arrow-left"></i>
        <span>Kembali ke Daftar Menu</span>
    </a>
    <h1 class="text-2xl font-bold text-gray-900 mt-4">Edit Menu</h1>
    <p class="text-gray-600">Edit data menu</p>
</div>

<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.update-menu', $menu->idmenu) }}" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Vendor</label>
                <select name="idvendor" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->idvendor }}" {{ old('idvendor', $menu->idvendor) == $vendor->idvendor ? 'selected' : '' }}>{{ $vendor->nama_vendor }}</option>
                    @endforeach
                </select>
                @error('idvendor')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Menu</label>
                <input type="text" name="nama_menu" required
                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                       value="{{ old('nama_menu', $menu->nama_menu) }}">
                @error('nama_menu')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Harga</label>
                <input type="number" name="harga" required min="0" step="100"
                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                       value="{{ old('harga', $menu->harga) }}">
                @error('harga')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ganti Gambar</label>
                @if($menu->path_gambar)
                    <div class="mb-4">
                        <img src="/storage/{{ $menu->path_gambar }}" alt="{{ $menu->nama_menu }}" class="w-32 h-32 object-cover rounded-xl">
                    </div>
                @endif
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-orange-500 transition-colors">
                    <input type="file" name="path_gambar" accept="image/*" id="menu-image" class="hidden" onchange="previewImage(this)">
                    <label for="menu-image" class="cursor-pointer">
                        <i class="ph ph-upload-simple text-4xl text-gray-400 mb-2"></i>
                        <p class="text-gray-600">Klik untuk upload gambar baru</p>
                        <p class="text-sm text-gray-400">Kosongkan jika tidak ingin mengubah</p>
                    </label>
                    <div id="image-preview" class="mt-4 hidden">
                        <img src="" alt="Preview" class="max-h-40 mx-auto rounded-lg">
                    </div>
                </div>
                @error('path_gambar')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_available" value="1" {{ $menu->is_available ? 'checked' : '' }} class="w-5 h-5 text-orange-500 border-gray-300 rounded focus:ring-orange-500">
                    <span class="text-sm font-medium text-gray-700">Menu Tersedia</span>
                </label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.menus') }}" class="px-6 py-2.5 border border-gray-300 rounded-xl hover:bg-gray-50 transition-all font-medium">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-xl transition-all font-semibold">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function previewImage(input) {
        const preview = document.getElementById('image-preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.querySelector('img').src = e.target.result;
                preview.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endsection
