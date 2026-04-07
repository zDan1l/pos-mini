<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::all();
        return view('vendor.index', compact('vendors'));
    }

    public function create()
    {
        return view('vendor.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_vendor' => 'required|string|max:255',
            'kode_vendor' => 'required|string|unique:vendors,kode_vendor|max:50',
        ]);

        Vendor::create([
            'nama_vendor' => $request->nama_vendor,
            'kode_vendor' => strtoupper($request->kode_vendor),
        ]);

        return redirect()->route('vendor.index')->with('success', 'Vendor berhasil ditambahkan');
    }

    public function edit($idvendor)
    {
        $vendor = Vendor::findOrFail($idvendor);
        return view('vendor.edit', compact('vendor'));
    }

    public function update(Request $request, $idvendor)
    {
        $vendor = Vendor::findOrFail($idvendor);

        $request->validate([
            'nama_vendor' => 'required|string|max:255',
            'kode_vendor' => 'required|string|unique:vendors,kode_vendor,' . $vendor->idvendor . ',idvendor|max:50',
        ]);

        $vendor->update([
            'nama_vendor' => $request->nama_vendor,
            'kode_vendor' => strtoupper($request->kode_vendor),
        ]);

        return redirect()->route('vendor.index')->with('success', 'Vendor berhasil diperbarui');
    }

    public function destroy($idvendor)
    {
        $vendor = Vendor::findOrFail($idvendor);
        $vendor->delete();

        return redirect()->route('vendor.index')->with('success', 'Vendor berhasil dihapus');
    }

    public function menus($idvendor)
    {
        $vendor = Vendor::with('menus')->findOrFail($idvendor);
        return view('vendor.menus', compact('vendor'));
    }

    public function createMenu($idvendor)
    {
        $vendor = Vendor::findOrFail($idvendor);
        return view('vendor.create-menu', compact('vendor'));
    }

    public function storeMenu(Request $request, $idvendor)
    {
        $vendor = Vendor::findOrFail($idvendor);

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

        return redirect()->route('vendor.menus', $vendor->idvendor)
            ->with('success', 'Menu berhasil ditambahkan');
    }

    public function editMenu($idvendor, $idmenu)
    {
        $vendor = Vendor::findOrFail($idvendor);
        $menu = Menu::where('idvendor', $idvendor)->where('idmenu', $idmenu)->firstOrFail();
        return view('vendor.edit-menu', compact('vendor', 'menu'));
    }

    public function updateMenu(Request $request, $idvendor, $idmenu)
    {
        $vendor = Vendor::findOrFail($idvendor);
        $menu = Menu::where('idvendor', $idvendor)->where('idmenu', $idmenu)->firstOrFail();

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

        return redirect()->route('vendor.menus', $vendor->idvendor)
            ->with('success', 'Menu berhasil diperbarui');
    }

    public function destroyMenu($idvendor, $idmenu)
    {
        $vendor = Vendor::findOrFail($idvendor);
        $menu = Menu::where('idvendor', $idvendor)->where('idmenu', $idmenu)->firstOrFail();

        if ($menu->path_gambar) {
            Storage::disk('public')->delete($menu->path_gambar);
        }

        $menu->delete();

        return redirect()->route('vendor.menus', $vendor->idvendor)
            ->with('success', 'Menu berhasil dihapus');
    }

    public function orders($idvendor)
    {
        $vendor = Vendor::findOrFail($idvendor);
        $orders = Pesanan::with(['customer', 'detailPesanan.menu'])
            ->where('idvendor', $idvendor)
            ->where('status_bayar', 'lunas')
            ->orderBy('timestamp', 'desc')
            ->get();

        return view('vendor.orders', compact('vendor', 'orders'));
    }

    public function orderDetail($idvendor, $idpesanan)
    {
        $vendor = Vendor::findOrFail($idvendor);
        $pesanan = Pesanan::with(['customer', 'detailPesanan.menu', 'vendor'])
            ->where('idvendor', $idvendor)
            ->where('idpesanan', $idpesanan)
            ->firstOrFail();

        return view('vendor.order-detail', compact('vendor', 'pesanan'));
    }
}
