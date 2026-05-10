<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::orderBy('created_at', 'desc')->get();
        return view('admin.stores.index', compact('stores'));
    }

    public function create()
    {
        return view('admin.stores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_toko' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'required|numeric|min:0',
        ]);

        $barcode = 'TKO-' . strtoupper(Str::random(8));

        Store::create([
            'barcode' => $barcode,
            'nama_toko' => $request->nama_toko,
            'alamat' => $request->alamat,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy' => $request->accuracy,
        ]);

        return redirect()->route('admin.stores.index')->with('success', 'Toko berhasil ditambahkan');
    }

    public function edit($idtoko)
    {
        $store = Store::findOrFail($idtoko);
        return view('admin.stores.edit', compact('store'));
    }

    public function update(Request $request, $idtoko)
    {
        $store = Store::findOrFail($idtoko);

        $request->validate([
            'nama_toko' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'required|numeric|min:0',
        ]);

        $store->update([
            'nama_toko' => $request->nama_toko,
            'alamat' => $request->alamat,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy' => $request->accuracy,
        ]);

        return redirect()->route('admin.stores.index')->with('success', 'Toko berhasil diperbarui');
    }

    public function destroy($idtoko)
    {
        $store = Store::findOrFail($idtoko);
        $store->delete();

        return redirect()->route('admin.stores.index')->with('success', 'Toko berhasil dihapus');
    }

    public function generateBarcode($idtoko)
    {
        $store = Store::findOrFail($idtoko);
        return redirect()->route('customer.qrcode', ['content' => $store->barcode]);
    }
}
