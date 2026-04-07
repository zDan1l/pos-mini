<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VendorPanelController extends Controller
{
    protected function getVendor()
    {
        return auth()->user()->vendor;
    }

    public function dashboard()
    {
        $vendor = $this->getVendor();

        $stats = [
            'total_menus' => $vendor->menus()->count(),
            'active_menus' => $vendor->menus()->where('is_available', true)->count(),
            'total_orders' => $vendor->pesanan()->where('status_bayar', 'lunas')->count(),
            'total_revenue' => $vendor->pesanan()->where('status_bayar', 'lunas')->sum('total'),
        ];

        $recentOrders = Pesanan::with('user')
            ->where('idvendor', $vendor->idvendor)
            ->orderBy('timestamp', 'desc')
            ->limit(10)
            ->get();

        return view('vendor.dashboard', compact('stats', 'recentOrders'));
    }

    public function menus()
    {
        $vendor = $this->getVendor();
        $menus = $vendor->menus()->get();

        return view('vendor.menus', compact('menus', 'vendor'));
    }

    public function createMenu()
    {
        $vendor = $this->getVendor();
        return view('vendor.create-menu', compact('vendor'));
    }

    public function storeMenu(Request $request)
    {
        $vendor = $this->getVendor();

        $request->validate([
            'nama_menu' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'path_gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
            'idvendor' => $vendor->idvendor,
            'is_available' => $request->is_available ?? true,
        ]);

        return redirect()->route('vendor.menus')->with('success', 'Menu berhasil ditambahkan');
    }

    public function editMenu($idmenu)
    {
        $vendor = $this->getVendor();
        $menu = $vendor->menus()->where('idmenu', $idmenu)->firstOrFail();

        return view('vendor.edit-menu', compact('vendor', 'menu'));
    }

    public function updateMenu(Request $request, $idmenu)
    {
        $vendor = $this->getVendor();
        $menu = $vendor->menus()->where('idmenu', $idmenu)->firstOrFail();

        $request->validate([
            'nama_menu' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'path_gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'nullable|boolean',
        ]);

        $data = [
            'nama_menu' => $request->nama_menu,
            'harga' => $request->harga,
            'is_available' => $request->is_available ?? true,
        ];

        if ($request->hasFile('path_gambar')) {
            if ($menu->path_gambar) {
                Storage::disk('public')->delete($menu->path_gambar);
            }
            $data['path_gambar'] = $request->file('path_gambar')->store('menu-images', 'public');
        }

        $menu->update($data);

        return redirect()->route('vendor.menus')->with('success', 'Menu berhasil diperbarui');
    }

    public function destroyMenu($idmenu)
    {
        $vendor = $this->getVendor();
        $menu = $vendor->menus()->where('idmenu', $idmenu)->firstOrFail();

        if ($menu->path_gambar) {
            Storage::disk('public')->delete($menu->path_gambar);
        }

        $menu->delete();

        return redirect()->route('vendor.menus')->with('success', 'Menu berhasil dihapus');
    }

    public function orders()
    {
        $vendor = $this->getVendor();
        $orders = Pesanan::with('user')
            ->where('idvendor', $vendor->idvendor)
            ->where('status_bayar', 'lunas')
            ->orderBy('timestamp', 'desc')
            ->get();

        return view('vendor.orders', compact('orders', 'vendor'));
    }

    public function orderDetail($idpesanan)
    {
        $vendor = $this->getVendor();
        $pesanan = Pesanan::with(['user', 'detailPesanan.menu'])
            ->where('idvendor', $vendor->idvendor)
            ->where('idpesanan', $idpesanan)
            ->firstOrFail();

        return view('vendor.order-detail', compact('vendor', 'pesanan'));
    }
}
