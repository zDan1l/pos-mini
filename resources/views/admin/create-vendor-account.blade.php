@extends('layouts.admin')

@section('title', 'Buat Akun Vendor')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.vendors') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-orange-500">
        <i class="ph ph-arrow-left"></i>
        <span>Kembali ke Daftar Vendor</span>
    </a>
    <h1 class="text-xl font-bold text-gray-900 mt-4">Buat Akun Vendor</h1>
    <p class="text-sm text-gray-600">Buat akun login untuk {{ $vendor->nama_vendor }}</p>
</div>

<div class="max-w-md">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="mb-4 p-3 bg-orange-50 rounded-lg flex items-center gap-2 text-orange-700">
            <i class="ph ph-info text-lg"></i>
            <span class="text-sm">Vendor: {{ $vendor->nama_vendor }}</span>
        </div>

        <form method="POST" action="{{ route('admin.store-vendor-account', $vendor->idvendor) }}">
            @csrf

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" required
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm"
                           placeholder="Nama pemilik/vendor" value="{{ old('name') }}">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" required
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm"
                           placeholder="vendor@email.com" value="{{ old('email') }}">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm"
                           placeholder="Minimal 6 karakter">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.vendors') }}" class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-sm transition-colors">
                    <i class="ph ph-check"></i> Buat Akun
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
