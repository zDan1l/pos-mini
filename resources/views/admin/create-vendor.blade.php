@extends('layouts.admin')

@section('title', 'Tambah Vendor')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.vendors') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-orange-500">
        <i class="ph ph-arrow-left"></i>
        <span>Kembali ke Daftar Vendor</span>
    </a>
    <h1 class="text-2xl font-bold text-gray-900 mt-4">Tambah Vendor Baru</h1>
    <p class="text-gray-600">Buat vendor dan akun login untuk vendor tersebut</p>
</div>

<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.store-vendor') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        @csrf

        <div class="space-y-6">
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Vendor</label>
                    <input type="text" name="nama_vendor" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                           placeholder="Contoh: Warung Bu Siti" value="{{ old('nama_vendor') }}">
                    @error('nama_vendor')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kode Vendor</label>
                    <input type="text" name="kode_vendor" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 uppercase"
                           placeholder="Contoh: WBS01" value="{{ old('kode_vendor') }}">
                    @error('kode_vendor')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h3 class="font-semibold text-gray-900 mb-4">Akun Login Vendor</h3>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" required
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                               placeholder="Nama pemilik/vendor" value="{{ old('name') }}">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" required
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                               placeholder="vendor@email.com" value="{{ old('email') }}">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                           placeholder="Minimal 6 karakter">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.vendors') }}" class="px-6 py-2.5 border border-gray-300 rounded-xl hover:bg-gray-50 transition-all font-medium">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-xl transition-all font-semibold">
                Simpan Vendor
            </button>
        </div>
    </form>
</div>
@endsection
