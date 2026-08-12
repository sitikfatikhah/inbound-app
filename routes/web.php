<?php

use App\Http\Controllers\ContainerController;
use App\Http\Controllers\CustomsController;
use App\Http\Controllers\CustomsPaymentController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\WarehouseController;
use App\Models\ActivityLog;
use App\Models\CustomsClearances;
use Faker\Provider\Payment;
use Illuminate\Support\Facades\Route;


Route::inertia('/', 'landing/index')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::resource('shipments', ShipmentController::class);

    Route::resource('custom-clearances', CustomsController::class);

    Route::resource('purchase-orders', PurchaseOrderController::class);

    Route::resource('suppliers', SupplierController::class);

    Route::resource('containers', ContainerController::class);

    Route::resource('warehouse', WarehouseController::class);

    Route::resource('payment', CustomsPaymentController::class);

    Route::resource('activity-logs', ActivityLog::class)->only(['index', 'show']);
});


require __DIR__.'/settings.php';
