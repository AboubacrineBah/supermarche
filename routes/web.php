<?php

use App\Http\Controllers\CreditPaymentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ReceiptNotificationController;
use App\Http\Controllers\SalesDashboardController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('home')
        : redirect()->route('login');
});

Auth::routes(['register' => false]);

Route::redirect('/dashboard', '/home')->name('dashboard');

Route::middleware(['auth', 'tenant'])->group(function (): void {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::resource('products', ProductController::class);

    Route::resource('suppliers', SupplierController::class);

    Route::post('purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'storeReceipt'])
        ->name('purchase-orders.receive.store');
    Route::get('purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])
        ->name('purchase-orders.receive');
    Route::resource('purchase-orders', PurchaseOrderController::class)
        ->parameters(['purchase-orders' => 'purchaseOrder']);

    Route::get('stocks', [StockController::class, 'index'])->name('stocks.index');
    Route::get('stocks/{product}/movements', [StockController::class, 'movements'])->name('stocks.movements');
    Route::get('stocks/{product}/adjust', [StockController::class, 'adjustForm'])->name('stocks.adjust');
    Route::post('stocks/{product}/adjust', [StockController::class, 'adjust'])->name('stocks.adjust.store');

    Route::get('pos', [PosController::class, 'index'])->name('pos.index');
    Route::get('pos/open', [PosController::class, 'openForm'])->name('pos.open');
    Route::post('pos/open', [PosController::class, 'open'])->name('pos.open.store');
    Route::get('pos/close/{cashSession}', [PosController::class, 'closeForm'])->name('pos.close');
    Route::post('pos/close/{cashSession}', [PosController::class, 'close'])->name('pos.close.store');
    Route::post('pos/sale', [PosController::class, 'storeSale'])->name('pos.sale.store');
    Route::get('pos/receipt/{sale}', [PosController::class, 'receipt'])->name('pos.receipt');
    Route::get('pos/receipt/{sale}/pdf', [PosController::class, 'receiptPdf'])->name('pos.receipt.pdf');

    Route::get('sales-dashboard', [SalesDashboardController::class, 'index'])->name('sales-dashboard.index');
    Route::post('pos/receipt/{sale}/whatsapp', [ReceiptNotificationController::class, 'sendWhatsApp'])->name('pos.receipt.whatsapp');
    Route::post('pos/receipt/{sale}/sms', [ReceiptNotificationController::class, 'sendSms'])->name('pos.receipt.sms');

    Route::resource('customers', CustomerController::class)->except(['show']);

    Route::get('credit-payments', [CreditPaymentController::class, 'index'])->name('credit-payments.index');
    Route::get('credit-payments/{customer}/create', [CreditPaymentController::class, 'create'])->name('credit-payments.create');
    Route::post('credit-payments/{customer}', [CreditPaymentController::class, 'store'])->name('credit-payments.store');
    Route::post('credit-payments/{customer}/remind', [CreditPaymentController::class, 'remind'])->name('credit-payments.remind');
});
