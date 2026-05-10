<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - Kantin Online</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        html { font-size: 14px; }
        .text-xs { font-size: 0.75rem !important; }
        .text-sm { font-size: 0.875rem !important; }
        .text-base { font-size: 0.9375rem !important; }
        .text-lg { font-size: 1rem !important; }
        .text-xl { font-size:1.0625rem !important; }
        .text-2xl { font-size: 1.125rem !important; }
        .text-3xl { font-size: 1.25rem !important; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center">
                            <i class="ph ph-storefront text-xl text-white"></i>
                        </div>
                        <span class="text-xl font-bold text-gray-900">Admin Panel</span>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                    <form action="{{ route('auth.logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 text-gray-600 hover:text-red-500 transition-colors">
                            <i class="ph ph-sign-out text-lg"></i>
                            <span class="hidden sm:inline">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-gray-200 min-h-[calc(100vh-73px)] p-4">
            <nav class="space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="ph ph-squares-four text-xl"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
                <a href="{{ route('admin.vendors') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.vendors*') ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="ph ph-storefront text-xl"></i>
                    <span class="font-medium">Vendor</span>
                </a>
                <a href="{{ route('admin.menus') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.menus*') ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="ph ph-bowl-food text-xl"></i>
                    <span class="font-medium">Menu</span>
                </a>
                <a href="{{ route('admin.orders') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.orders*') ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="ph ph-shopping-cart text-xl"></i>
                    <span class="font-medium">Pesanan</span>
                </a>

                <!-- Kunjungan Toko -->
                <div class="pt-4">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Kunjungan Toko</p>
                    <a href="{{ route('admin.stores.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.stores*') ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-gray-100' }}">
                        <i class="ph ph-map-pin text-xl"></i>
                        <span class="font-medium">Data Toko</span>
                    </a>
                </div>

                <!-- Customer Management -->
                <div class="pt-4">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Customer</p>
                    <a href="{{ route('customer-management.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('customer-management.index') ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-gray-100' }}">
                        <i class="ph ph-users text-xl"></i>
                        <span class="font-medium">Data Customer</span>
                    </a>
                    <a href="{{ route('customer-management.create-blob') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('customer-management.create-blob') ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-gray-100' }}">
                        <i class="ph ph-camera-plus text-xl"></i>
                        <span class="font-medium">Tambah (BLOB)</span>
                    </a>
                    <a href="{{ route('customer-management.create-file') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('customer-management.create-file') ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-gray-100' }}">
                        <i class="ph ph-image text-xl"></i>
                        <span class="font-medium">Tambah (File)</span>
                    </a>
                </div>
            </nav>

            <div class="mt-8 pt-8 border-t border-gray-200">
                <a href="{{ url('/') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-gray-100">
                    <i class="ph ph-house text-xl"></i>
                    <span class="font-medium">Ke Halaman Customer</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            @session('success')
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-2">
                    <i class="ph ph-check-circle text-xl"></i>
                    <span>{{ $value }}</span>
                </div>
            @endsession

            @session('error')
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-2">
                    <i class="ph ph-warning-circle text-xl"></i>
                    <span>{{ $value }}</span>
                </div>
            @endsession

            @yield('content')
        </main>
    </div>

    <script>
        function formatRupiah(amount) {
            return 'Rp ' + amount.toLocaleString('id-ID');
        }
    </script>
    @stack('scripts')
</body>
</html>
