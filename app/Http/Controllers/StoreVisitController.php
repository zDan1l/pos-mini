<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\StoreVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreVisitController extends Controller
{
    const THRESHOLD_METERS = 100;

    public function index()
    {
        $stores = Store::orderBy('nama_toko')->get();
        return view('vendor.store-visit.index', compact('stores'));
    }

    public function visit()
    {
        return view('vendor.store-visit.visit');
    }

    public function lookupStore($barcode)
    {
        $store = Store::where('barcode', $barcode)->first();

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => "Barcode \"$barcode\" tidak valid. Toko tidak ditemukan.",
                'barcode' => $barcode,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'idtoko' => $store->idtoko,
                'barcode' => $store->barcode,
                'nama_toko' => $store->nama_toko,
                'alamat' => $store->alamat,
                'latitude' => $store->latitude,
                'longitude' => $store->longitude,
                'accuracy' => $store->accuracy,
            ],
        ]);
    }

    public function processVisit(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'required|numeric|min:0',
        ]);

        $store = Store::where('barcode', $request->barcode)->first();

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => "Barcode tidak valid. Toko tidak ditemukan di database.",
            ], 404);
        }

        $distance = $this->calculateDistance(
            $store->latitude,
            $store->longitude,
            $request->latitude,
            $request->longitude
        );

        $thresholdEffective = self::THRESHOLD_METERS + $store->accuracy + $request->accuracy;
        $status = $distance <= $thresholdEffective ? 'diterima' : 'ditolak';

        $visit = StoreVisit::create([
            'idtoko' => $store->idtoko,
            'iduser' => auth()->id(),
            'visit_latitude' => $request->latitude,
            'visit_longitude' => $request->longitude,
            'visit_accuracy' => $request->accuracy,
            'distance_from_store' => $distance,
            'status' => $status,
            'visited_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'nama_toko' => $store->nama_toko,
                'alamat' => $store->alamat,
                'distance' => round($distance, 2),
                'threshold' => self::THRESHOLD_METERS,
                'threshold_effective' => round($thresholdEffective, 2),
                'store_accuracy' => $store->accuracy,
                'visit_accuracy' => $request->accuracy,
                'status' => $status,
                'visited_at' => $visit->visited_at->format('d M Y H:i:s'),
            ],
        ]);
    }

    public function history()
    {
        $visits = StoreVisit::with(['store', 'user'])
            ->where('iduser', auth()->id())
            ->orderBy('visited_at', 'desc')
            ->paginate(20);

        return view('vendor.store-visit.history', compact('visits'));
    }

    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $R = 6371000;
        $dLat = ($lat2 - $lat1) * M_PI / 180;
        $dLng = ($lng2 - $lng1) * M_PI / 180;

        $a = sin($dLat / 2) ** 2 +
             cos($lat1 * M_PI / 180) * cos($lat2 * M_PI / 180) *
             sin($dLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $R * $c;
    }
}
