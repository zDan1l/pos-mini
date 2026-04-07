<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Vendor - Kantin Online</title>
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
    <div class="min-h-screen flex items-center justify-center p-8">
        <div class="w-full max-w-lg">
            <div class="text-center mb-8">
                <div class="flex items-center justify-center gap-3 mb-4">
                    <div class="w-14 h-14 bg-orange-500 rounded-xl flex items-center justify-center">
                        <i class="ph ph-storefront text-2xl text-white"></i>
                    </div>
                    <span class="text-3xl font-bold text-orange-600">Kantin Online</span>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Daftar sebagai Vendor</h2>
                <p class="text-gray-600">Buat akun vendor untuk mulai berjualan</p>
            </div>

            <form method="POST" action="{{ route('auth.register') }}" class="bg-white rounded-2xl shadow-xl p-8 space-y-5">
                @csrf

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                        <div class="flex items-center gap-2 text-red-600 mb-2">
                            <i class="ph ph-warning-circle"></i>
                            <span class="font-medium">Terjadi Kesalahan</span>
                        </div>
                        @foreach ($errors->all() as $error)
                            <p class="text-red-600 text-sm">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <div class="relative">
                            <i class="ph ph-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input type="text" name="name" required autofocus
                                   class="w-full pl-12 pr-4 py-3.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all"
                                   placeholder="Nama Anda" value="{{ old('name') }}">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <div class="relative">
                            <i class="ph ph-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input type="email" name="email" required
                                   class="w-full pl-12 pr-4 py-3.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all"
                                   placeholder="nama@email.com" value="{{ old('email') }}">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <i class="ph ph-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                        <input type="password" name="password" required
                               class="w-full pl-12 pr-12 py-3.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all"
                               placeholder="Minimal 6 karakter">
                        <button type="button" onclick="togglePassword('password')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="ph ph-eye text-lg" id="eye-icon-password"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                    <div class="relative">
                        <i class="ph ph-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                        <input type="password" name="password_confirmation" required
                               class="w-full pl-12 pr-12 py-3.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all"
                               placeholder="Ulangi password">
                        <button type="button" onclick="togglePassword('password_confirmation')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="ph ph-eye text-lg" id="eye-icon-password_confirmation"></i>
                        </button>
                    </div>
                </div>

                <input type="hidden" name="role" value="vendor">

                <div class="bg-orange-50 rounded-xl p-4 flex items-start gap-3">
                    <i class="ph ph-info text-orange-500 text-xl mt-0.5"></i>
                    <div class="text-sm text-orange-700">
                        <p class="font-medium">Informasi Akun Vendor</p>
                        <p class="mt-1">Setelah mendaftar, Anda perlu menghubungi admin untuk mengaktifkan akses vendor Anda.</p>
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3.5 rounded-xl transition-all flex items-center justify-center gap-2">
                    <span>Daftar Sekarang</span>
                    <i class="ph ph-user-plus"></i>
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600">Sudah punya akun?
                    <a href="{{ route('auth.login') }}" class="text-orange-500 font-medium hover:text-orange-600">Masuk di sini</a>
                </p>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-200">
                <a href="{{ url('/') }}" class="flex items-center justify-center gap-2 text-gray-600 hover:text-orange-500 transition-colors">
                    <i class="ph ph-house"></i>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldName) {
            const input = document.querySelector(`input[name="${fieldName}"]`);
            const icon = document.getElementById(`eye-icon-${fieldName}`);

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
