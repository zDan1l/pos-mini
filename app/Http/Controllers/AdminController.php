<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_vendors' => Vendor::count(),
            'total_menus' => Menu::count(),
            'total_orders' => Pesanan::where('status_bayar', 'lunas')->count(),
            'total_revenue' => Pesanan::where('status_bayar', 'lunas')->sum('total'),
        ];

        $recentOrders = Pesanan::with(['user', 'vendor'])
            ->orderBy('timestamp', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders'));
    }

    // Vendors Management
    public function vendors()
    {
        $vendors = Vendor::with('user')->get();
        return view('admin.vendors', compact('vendors'));
    }

    public function createVendor()
    {
        return view('admin.create-vendor');
    }

    public function storeVendor(Request $request)
    {
        $request->validate([
            'nama_vendor' => 'required|string|max:255',
            'kode_vendor' => 'required|string|unique:vendors,kode_vendor|max:50',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $vendor = Vendor::create([
            'nama_vendor' => $request->nama_vendor,
            'kode_vendor' => strtoupper($request->kode_vendor),
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'vendor',
            'idvendor' => $vendor->idvendor,
        ]);

        return redirect()->route('admin.vendors')->with('success', 'Vendor berhasil ditambahkan');
    }

    public function editVendor($idvendor)
    {
        $vendor = Vendor::with('user')->findOrFail($idvendor);
        return view('admin.edit-vendor', compact('vendor'));
    }

    public function updateVendor(Request $request, $idvendor)
    {
        $vendor = Vendor::with('user')->findOrFail($idvendor);

        $request->validate([
            'nama_vendor' => 'required|string|max:255',
            'kode_vendor' => 'required|string|unique:vendors,kode_vendor,' . $vendor->idvendor . ',idvendor|max:50',
        ]);

        $vendor->update([
            'nama_vendor' => $request->nama_vendor,
            'kode_vendor' => strtoupper($request->kode_vendor),
        ]);

        // If vendor has user, update user data
        if ($vendor->user) {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $vendor->user->id,
            ]);

            $vendor->user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            if ($request->filled('password')) {
                $vendor->user->update([
                    'password' => Hash::make($request->password),
                ]);
            }
        }

        return redirect()->route('admin.vendors')->with('success', 'Vendor berhasil diperbarui');
    }

    public function createVendorAccount($idvendor)
    {
        $vendor = Vendor::findOrFail($idvendor);
        return view('admin.create-vendor-account', compact('vendor'));
    }

    public function storeVendorAccount(Request $request, $idvendor)
    {
        $vendor = Vendor::findOrFail($idvendor);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'vendor',
            'idvendor' => $vendor->idvendor,
        ]);

        return redirect()->route('admin.vendors')->with('success', 'Akun vendor berhasil dibuat');
    }

    public function destroyVendor($idvendor)
    {
        $vendor = Vendor::findOrFail($idvendor);

        if ($vendor->user) {
            $vendor->user->update(['idvendor' => null]);
        }

        $vendor->delete();

        return redirect()->route('admin.vendors')->with('success', 'Vendor berhasil dihapus');
    }

    // Menus Management
    public function menus()
    {
        $menus = Menu::with('vendor')->get();
        $vendors = Vendor::all();
        return view('admin.menus', compact('menus', 'vendors'));
    }

    public function createMenu()
    {
        $vendors = Vendor::all();
        return view('admin.create-menu', compact('vendors'));
    }

    public function storeMenu(Request $request)
    {
        $request->validate([
            'nama_menu' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'path_gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'idvendor' => 'required|exists:vendors,idvendor',
            'is_available' => 'nullable|boolean',
        ]);

        $imagePath = null;
        if ($request->hasFile('path_gambar')) {
            $imagePath = $request->file('path_gambar')->store('menu-images', 'public');
        }

        Menu::create([
            'nama_menu' => $request->nama_menu,
            'harga' => $request->harga,
            'path_gambar' => $imagePath,
            'idvendor' => $request->idvendor,
            'is_available' => $request->is_available ?? true,
        ]);

        return redirect()->route('admin.menus')->with('success', 'Menu berhasil ditambahkan');
    }

    public function editMenu($idmenu)
    {
        $menu = Menu::findOrFail($idmenu);
        $vendors = Vendor::all();
        return view('admin.edit-menu', compact('menu', 'vendors'));
    }

    public function updateMenu(Request $request, $idmenu)
    {
        $menu = Menu::findOrFail($idmenu);

        $request->validate([
            'nama_menu' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'path_gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'idvendor' => 'required|exists:vendors,idvendor',
            'is_available' => 'nullable|boolean',
        ]);

        $data = [
            'nama_menu' => $request->nama_menu,
            'harga' => $request->harga,
            'idvendor' => $request->idvendor,
            'is_available' => $request->is_available ?? true,
        ];

        if ($request->hasFile('path_gambar')) {
            if ($menu->path_gambar) {
                Storage::disk('public')->delete($menu->path_gambar);
            }
            $data['path_gambar'] = $request->file('path_gambar')->store('menu-images', 'public');
        }

        $menu->update($data);

        return redirect()->route('admin.menus')->with('success', 'Menu berhasil diperbarui');
    }

    public function destroyMenu($idmenu)
    {
        $menu = Menu::findOrFail($idmenu);

        if ($menu->path_gambar) {
            Storage::disk('public')->delete($menu->path_gambar);
        }

        $menu->delete();

        return redirect()->route('admin.menus')->with('success', 'Menu berhasil dihapus');
    }

    // Orders
    public function orders()
    {
        $orders = Pesanan::with(['user', 'vendor'])
            ->orderBy('timestamp', 'desc')
            ->get();

        return view('admin.orders', compact('orders'));
    }

    public function orderDetail($idpesanan)
    {
        $pesanan = Pesanan::with(['user', 'vendor', 'detailPesanan.menu'])
            ->findOrFail($idpesanan);

        return view('admin.order-detail', compact('pesanan'));
    }
}
