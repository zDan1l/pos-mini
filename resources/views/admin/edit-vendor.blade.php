@extends('layouts.admin')

@section('title', 'Edit Vendor')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.vendors') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-orange-500">
        <i class="ph ph-arrow-left"></i>
        <span>Kembali ke Daftar Vendor</span>
    </a>
    <h1 class="text-xl font-bold text-gray-900 mt-4">Edit Vendor</h1>
    <p class="text-sm text-gray-600">Edit data vendor dan akun login</p>
</div>

<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.update-vendor', $vendor->idvendor) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Vendor</label>
                    <input type="text" name="nama_vendor" required
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm"
                           value="{{ old('nama_vendor', $vendor->nama_vendor) }}">
                    @error('nama_vendor')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Vendor</label>
                    <input type="text" name="kode_vendor" required
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 uppercase text-sm"
                           value="{{ old('kode_vendor', $vendor->kode_vendor) }}">
                    @error('kode_vendor')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <h3 class="font-semibold text-gray-900 mb-3 text-sm">Akun Login Vendor</h3>

                @if($vendor->user)
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="name" required
                                   class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm"
                                   value="{{ old('name', $vendor->user->name) }}">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" required
                                   class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm"
                                   value="{{ old('email', $vendor->user->email) }}">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru (opsional)</label>
                        <input type="password" name="password"
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm"
                               placeholder="Kosongkan jika tidak ingin mengubah">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @else
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-2 text-gray-600 text-sm">
                            <i class="ph ph-info"></i>
                            <span>Vendor ini belum memiliki akun login</span>
                        </div>
                        <a href="{{ route('admin.create-vendor-account', $vendor->idvendor) }}" class="text-xs bg-orange-500 text-white px-3 py-1.5 rounded-lg hover:bg-orange-600 transition-colors">
                            <i class="ph ph-plus"></i> Buat Akun
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
            <a href="{{ route('admin.vendors') }}" class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </a>
            @if($vendor->user)
                <button type="submit" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-sm transition-colors">
                    Simpan
                </button>
            @endif
        </div>
    </form>
</div>
@endsection
