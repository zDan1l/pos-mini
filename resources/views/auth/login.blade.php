<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kantin Online</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        html { font-size: 14px; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-orange-50">
    <div class="min-h-screen flex">
        <!-- Left Side - Branding -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-orange-500 to-orange-600 p-12 flex-col justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center">
                        <i class="ph ph-storefront text-2xl text-orange-500"></i>
                    </div>
                    <span class="text-2xl font-bold text-white">Kantin Online</span>
                </div>
            </div>

            <div class="text-white">
                <h1 class="text-4xl font-bold mb-4">Kelola Kantin Anda dengan Mudah</h1>
                <p class="text-orange-100 text-lg">Platform pemesanan makanan online yang modern dan mudah digunakan untuk vendor dan pelanggan.</p>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white/20 backdrop-blur rounded-xl p-4 text-white text-center">
                    <i class="ph ph-users text-3xl mb-2"></i>
                    <p class="text-sm">1000+ Pengguna</p>
                </div>
                <div class="bg-white/20 backdrop-blur rounded-xl p-4 text-white text-center">
                    <i class="ph ph-shopping-cart text-3xl mb-2"></i>
                    <p class="text-sm">500+ Pesanan</p>
                </div>
                <div class="bg-white/20 backdrop-blur rounded-xl p-4 text-white text-center">
                    <i class="ph ph-storefront text-3xl mb-2"></i>
                    <p class="text-sm">50+ Vendor</p>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                <div class="lg:hidden mb-8 text-center">
                    <div class="flex items-center justify-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center">
                            <i class="ph ph-storefront text-2xl text-white"></i>
                        </div>
                        <span class="text-2xl font-bold text-orange-600">Kantin Online</span>
                    </div>
                </div>

                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang Kembali</h2>
                    <p class="text-gray-600">Masuk ke akun Anda untuk melanjutkan</p>
                </div>

                <form method="POST" action="{{ route('auth.login') }}" class="space-y-5">
                    @csrf

                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                            <div class="flex items-center gap-2 text-red-600 mb-1">
                                <i class="ph ph-warning-circle"></i>
                                <span class="font-medium">Terjadi Kesalahan</span>
                            </div>
                            <p class="text-red-600 text-sm">{{ $errors->first() }}</p>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <div class="relative">
                            <i class="ph ph-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input type="email" name="email" required autofocus
                                   class="w-full pl-12 pr-4 py-3.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all"
                                   placeholder="nama@email.com">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <i class="ph ph-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input type="password" name="password" required
                                   class="w-full pl-12 pr-12 py-3.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all"
                                   placeholder="••••••••">
                            <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="ph ph-eye text-lg" id="eye-icon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 text-orange-500 border-gray-300 rounded focus:ring-orange-500">
                            <span class="text-sm text-gray-600">Ingat saya</span>
                        </label>
                    </div>

                    <button type="submit"
                            class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3.5 rounded-xl transition-all flex items-center justify-center gap-2">
                        <span>Masuk</span>
                        <i class="ph ph-arrow-right"></i>
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-gray-600">Belum punya akun?
                        <a href="{{ route('auth.register') }}" class="text-orange-500 font-medium hover:text-orange-600">Daftar sebagai Vendor</a>
                    </p>
                </div>

                <div class="mt-8 pt-8 border-t border-gray-200">
                    <a href="{{ url('/') }}" class="flex items-center justify-center gap-2 text-gray-600 hover:text-orange-500 transition-colors">
                        <i class="ph ph-house"></i>
                        <span>Kembali ke Beranda</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.querySelector('input[name="password"]');
            const icon = document.getElementById('eye-icon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('ph-eye');
                icon.classList.add('ph-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('ph-eye-slash');
                icon.classList.add('ph-eye');
            }
        }
    </script>
</body>
</html>
