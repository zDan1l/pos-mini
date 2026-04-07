@extends('layouts.app')

@section('title', 'Kantin Online - Pesan Makanan')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Kantin Online</h1>
            <p class="text-sm text-gray-600 mt-1">Pesan makanan favoritmu dengan mudah</p>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <!-- Search -->
            <div class="relative flex-1">
                <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="search-input" placeholder="Cari menu..."
                       class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
            </div>
            <!-- Cart Button -->
            <a href="{{ route('customer.cart') }}" class="relative bg-orange-500 text-white px-4 py-2.5 rounded-xl hover:bg-orange-600 transition-colors flex items-center gap-2 flex-shrink-0">
                <i class="ph ph-shopping-cart"></i>
                <span>Keranjang</span>
                @if(session('cart'))
                    <span class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white rounded-full text-xs flex items-center justify-center">
                        {{ array_sum(session('cart')) }}
                    </span>
                @endif
            </a>
        </div>
    </div>

    <!-- Vendor Filter Tabs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6 overflow-hidden">
        <div class="flex items-center gap-1 p-1 overflow-x-auto" id="vendor-tabs">
            <button onclick="filterByVendor('all')" data-vendor="all"
                    class="vendor-tab active px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap">
                <i class="ph ph-squares-four mr-1"></i>
                Semua
            </button>
            @foreach($vendors as $vendor)
                <button onclick="filterByVendor({{ $vendor->idvendor }})" data-vendor="{{ $vendor->idvendor }}"
                        class="vendor-tab px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap">
                    <i class="ph ph-storefront mr-1"></i>
                    {{ $vendor->nama_vendor }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Menu List -->
    <div id="menu-container" class="grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($vendors as $vendor)
            @foreach($vendor->menus as $menu)
                <div class="menu-card bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow" data-vendor="{{ $vendor->idvendor }}" data-search="{{ strtolower($menu->nama_menu) }}">
                    @if($menu->path_gambar)
                        <img src="/storage/{{ $menu->path_gambar }}" alt="{{ $menu->nama_menu }}"
                             class="w-full h-40 object-cover">
                    @else
                        <div class="w-full h-40 bg-gradient-to-br from-orange-100 to-orange-50 flex items-center justify-center">
                            <i class="ph ph-bowl-food text-3xl text-orange-300"></i>
                        </div>
                    @endif

                    <div class="p-3">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <p class="font-semibold text-sm text-gray-900 truncate pr-2">{{ $menu->nama_menu }}</p>
                                <p class="text-xs text-orange-500 font-bold">{{ formatRupiah($menu->harga) }}</p>
                            </div>
                            <span class="{{ $menu->is_available ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} px-2 py-0.5 rounded text-xs">
                                {{ $menu->is_available ? 'Tersedia' : 'Habis' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between mt-3">
                            <p class="text-xs text-gray-500">{{ $vendor->nama_vendor }}</p>

                            @if($menu->is_available)
                                <button onclick="addToCart({{ $menu->idmenu }}, {{ $menu->idvendor }}, '{{ $menu->nama_menu }}', {{ $menu->harga }})"
                                        class="text-xs bg-orange-500 hover:bg-orange-600 text-white px-3 py-1.5 rounded-lg transition-colors">
                                    <i class="ph ph-plus"></i> Tambah
                                </button>
                            @else
                                <button disabled class="text-xs bg-gray-200 text-gray-400 px-3 py-1.5 rounded-lg">
                                    Habis
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
        @endforeach
    </div>

    @if($vendors->pluck('menus')->flatten()->filter()->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center text-gray-500">
            <i class="ph ph-bowl-food text-4xl mb-2 block"></i>
            <p class="font-medium">Belum ada menu tersedia</p>
        </div>
    @endif
</div>

<div id="modal-cart" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2 max-w-sm w-full mx-4">
        <h3 class="text-base font-semibold mb-4">Tambah ke Keranjang</h3>
        <div id="modal-content"></div>
    </div>
</div>

@push('scripts')
<script>
    function filterByVendor(vendorId) {
        // Update tab active state
        document.querySelectorAll('.vendor-tab').forEach(tab => {
            tab.classList.remove('active', 'bg-orange-500', 'text-white');
            tab.classList.add('text-gray-700', 'hover:bg-gray-100');
        });

        const activeTab = document.querySelector(`[data-vendor="${vendorId}"]`);
        activeTab.classList.add('active', 'bg-orange-500', 'text-white');
        activeTab.classList.remove('text-gray-700', 'hover:bg-gray-100');

        // Filter menu cards
        document.querySelectorAll('.menu-card').forEach(card => {
            if (vendorId === 'all' || card.dataset.vendor == vendorId) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Search functionality
    document.getElementById('search-input').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        document.querySelectorAll('.menu-card').forEach(card => {
            const menuName = card.dataset.search;
            if (menuName.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });

    function addToCart(idmenu, idvendor, namaMenu, harga) {
        const modalContent = document.getElementById('modal-content');
        modalContent.innerHTML = `
            <p class="mb-2"><strong>${namaMenu}</strong> - {{ formatRupiah(${harga}) }}</p>
            <input type="number" id="jumlah" value="1" min="1" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm mb-3" placeholder="Jumlah">
            <div class="flex gap-2">
                <button onclick="submitAddToCart(${idmenu}, ${idvendor})" class="flex-1 bg-orange-500 text-white py-2 px-4 rounded-lg hover:bg-orange-600 text-sm">
                    Tambah
                </button>
                <button onclick="closeModal()" class="flex-1 bg-gray-200 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-300 text-sm">
                    Batal
                </button>
            </div>
        `;
        document.getElementById('modal-cart').classList.remove('hidden');
    }

    function submitAddToCart(idmenu, idvendor) {
        const jumlah = parseInt(document.getElementById('jumlah').value);
        if (jumlah < 1) {
            alert('Jumlah minimal 1');
            return;
        }

        $.ajax({
            url: '{{ route('customer.add-to-cart') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                idmenu: idmenu,
                idvendor: idvendor,
                jumlah: jumlah
            },
            success: function(response) {
                if (response.success) {
                    updateCartBadge(response.cart_count);
                    closeModal();
                    showToast(response.message);
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Terjadi kesalahan');
            }
        });
    }

    function closeModal() {
        document.getElementById('modal-cart').classList.add('hidden');
    }

    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 text-sm';
        toast.innerHTML = `<i class="ph ph-check-circle mr-1"></i>${message}`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    // Cart modal click outside to close
    document.getElementById('modal-cart').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
@endpush
@endsection
