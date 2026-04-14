<?php

namespace App\Http\Controllers;

use App\Models\DetailPesanan;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\User;
use App\Models\Vendor;
use App\Services\MidtransService;
use App\Services\QRCodeService;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    protected MidtransService $midtransService;

    protected QRCodeService $qrCodeService;

    public function __construct(MidtransService $midtransService, QRCodeService $qrCodeService)
    {
        $this->midtransService = $midtransService;
        $this->qrCodeService = $qrCodeService;
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

        // Check if Midtrans is configured
        if (!$this->midtransService->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'Midtrans belum dikonfigurasi. Silakan hubungi admin.',
            ], 500);
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
                    'price' => (int) $menu->harga,
                    'quantity' => $qty,
                    'name' => $menu->nama_menu,
                ];
            }

            // Generate order ID with format: ORD-YYMMDDXXXX (8 digits: YYMMDD + 4 digit sequence)
            $datePart = now()->format('ymd'); // YYMMDD (6 digits)
            $sequence = str_pad(Pesanan::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT); // 4 digits
            $paymentRef = 'ORD-' . $datePart . $sequence; // e.g., ORD-2404140001

            // Save order first (before Midtrans call)
            $pesanan = Pesanan::create([
                'nama' => 'POS-' . $paymentRef,
                'timestamp' => now(),
                'total' => $total,
                'metode_bayar' => 'midtrans',
                'status_bayar' => 'pending',
                'payment_reference' => $paymentRef,
                'user_id' => $customer->id,
                'idvendor' => $vendorId,
            ]);

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

            // Create Midtrans Snap transaction with finish_url
            $finishUrl = route('customer.order-success', $pesanan->idpesanan);
            $midtransResponse = $this->midtransService->createSnapTransaction([
                'order_id' => $paymentRef,
                'gross_amount' => (int) $total,
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'items' => $itemDetails,
                'finish_url' => $finishUrl,
            ]);

            session()->forget(['cart', 'cart_vendor_id']);

            DB::commit();

            // Return Snap token
            if (isset($midtransResponse['token'])) {
                return response()->json([
                    'success' => true,
                    'snap_token' => $midtransResponse['token'],
                    'redirect_url' => $midtransResponse['redirect_url'] ?? null,
                    'pesanan_id' => $pesanan->idpesanan,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan token pembayaran',
            ], 500);

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
        $paymentType = $request->input('payment_type');
        $fraudStatus = $request->input('fraud_status');

        // Find order by payment_reference
        $pesanan = Pesanan::where('payment_reference', $orderId)->first();

        if (!$pesanan) {
            Log::warning('Order not found: ' . $orderId);
            return response()->json(['status' => 'error'], 404);
        }

        // Map Midtrans payment type to our payment method
        $paymentMethod = $this->mapPaymentType($paymentType);

        // Update order status
        $newStatus = $this->midtransService->mapStatus($transactionStatus);

        DB::beginTransaction();
        try {
            $pesanan->update([
                'status_bayar' => $newStatus,
                'metode_bayar' => $paymentMethod,
            ]);

            Log::info("Order {$pesanan->idpesanan} status updated to: {$newStatus}, payment method: {$paymentMethod}");

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating order: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Map Midtrans payment type to our payment method
     */
    private function mapPaymentType(string $paymentType): string
    {
        return match ($paymentType) {
            'qris' => 'qris',
            'bank_transfer' => 'virtual_account',
            'va', 'bca_va', 'mandiri_bill', 'bni_va', 'bri_va', 'cimb_va', 'permata_va' => 'virtual_account',
            'gopay', 'shopeepay' => 'ewallet',
            default => 'midtrans',
        };
    }

    public function orderSuccess($idpesanan)
    {
        $pesanan = Pesanan::with(['user', 'vendor', 'detailPesanan.menu'])
            ->findOrFail($idpesanan);

        // For sandbox/simulator: always show the page with QR code
        // Check and update status from Midtrans if not already 'lunas'
        if ($pesanan->status_bayar !== 'lunas') {
            try {
                $midtransStatus = $this->midtransService->getTransactionStatus($pesanan->payment_reference);
                $newStatus = $this->midtransService->mapStatus($midtransStatus['transaction_status'] ?? 'pending');
                $paymentType = $midtransStatus['payment_type'] ?? null;

                $updateData = ['status_bayar' => $newStatus];

                if ($paymentType) {
                    $updateData['metode_bayar'] = $this->mapPaymentType($paymentType);
                }

                if ($newStatus === 'lunas' || $paymentType) {
                    $pesanan->update($updateData);
                }

                // Reload to get updated data
                $pesanan = $pesanan->fresh();
            } catch (\Exception $e) {
                Log::error('Error checking Midtrans status: ' . $e->getMessage());
            }
        }

        // Always show the order success page with QR code (for testing/development)
        return view('customer.order-success', compact('pesanan'));
    }

    /**
     * Generate QR Code for order reference (with UTF-8 encoding)
     */
    public function generateQRCode($content)
    {
        // Ensure UTF-8 encoding for QR Code content
        $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');

        $qrCodeData = $this->qrCodeService->generateAsRawPng($content);

        return response($qrCodeData, 200)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'public, max-age=86400');
    }
}
