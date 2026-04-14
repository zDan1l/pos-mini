<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerManagementController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\VendorPanelController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

// Customer Routes (No login required)
Route::get('/', [CustomerController::class, 'index'])->name('customer.index');
Route::get('/customer/menu/{idvendor}', [CustomerController::class, 'menuByVendor'])->name('customer.menu-by-vendor');
Route::get('/cart', [CustomerController::class, 'cart'])->name('customer.cart');
Route::post('/cart/add', [CustomerController::class, 'addToCart'])->name('customer.add-to-cart');
Route::post('/cart/update', [CustomerController::class, 'updateCart'])->name('customer.update-cart');
Route::post('/cart/clear', [CustomerController::class, 'clearCart'])->name('customer.clear-cart');
Route::get('/checkout', [CustomerController::class, 'checkout'])->name('customer.checkout');
Route::post('/payment/process', [CustomerController::class, 'processPayment'])->name('customer.process-payment');
Route::get('/payment/{idpesanan}', [CustomerController::class, 'payment'])->name('customer.payment');
Route::get('/order/success/{idpesanan}', [CustomerController::class, 'orderSuccess'])->name('customer.order-success');
Route::get('/qrcode/{content}', [CustomerController::class, 'generateQRCode'])->name('customer.qrcode');

// Midtrans Callback Route (no CSRF, no auth)
Route::post('/midtrans/callback', [CustomerController::class, 'midtransCallback'])
    ->name('midtrans.callback')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Customer Management Routes (Admin only, requires auth)
Route::middleware(['auth', 'admin'])->prefix('customer-management')->name('customer-management.')->group(function () {
    Route::get('/', [CustomerManagementController::class, 'index'])->name('index');
    Route::get('/create-blob', [CustomerManagementController::class, 'createBlob'])->name('create-blob');
    Route::post('/store-blob', [CustomerManagementController::class, 'storeBlob'])->name('store-blob');
    Route::get('/create-file', [CustomerManagementController::class, 'createFile'])->name('create-file');
    Route::post('/store-file', [CustomerManagementController::class, 'storeFile'])->name('store-file');
    Route::get('/photo/{id}', [CustomerManagementController::class, 'getPhoto'])->name('photo');
    Route::get('/{id}/edit', [CustomerManagementController::class, 'edit'])->name('edit');
    Route::put('/{id}', [CustomerManagementController::class, 'update'])->name('update');
    Route::delete('/{id}', [CustomerManagementController::class, 'destroy'])->name('destroy');
});

// Admin Routes (Admin only, requires auth)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/vendors', [AdminController::class, 'vendors'])->name('vendors');
    Route::get('/vendors/create', [AdminController::class, 'createVendor'])->name('create-vendor');
    Route::post('/vendors', [AdminController::class, 'storeVendor'])->name('store-vendor');
    Route::get('/vendors/{idvendor}/edit', [AdminController::class, 'editVendor'])->name('edit-vendor');
    Route::put('/vendors/{idvendor}', [AdminController::class, 'updateVendor'])->name('update-vendor');
    Route::delete('/vendors/{idvendor}', [AdminController::class, 'destroyVendor'])->name('destroy-vendor');
    Route::get('/vendors/{idvendor}/account/create', [AdminController::class, 'createVendorAccount'])->name('create-vendor-account');
    Route::post('/vendors/{idvendor}/account', [AdminController::class, 'storeVendorAccount'])->name('store-vendor-account');

    Route::get('/menus', [AdminController::class, 'menus'])->name('menus');
    Route::get('/menus/create', [AdminController::class, 'createMenu'])->name('create-menu');
    Route::post('/menus', [AdminController::class, 'storeMenu'])->name('store-menu');
    Route::get('/menus/{idmenu}/edit', [AdminController::class, 'editMenu'])->name('edit-menu');
    Route::put('/menus/{idmenu}', [AdminController::class, 'updateMenu'])->name('update-menu');
    Route::delete('/menus/{idmenu}', [AdminController::class, 'destroyMenu'])->name('destroy-menu');

    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/orders/{idpesanan}', [AdminController::class, 'orderDetail'])->name('order-detail');
});

// Vendor Panel Routes (Vendor only, requires auth)
Route::middleware(['auth', 'vendor'])->prefix('vendor-panel')->name('vendor.')->group(function () {
    Route::get('/dashboard', [VendorPanelController::class, 'dashboard'])->name('dashboard');
    Route::get('/menus', [VendorPanelController::class, 'menus'])->name('menus');
    Route::get('/menus/create', [VendorPanelController::class, 'createMenu'])->name('create-menu');
    Route::post('/menus', [VendorPanelController::class, 'storeMenu'])->name('store-menu');
    Route::get('/menus/{idmenu}/edit', [VendorPanelController::class, 'editMenu'])->name('edit-menu');
    Route::put('/menus/{idmenu}', [VendorPanelController::class, 'updateMenu'])->name('update-menu');
    Route::delete('/menus/{idmenu}', [VendorPanelController::class, 'destroyMenu'])->name('destroy-menu');

    Route::get('/orders', [VendorPanelController::class, 'orders'])->name('orders');
    Route::get('/orders/{idpesanan}', [VendorPanelController::class, 'orderDetail'])->name('order-detail');
});
