<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kantin Online')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        /* Reduce all font sizes */
        html { font-size: 14px; }
        .text-xs { font-size: 0.75rem !important; }
        .text-sm { font-size: 0.875rem !important; }
        .text-base { font-size: 0.9375rem !important; }
        .text-lg { font-size: 1rem !important; }
        .text-xl { font-size: 1.0625rem !important; }
        .text-2xl { font-size: 1.125rem !important; }
        .text-3xl { font-size: 1.25rem !important; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-orange-500 text-white shadow-lg">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <i class="ph ph-storefront text-2xl"></i>
                    <h1 class="text-xl font-bold">Kantin Online</h1>
                </div>
                <div class="flex gap-4 items-center">
                    <a href="{{ route('customer.index') }}" class="hover:text-orange-100 flex items-center gap-1">
                        <i class="ph ph-house"></i> Beranda
                    </a>

                    {{-- Customer Management Menu (Admin only) --}}
                    @if(auth()->check() && auth()->user()->isAdmin())
                        <div class="relative group">
                            <button class="hover:text-orange-100 flex items-center gap-1">
                                <i class="ph ph-users"></i> Customer
                                <i class="ph ph-caret-down text-xs"></i>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 hidden group-hover:block z-50">
                                <a href="{{ route('customer-management.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-orange-50 hover:text-orange-600">
                                    <i class="ph ph-list-dashes mr-2"></i> Data Customer
                                </a>
                                <a href="{{ route('customer-management.create-blob') }}" class="block px-4 py-2 text-gray-700 hover:bg-orange-50 hover:text-orange-600">
                                    <i class="ph ph-camera-plus mr-2"></i> Tambah (BLOB)
                                </a>
                                <a href="{{ route('customer-management.create-file') }}" class="block px-4 py-2 text-gray-700 hover:bg-orange-50 hover:text-orange-600">
                                    <i class="ph ph-image mr-2"></i> Tambah (File)
                                </a>
                            </div>
                        </div>
                    @endif
                    <a href="{{ route('customer.cart') }}" class="hover:text-orange-100 flex items-center gap-1 relative">
                        <i class="ph ph-shopping-cart"></i> Keranjang
                        @if(session('cart'))
                            <span class="cart-badge" id="cart-badge">{{ array_sum(session('cart')) }}</span>
                        @endif
                    </a>
                    @if(auth()->check())
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="text-sm hover:text-orange-100 flex items-center gap-1">
                                <i class="ph ph-shield-check"></i> Admin
                            </a>
                        @elseif(auth()->user()->isVendor())
                            <a href="{{ route('vendor.dashboard') }}" class="text-sm hover:text-orange-100 flex items-center gap-1">
                                <i class="ph ph-storefront"></i> Panel
                            </a>
                        @endif
                        <form action="{{ route('auth.logout') }}" method="POST" class="inline ml-2">
                            @csrf
                            <button type="submit" class="text-sm hover:text-orange-100 flex items-center gap-1">
                                <i class="ph ph-sign-out"></i> Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('auth.login') }}" class="text-sm hover:text-orange-100 flex items-center gap-1">
                            <i class="ph ph-sign-in"></i> Login
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-6">
        @session('success')
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 flex items-center gap-2">
                <i class="ph ph-check-circle text-xl"></i>
                <span>{{ $value }}</span>
            </div>
        @endsession

        @session('error')
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 flex items-center gap-2">
                <i class="ph ph-warning-circle text-xl"></i>
                <span>{{ $value }}</span>
            </div>
        @endsession

        @yield('content')
    </main>

    <script>
        function updateCartBadge(count) {
            const badge = document.getElementById('cart-badge');
            if (badge) {
                if (count > 0) {
                    badge.textContent = count;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            }
        }

        function formatRupiah(amount) {
            return 'Rp ' + amount.toLocaleString('id-ID');
        }
    </script>
    @stack('scripts')
</body>
</html>
