<?php

use App\Http\Controllers\ContainerController;
use App\Http\Controllers\CustomsController;
use App\Http\Controllers\CustomsPaymentController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\WarehouseController;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::inertia('/', 'Welcome')->name('home');

Route::get('/search/bl_number', function () {
    return Inertia::render('search/bl_number', [
        'reference' => request('reference'),
    ]);
})->name('search.bl_number');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::resource('custom-clearances', CustomsController::class);

    Route::resource('purchase-orders', PurchaseOrderController::class);

    Route::resource('suppliers', SupplierController::class);

    Route::resource('containers', ContainerController::class);

    Route::resource('warehouse', WarehouseController::class);

    Route::resource('payment', CustomsPaymentController::class);

    Route::resource('activity-logs', ActivityLog::class)->only(['index', 'show']);
});

Route::prefix('shipments')
    ->name('shipments.')
    ->controller(ShipmentController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::get('/{shipment}', 'show')
            ->whereNumber('shipment')
            ->name('show');
        Route::get('/{shipment}/edit', 'edit')
            ->whereNumber('shipment')
            ->name('edit');
        Route::post('/', 'store')->name('store');
        Route::put('/{shipment}', 'update')->name('update');
        Route::delete('/{shipment}', 'destroy')->name('destroy');
    });

require __DIR__.'/settings.php';
