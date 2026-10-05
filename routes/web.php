<?php

use App\Http\Controllers\ContainerController;
use App\Http\Controllers\CustomsController;
use App\Http\Controllers\CustomsPaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForwarderController;
use App\Http\Controllers\PortController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\ShippingLineController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::inertia('/', 'Welcome')->name('home'); //
Route::inertia('/landing/login', 'landing/login')->name('landing-login');
Route::inertia('/arrival-schedule', 'tracking/schedule')->name('arrival-schedule');
Route::inertia('/arrival-warehouse', 'tracking/warehouse')->name('warehouse');
Route::get('/project', [TrackingController::class, 'project'])->name('project-monitoring');
Route::get('/knowledge-mgm', [TrackingController::class, 'knowledge'])->name('knowledge-mgm');
Route::get('/analytics', [TrackingController::class, 'analytics'])->name('analytics');

Route::prefix('tracking')
    ->name('tracking.')
    ->controller(TrackingController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index'); // harusnya tanpa sidebar !!
        Route::get('/import', 'import')->name('import');
        Route::get('/local', 'local')->name('local');
        Route::get('/schedule', 'schedule')->name('schedule');
        Route::get('/warehouse', 'warehouse')->name('warehouse');
        Route::get('/project', 'project')->name('project');
    });

Route::get('/search/bl_number', function () {
    return Inertia::render('search/bl_number', [
        'reference' => request('reference'),
    ]);
})->name('search.bl_number');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::resource('custom-clearances', CustomsController::class);
    Route::resource('containers', ContainerController::class);
    Route::resource('payment', CustomsPaymentController::class);
    Route::resource('activity-logs', ActivityLog::class)->only(['index', 'show']);
});

Route::prefix('users')
    ->name('Users.')
    ->controller(UserController::class)
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::get('/{user}', 'show')
            ->whereNumber('user')
            ->name('users');
        Route::get('/{user}/edit', 'edit')
            ->whereNumber('user')
            ->name('edit');
        Route::post('/', 'store')->name('store');
        Route::put('/{user}', 'update')->name('update');
        Route::delete('/{user}', 'destroy')->name('destroy');
    });

Route::prefix('shipments')
    ->name('shipments.')
    ->controller(ShipmentController::class)
    ->middleware(['auth', 'verified'])
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

Route::prefix('forwarders')
    ->name('forwarders.')
    ->controller(ForwarderController::class)
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::get('/{forwarder}', 'show')
            ->whereNumber('forwarder')
            ->name('show');
        Route::get('/{forwarder}/edit', 'edit')
            ->whereNumber('forwarder')
            ->name('edit');
        Route::post('/', 'store')->name('store');
        Route::put('/{forwarder}', 'update')->name('update');
        Route::delete('/{forwarder}', 'destroy')->name('destroy');
    });

Route::prefix('ports')
    ->name('ports.')
    ->controller(PortController::class)
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::get('/{port}', 'show')
            ->whereNumber('port')
            ->name('show');
        Route::get('/{port}/edit', 'edit')
            ->whereNumber('port')
            ->name('edit');
        Route::post('/', 'store')->name('store');
        Route::put('/{port}', 'update')->name('update');
        Route::delete('/{port}', 'destroy')->name('destroy');
    });

Route::prefix('warehouses')
    ->name('warehouses.')
    ->controller(WarehouseController::class)
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::get('/{warehouse}', 'show')
            ->whereNumber('warehouse')
            ->name('show');
        Route::get('/{warehouse}/edit', 'edit')
            ->whereNumber('warehouse')
            ->name('edit');
        Route::post('/', 'store')->name('store');
        Route::put('/{warehouse}', 'update')->name('update');
        Route::delete('/{warehouse}', 'destroy')->name('destroy');
    });

Route::prefix('shippingLine')
    ->name('shipping_line.')
    ->controller(ShippingLineController::class)
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::get('/{shippingLine}', 'show')
            ->whereNumber('shippingLine')
            ->name('show');
        Route::get('/{shippingLine}/edit', 'edit')
            ->whereNumber('shippingLine')
            ->name('edit');
        Route::post('/', 'store')->name('store');
        Route::put('/{shippingLine}', 'update')->name('update');
        Route::delete('/{shippingLine}', 'destroy')->name('destroy');
    });

Route::prefix('po')
    ->name('po.')
    ->controller(PurchaseOrderController::class)
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::get('/{purchaseOrder}', 'show')
            ->whereNumber('purchaseOrder')
            ->name('show');
        Route::get('/{purchaseOrder}/edit', 'edit')
            ->whereNumber('purchaseOrder')
            ->name('edit');
        Route::post('/', 'store')->name('store');
        Route::put('/{purchaseOrder}', 'update')->name('update');
        Route::delete('/{purchaseOrder}', 'destroy')->name('destroy');
    });

Route::prefix('products')
    ->name('products.')
    ->controller(ProductController::class)
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::get('/{products}', 'show')
            ->whereNumber('products')
            ->name('show');
        Route::get('/{products}/edit', 'edit')
            ->whereNumber('products')
            ->name('edit');
        Route::post('/', 'store')->name('store');
        Route::put('/{products}', 'update')->name('update');
        Route::delete('/{products}', 'destroy')->name('destroy');
    });

Route::prefix('suppliers')
    ->name('suppliers.')
    ->controller(SupplierController::class)
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::get('/{supplier}', 'show')
            ->whereNumber('supplier')
            ->name('show');
        Route::get('/{supplier}/edit', 'edit')
            ->whereNumber('supplier')
            ->name('edit');
        Route::post('/', 'store')->name('store');
        Route::put('/{supplier}', 'update')->name('update');
        Route::delete('/{supplier}', 'destroy')->name('destroy');
    });

require __DIR__.'/settings.php';
