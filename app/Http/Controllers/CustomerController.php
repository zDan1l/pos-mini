<?php

namespace App\Http\Controllers;

use App\Models\DetailPesanan;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\User;
use App\Models\Vendor;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    protected MidtransService $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function index()
    {
        $vendors = Vendor::with('menus')->get();
        return view('customer.index', compact('vendors'));
    }

    public function menuByVendor($idvendor)
    {
        $vendor = Vendor::with('menus')->findOrFail($idvendor);
        return response()->json($vendor->menus);
    }

    public function cart()
    {
        $vendorId = session('cart_vendor_id');
        $cartItems = session('cart', []);

        if (!$vendorId || empty($cartItems)) {
            return view('customer.cart', [
                'cartItems' => [],
                'vendor' => null,
                'total' => 0,
            ]);
        }

        $vendor = Vendor::find($vendorId);
        $menuIds = array_keys($cartItems);
        $menus = Menu::whereIn('idmenu', $menuIds)->get();

        $cartItemsWithDetails = [];
        $total = 0;

        foreach ($menus as $menu) {
            $qty = $cartItems[$menu->idmenu];
            $subtotal = $menu->harga * $qty;
            $total += $subtotal;

            $cartItemsWithDetails[] = [
                'idmenu' => $menu->idmenu,
                'nama_menu' => $menu->nama_menu,
                'harga' => $menu->harga,
                'qty' => $qty,
                'subtotal' => $subtotal,
                'path_gambar' => $menu->path_gambar,
            ];
        }

        return view('customer.cart', [
            'cartItems' => $cartItemsWithDetails,
            'vendor' => $vendor,
            'total' => $total,
        ]);
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'idmenu' => 'required|exists:menus,idmenu',
            'idvendor' => 'required|exists:vendors,idvendor',
            'jumlah' => 'required|integer|min:1',
        ]);

        $menuId = $request->idmenu;
        $vendorId = $request->idvendor;
        $jumlah = $request->jumlah;

        $currentVendorId = session('cart_vendor_id');

        if ($currentVendorId && $currentVendorId != $vendorId) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat memesan dari vendor berbeda. Silakan selesaikan pesanan saat ini terlebih dahulu.',
            ], 400);
        }

        $cart = session('cart', []);
        $cart[$menuId] = ($cart[$menuId] ?? 0) + $jumlah;

        session(['cart' => $cart]);
        session(['cart_vendor_id' => $vendorId]);

        return response()->json([
            'success' => true,
            'message' => 'Menu ditambahkan ke keranjang',
            'cart_count' => array_sum($cart),
        ]);
    }

    public function updateCart(Request $request)
    {
        $request->validate([
            'idmenu' => 'required|exists:menus,idmenu',
            'jumlah' => 'required|integer|min:0',
        ]);

        $cart = session('cart', []);
        $menuId = $request->idmenu;
        $jumlah = $request->jumlah;

        if ($jumlah == 0) {
            unset($cart[$menuId]);
        } else {
            $cart[$menuId] = $jumlah;
        }

        session(['cart' => $cart]);

        if (empty($cart)) {
            session()->forget(['cart', 'cart_vendor_id']);
        }

        return response()->json([
            'success' => true,
            'cart_count' => array_sum($cart),
        ]);
    }

    public function clearCart()
    {
        session()->forget(['cart', 'cart_vendor_id']);
        return response()->json(['success' => true]);
    }

    public function checkout()
    {
        $vendorId = session('cart_vendor_id');
        $cartItems = session('cart', []);

        if (!$vendorId || empty($cartItems)) {
            return redirect()->route('customer.index')->with('error', 'Keranjang belanja kosong');
        }

        $vendor = Vendor::find($vendorId);
        $menuIds = array_keys($cartItems);
        $menus = Menu::whereIn('idmenu', $menuIds)->get();

        $cartItemsWithDetails = [];
        $total = 0;

        foreach ($menus as $menu) {
            $qty = $cartItems[$menu->idmenu];
            $subtotal = $menu->harga * $qty;
            $total += $subtotal;

            $cartItemsWithDetails[] = [
                'idmenu' => $menu->idmenu,
                'nama_menu' => $menu->nama_menu,
                'harga' => $menu->harga,
                'qty' => $qty,
                'subtotal' => $subtotal,
                'path_gambar' => $menu->path_gambar,
            ];
        }

        return view('customer.checkout', [
            'cartItems' => $cartItemsWithDetails,
            'vendor' => $vendor,
            'total' => $total,
        ]);
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'metode_bayar' => 'required|in:qris,bank_transfer',
            'customer_name' => 'nullable|string|max:255',
        ]);

        $vendorId = session('cart_vendor_id');
        $cartItems = session('cart', []);

        if (!$vendorId || empty($cartItems)) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang belanja kosong',
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Create user/customer with optional name or auto-generate Guest
            $customerName = $request->filled('customer_name')
                ? trim($request->customer_name)
                : null;

            $customer = User::createGuest($customerName);

            $vendor = Vendor::find($vendorId);
            $menuIds = array_keys($cartItems);
            $menus = Menu::whereIn('idmenu', $menuIds)->get();

            $total = 0;
            $itemDetails = [];

            foreach ($menus as $menu) {
                $qty = $cartItems[$menu->idmenu];
                $subtotal = $menu->harga * $qty;
                $total += $subtotal;

                $itemDetails[] = [
                    'id' => $menu->idmenu,
                    'price' => $menu->harga,
                    'quantity' => $qty,
                    'name' => $menu->nama_menu,
                ];
            }

            $paymentRef = 'ORDER-' . strtoupper(Str::random(12));

            // Determine Midtrans payment type
            $midtransPaymentType = 'qris';
            if ($request->metode_bayar === 'bank_transfer') {
                $midtransPaymentType = 'bank_transfer';
            }

            // Create Midtrans transaction
            $midtransResponse = $this->midtransService->createTransaction([
                'order_id' => $paymentRef,
                'gross_amount' => (int) $total,
                'payment_type' => $midtransPaymentType,
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'items' => $itemDetails,
            ]);

            // Save order
            $pesanan = Pesanan::create([
                'nama' => 'Pesanan-' . $customer->name,
                'timestamp' => now(),
                'total' => $total,
                'metode_bayar' => $request->metode_bayar,
                'status_bayar' => 'pending',
                'payment_reference' => $paymentRef,
                'user_id' => $customer->id,
                'idvendor' => $vendorId,
            ]);

            // Update order_id for Midtrans
            $pesanan->update(['nama' => 'POS-' . $pesanan->idpesanan]);

            foreach ($menus as $menu) {
                $qty = $cartItems[$menu->idmenu];
                $subtotal = $menu->harga * $qty;

                DetailPesanan::create([
                    'idmenu' => $menu->idmenu,
                    'idpesanan' => $pesanan->idpesanan,
                    'jumlah' => $qty,
                    'harga' => $menu->harga,
                    'subtotal' => $subtotal,
                    'timestamp' => now(),
                    'catatan' => null,
                ]);
            }

            session()->forget(['cart', 'cart_vendor_id']);

            DB::commit();

            // Return Midtrans redirect URL
            if (isset($midtransResponse['redirect_url'])) {
                return response()->json([
                    'success' => true,
                    'redirect_url' => $midtransResponse['redirect_url'],
                ]);
            }

            // Fallback if no redirect URL
            return response()->json([
                'success' => true,
                'redirect_url' => route('customer.payment', $pesanan->idpesanan),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function payment($idpesanan)
    {
        $pesanan = Pesanan::with(['user', 'vendor', 'detailPesanan.menu'])
            ->findOrFail($idpesanan);

        // Check if already paid
        if ($pesanan->status_bayar === 'lunas') {
            return redirect()->route('customer.order-success', $pesanan->idpesanan);
        }

        return view('customer.payment', compact('pesanan'));
    }

    /**
     * Midtrans notification handler
     */
    public function midtransCallback(Request $request)
    {
        Log::info('Midtrans callback received', $request->all());

        // Verify signature
        if (!$this->midtransService->verifySignature($request->all())) {
            Log::warning('Invalid Midtrans signature');
            return response()->json(['status' => 'error'], 403);
        }

        $orderId = $request->input('order_id');
        $transactionStatus = $request->input('transaction_status');
        $fraudStatus = $request->input('fraud_status');

        // Find order by payment_reference
        $pesanan = Pesanan::where('payment_reference', $orderId)->first();

        if (!$pesanan) {
            Log::warning('Order not found: ' . $orderId);
            return response()->json(['status' => 'error'], 404);
        }

        // Update order status
        $newStatus = $this->midtransService->mapStatus($transactionStatus);

        DB::beginTransaction();
        try {
            $pesanan->update([
                'status_bayar' => $newStatus,
            ]);

            Log::info("Order {$pesanan->idpesanan} status updated to: {$newStatus}");

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating order: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }

        return response()->json(['status' => 'ok']);
    }

    public function orderSuccess($idpesanan)
    {
        $pesanan = Pesanan::with(['user', 'vendor', 'detailPesanan.menu'])
            ->findOrFail($idpesanan);

        if ($pesanan->status_bayar !== 'lunas') {
            // Check Midtrans status
            try {
                $midtransStatus = $this->midtransService->getTransactionStatus($pesanan->payment_reference);
                $newStatus = $this->midtransService->mapStatus($midtransStatus['transaction_status'] ?? 'pending');

                if ($newStatus === 'lunas') {
                    $pesanan->update(['status_bayar' => 'lunas']);
                }
            } catch (\Exception $e) {
                Log::error('Error checking Midtrans status: ' . $e->getMessage());
            }

            if ($pesanan->status_bayar !== 'lunas') {
                return redirect()->route('customer.index')->with('error', 'Pesanan belum lunas');
            }
        }

        return view('customer.order-success', compact('pesanan'));
    }
}
