<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ContainerController;
use App\Http\Controllers\CustomsClearanceResponsesController;
use App\Http\Controllers\CustomsClearancesController;
use App\Http\Controllers\CustomsController;
use App\Http\Controllers\CustomsPaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForwarderController;
use App\Http\Controllers\KnowledgeArticleController;
use App\Http\Controllers\LocalShipmentsController;
use App\Http\Controllers\PortController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\ShipmentCustomsClearancesController;
use App\Http\Controllers\ShipmentDocumentController;
use App\Http\Controllers\ShipmentImportController;
use App\Http\Controllers\ShipmentScheduleHistoriesController;
use App\Http\Controllers\ShippingLineController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\TrackingStatusController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
| Routes ini tetap public karena merupakan halaman landing/tracking.
| Jika tracking harus internal, pindahkan ke group auth dan tambahkan
| permission sesuai kebutuhan.
*/

Route::inertia('/', 'Welcome')->middleware('throttle:60,1')->name('home');
Route::inertia('/landing/login', 'landing/login')->middleware(['guest', 'throttle:20,1'])->name('landing-login');
Route::inertia('/public', 'public/public-dashboard')
    ->middleware(['guest', 'throttle:20,1'])
    ->name('public-dashboard');

Route::get('/analytics', function () { return Inertia::render('public/analytics');
    })->name('analytics.index');

Route::get('/search/bl_number', function () {
    return Inertia::render('search/bl_number', [
        'reference' => request('reference'),
    ]);
})->name('search.bl_number');

/*
|--------------------------------------------------------------------------
| Authenticated and verified routes
|--------------------------------------------------------------------------
| Semua route internal wajib login dan email terverifikasi.
| Permission middleware menentukan hak akses tiap aksi.
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', DashboardController::class)
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Customs master (CustomsController)
    |--------------------------------------------------------------------------
    */
    Route::prefix('custom-clearances')
        ->name('custom-clearances.')
        ->controller(CustomsController::class)
        ->group(function () {
            Route::get('/', 'index')
                ->middleware('permission:custom-clearances.view|customs-clearance.view|master-data.view')
                ->name('index');

            Route::get('/create', 'create')
                ->middleware('permission:custom-clearances.create|customs-clearance.create|master-data.create')
                ->name('create');

            Route::post('/', 'store')
                ->middleware('permission:custom-clearances.create|customs-clearance.create|master-data.create')
                ->name('store');

            Route::get('/{customClearance}', 'show')
                ->whereNumber('customClearance')
                ->middleware('permission:custom-clearances.view|customs-clearance.view|master-data.view')
                ->name('show');

            Route::get('/{customClearance}/edit', 'edit')
                ->whereNumber('customClearance')
                ->middleware('permission:custom-clearances.update|customs-clearance.update|master-data.update')
                ->name('edit');

            Route::put('/{customClearance}', 'update')
                ->whereNumber('customClearance')
                ->middleware('permission:custom-clearances.update|customs-clearance.update|master-data.update')
                ->name('update');

            Route::delete('/{customClearance}', 'destroy')
                ->whereNumber('customClearance')
                ->middleware('permission:custom-clearances.delete|master-data.delete')
                ->name('destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Containers
    |--------------------------------------------------------------------------
    */
    Route::prefix('containers')
        ->name('containers.')
        ->controller(ContainerController::class)
        ->group(function () {
            Route::get('/', 'index')->middleware('permission:containers.view|master-data.view')->name('index');
            Route::get('/create', 'create')->middleware('permission:containers.create|master-data.create')->name('create');
            Route::post('/', 'store')->middleware('permission:containers.create|master-data.create')->name('store');
            Route::get('/{container}', 'show')->whereNumber('container')->middleware('permission:containers.view|master-data.view')->name('show');
            Route::get('/{container}/edit', 'edit')->whereNumber('container')->middleware('permission:containers.update|master-data.update')->name('edit');
            Route::put('/{container}', 'update')->whereNumber('container')->middleware('permission:containers.update|master-data.update')->name('update');
            Route::delete('/{container}', 'destroy')->whereNumber('container')->middleware('permission:containers.delete|master-data.delete')->name('destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Customs payment
    |--------------------------------------------------------------------------
    */
    Route::prefix('payment')
        ->name('payment.')
        ->controller(CustomsPaymentController::class)
        ->group(function () {
            Route::get('/', 'index')->middleware('permission:payment.view')->name('index');
            Route::get('/create', 'create')->middleware('permission:payment.create')->name('create');
            Route::post('/', 'store')->middleware('permission:payment.create')->name('store');
            Route::get('/{payment}', 'show')->whereNumber('payment')->middleware('permission:payment.view')->name('show');
            Route::get('/{payment}/edit', 'edit')->whereNumber('payment')->middleware('permission:payment.update')->name('edit');
            Route::put('/{payment}', 'update')->whereNumber('payment')->middleware('permission:payment.update')->name('update');
            Route::delete('/{payment}', 'destroy')->whereNumber('payment')->middleware('permission:payment.delete')->name('destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Activity logs (read-only)
    |--------------------------------------------------------------------------
    */
    Route::prefix('activity-logs')
        ->name('activity-logs.')
        ->controller(ActivityLogController::class)
        ->group(function () {
            Route::get('/', 'index')->middleware('permission:activity-logs.view|activity-log.view')->name('index');
            Route::get('/{activityLog}', 'show')->whereNumber('activityLog')->middleware('permission:activity-logs.view|activity-log.view')->name('show');
        });

    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */
    Route::prefix('user')
        ->name('user.')
        ->controller(UserController::class)
        ->group(function () {
            Route::get('/', 'index')->middleware('permission:users.view')->name('index');
            Route::get('/create', 'create')->middleware('permission:users.create')->name('create');
            Route::post('/', 'store')->middleware('permission:users.create')->name('store');
            Route::get('/{user}', 'show')->whereNumber('user')->middleware('permission:users.view')->name('show');
            Route::get('/{user}/edit', 'edit')->whereNumber('user')->middleware('permission:users.update')->name('edit');
            Route::put('/{user}', 'update')->whereNumber('user')->middleware('permission:users.update')->name('update');
            Route::delete('/{user}', 'destroy')->whereNumber('user')->middleware('permission:users.delete')->name('destroy');
        });
    /*
    |--------------------------------------------------------------------------
    | Role
    |--------------------------------------------------------------------------
    */
    Route::prefix('roles')
        ->name('roles.')
        ->controller(RoleController::class)
        ->group(function () {
            Route::get('/', 'index')->middleware('permission:roles.view')->name('index');
            Route::get('/create', 'create')->middleware('permission:roles.create')->name('create');
            Route::post('/', 'store')->middleware('permission:roles.create')->name('store');
            Route::get('/{role}/edit', 'edit')->middleware('permission:roles.update')->name('edit');
            Route::put('/{role}', 'update')->middleware('permission:roles.update')->name('update');
            Route::delete('/{role}', 'destroy')->middleware('permission:roles.delete')->name('destroy');
            Route::get('/{role}', 'show')->middleware('permission:roles.view')->name('show');
        });

    /*
    |--------------------------------------------------------------------------
    | Shipments
    |--------------------------------------------------------------------------
    */
    Route::prefix('shipments')
        ->name('shipments.')
        ->controller(ShipmentController::class)
        ->group(function () {
            Route::get('/', 'index')->middleware('permission:shipment.view')->name('index');
            Route::get('/create', 'create')->middleware('permission:shipment.create')->name('create');
            Route::post('/', 'store')->middleware('permission:shipment.create')->name('store');

            Route::get('/{shipment}', 'show')->whereNumber('shipment')->middleware('permission:shipment.view')->name('show');
            Route::get('/{shipment}/edit', 'edit')->whereNumber('shipment')->middleware('permission:shipment.update')->name('edit');
            Route::put('/{shipment}', 'update')->whereNumber('shipment')->middleware('permission:shipment.update')->name('update');
            Route::delete('/{shipment}', 'destroy')->whereNumber('shipment')->middleware('permission:shipment.delete')->name('destroy');

            Route::put('/{shipment}/customs-clearance', [ShipmentCustomsClearancesController::class, 'update'])
                ->whereNumber('shipment')
                ->middleware('permission:shipment.customs-clearance.update|customs-clearance.update|shipment.update')
                ->name('customs-clearance.update');

            Route::post('/{shipment}/schedule-revisions', [ShipmentScheduleHistoriesController::class, 'store'])
                ->whereNumber('shipment')
                ->middleware('permission:shipment.schedule-revision.create|shipment.update')
                ->name('schedule-revisions.store');
        });

    /*
    |--------------------------------------------------------------------------
    | Shipment documents
    |--------------------------------------------------------------------------
    */
    Route::prefix('shipment-documents')
        ->name('shipment-documents.')
        ->controller(ShipmentDocumentController::class)
        ->group(function () {
            Route::get('/', 'index')->middleware('permission:shipment-document.view')->name('index');
            Route::get('/create', 'create')->middleware('permission:shipment-document.create')->name('create');
            Route::post('/', 'store')->middleware('permission:shipment-document.create')->name('store');
            Route::get('/{shipment_document}', 'show')->whereNumber('shipment_document')->middleware('permission:shipment-document.view')->name('show');
            Route::get('/{shipment_document}/edit', 'edit')->whereNumber('shipment_document')->middleware('permission:shipment-document.update')->name('edit');
            Route::put('/{shipment_document}', 'update')->whereNumber('shipment_document')->middleware('permission:shipment-document.update')->name('update');
            Route::delete('/{shipment_document}', 'destroy')->whereNumber('shipment_document')->middleware('permission:shipment-document.delete')->name('destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Local shipments
    |--------------------------------------------------------------------------
    */
    Route::prefix('local-shipment')
        ->name('local-shipment.')
        ->controller(LocalShipmentsController::class)
        ->group(function () {
            Route::get('/', 'index')->middleware('permission:local-shipment.view')->name('index');
            Route::get('/create', 'create')->middleware('permission:local-shipment.create')->name('create');
            Route::post('/', 'store')->middleware('permission:local-shipment.create')->name('store');
            Route::get('/{local_shipment}', 'show')->whereNumber('local_shipment')->middleware('permission:local-shipment.view')->name('show');
            Route::get('/{local_shipment}/edit', 'edit')->whereNumber('local_shipment')->middleware('permission:local-shipment.update')->name('edit');
            Route::put('/{local_shipment}', 'update')->whereNumber('local_shipment')->middleware('permission:local-shipment.update')->name('update');
            Route::delete('/{local_shipment}', 'destroy')->whereNumber('local_shipment')->middleware('permission:local-shipment.delete')->name('destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Forwarders
    |--------------------------------------------------------------------------
    */
    Route::prefix('forwarders')
        ->name('forwarders.')
        ->controller(ForwarderController::class)
        ->group(function () {
            Route::get('/', 'index')->middleware('permission:forwarder.view|master-data.view')->name('index');
            Route::get('/create', 'create')->middleware('permission:forwarder.create|master-data.create')->name('create');
            Route::post('/', 'store')->middleware('permission:forwarder.create|master-data.create')->name('store');
            Route::get('/{forwarder}', 'show')->whereNumber('forwarder')->middleware('permission:forwarder.view|master-data.view')->name('show');
            Route::get('/{forwarder}/edit', 'edit')->whereNumber('forwarder')->middleware('permission:forwarder.update|master-data.update')->name('edit');
            Route::put('/{forwarder}', 'update')->whereNumber('forwarder')->middleware('permission:forwarder.update|master-data.update')->name('update');
            Route::delete('/{forwarder}', 'destroy')->whereNumber('forwarder')->middleware('permission:forwarder.delete|master-data.delete')->name('destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Ports
    |--------------------------------------------------------------------------
    */
    Route::prefix('ports')
        ->name('ports.')
        ->controller(PortController::class)
        ->group(function () {
            Route::get('/', 'index')->middleware('permission:port.view|master-data.view')->name('index');
            Route::get('/create', 'create')->middleware('permission:port.create|master-data.create')->name('create');
            Route::post('/', 'store')->middleware('permission:port.create|master-data.create')->name('store');
            Route::get('/{port}', 'show')->whereNumber('port')->middleware('permission:port.view|master-data.view')->name('show');
            Route::get('/{port}/edit', 'edit')->whereNumber('port')->middleware('permission:port.update|master-data.update')->name('edit');
            Route::put('/{port}', 'update')->whereNumber('port')->middleware('permission:port.update|master-data.update')->name('update');
            Route::delete('/{port}', 'destroy')->whereNumber('port')->middleware('permission:port.delete|master-data.delete')->name('destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Warehouses
    |--------------------------------------------------------------------------
    */
    Route::prefix('warehouses')
        ->name('warehouses.')
        ->controller(WarehouseController::class)
        ->group(function () {
            Route::get('/', 'index')->middleware('permission:warehouse.view|master-data.view|warehouse-receiving.view')->name('index');
            Route::get('/create', 'create')->middleware('permission:warehouse.create|master-data.create|warehouse-receiving.create')->name('create');
            Route::post('/', 'store')->middleware('permission:warehouse.create|master-data.create|warehouse-receiving.create')->name('store');
            Route::get('/{warehouse}', 'show')->whereNumber('warehouse')->middleware('permission:warehouse.view|master-data.view|warehouse-receiving.view')->name('show');
            Route::get('/{warehouse}/edit', 'edit')->whereNumber('warehouse')->middleware('permission:warehouse.update|master-data.update|warehouse-receiving.update')->name('edit');
            Route::put('/{warehouse}', 'update')->whereNumber('warehouse')->middleware('permission:warehouse.update|master-data.update|warehouse-receiving.update')->name('update');
            Route::delete('/{warehouse}', 'destroy')->whereNumber('warehouse')->middleware('permission:warehouse.delete|master-data.delete')->name('destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Shipping lines
    |--------------------------------------------------------------------------
    | URL prefix tetap shippingLine agar tidak merusak URL lama.
    */
    Route::prefix('shippingLine')
        ->name('shipping_line.')
        ->controller(ShippingLineController::class)
        ->group(function () {
            Route::get('/', 'index')->middleware('permission:shipping-line.view|master-data.view')->name('index');
            Route::get('/create', 'create')->middleware('permission:shipping-line.create|master-data.create')->name('create');
            Route::post('/', 'store')->middleware('permission:shipping-line.create|master-data.create')->name('store');
            Route::get('/{shippingLine}', 'show')->whereNumber('shippingLine')->middleware('permission:shipping-line.view|master-data.view')->name('show');
            Route::get('/{shippingLine}/edit', 'edit')->whereNumber('shippingLine')->middleware('permission:shipping-line.update|master-data.update')->name('edit');
            Route::put('/{shippingLine}', 'update')->whereNumber('shippingLine')->middleware('permission:shipping-line.update|master-data.update')->name('update');
            Route::delete('/{shippingLine}', 'destroy')->whereNumber('shippingLine')->middleware('permission:shipping-line.delete|master-data.delete')->name('destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Purchase orders
    |--------------------------------------------------------------------------
    */
    Route::prefix('po')
        ->name('po.')
        ->controller(PurchaseOrderController::class)
        ->group(function () {
            Route::get('/', 'index')->middleware('permission:purchase-order.view')->name('index');
            Route::get('/create', 'create')->middleware('permission:purchase-order.create')->name('create');
            Route::post('/', 'store')->middleware('permission:purchase-order.create')->name('store');
            Route::get('/{purchaseOrder}', 'show')->whereNumber('purchaseOrder')->middleware('permission:purchase-order.view')->name('show');
            Route::get('/{purchaseOrder}/edit', 'edit')->whereNumber('purchaseOrder')->middleware('permission:purchase-order.update')->name('edit');
            Route::put('/{purchaseOrder}', 'update')->whereNumber('purchaseOrder')->middleware('permission:purchase-order.update')->name('update');
            Route::delete('/{purchaseOrder}', 'destroy')->whereNumber('purchaseOrder')->middleware('permission:purchase-order.delete')->name('destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Products
    |--------------------------------------------------------------------------
    */
    Route::prefix('products')
        ->name('products.')
        ->controller(ProductController::class)
        ->group(function () {
            Route::get('/', 'index')->middleware('permission:product.view|master-data.view')->name('index');
            Route::get('/create', 'create')->middleware('permission:product.create|master-data.create')->name('create');
            Route::post('/', 'store')->middleware('permission:product.create|master-data.create')->name('store');
            Route::get('/{products}', 'show')->whereNumber('products')->middleware('permission:product.view|master-data.view')->name('show');
            Route::get('/{products}/edit', 'edit')->whereNumber('products')->middleware('permission:product.update|master-data.update')->name('edit');
            Route::put('/{products}', 'update')->whereNumber('products')->middleware('permission:product.update|master-data.update')->name('update');
            Route::delete('/{products}', 'destroy')->whereNumber('products')->middleware('permission:product.delete|master-data.delete')->name('destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Suppliers
    |--------------------------------------------------------------------------
    */
    Route::prefix('suppliers')
        ->name('suppliers.')
        ->controller(SupplierController::class)
        ->group(function () {
            Route::get('/', 'index')->middleware('permission:supplier.view|master-data.view')->name('index');
            Route::get('/create', 'create')->middleware('permission:supplier.create|master-data.create')->name('create');
            Route::post('/', 'store')->middleware('permission:supplier.create|master-data.create')->name('store');
            Route::get('/{supplier}', 'show')->whereNumber('supplier')->middleware('permission:supplier.view|master-data.view')->name('show');
            Route::get('/{supplier}/edit', 'edit')->whereNumber('supplier')->middleware('permission:supplier.update|master-data.update')->name('edit');
            Route::put('/{supplier}', 'update')->whereNumber('supplier')->middleware('permission:supplier.update|master-data.update')->name('update');
            Route::delete('/{supplier}', 'destroy')->whereNumber('supplier')->middleware('permission:supplier.delete|master-data.delete')->name('destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Tracking statuses
    |--------------------------------------------------------------------------
    */
    Route::prefix('tracking-status')
        ->name('tracking-status.')
        ->controller(TrackingStatusController::class)
        ->group(function () {
            Route::get('/', 'index')->middleware('permission:tracking-status.view')->name('index');
            Route::get('/create', 'create')->middleware('permission:tracking-status.create')->name('create');
            Route::post('/', 'store')->middleware('permission:tracking-status.create')->name('store');
            Route::get('/{tracking_status}', 'show')->whereNumber('tracking_status')->middleware('permission:tracking-status.view')->name('show');
            Route::get('/{tracking_status}/edit', 'edit')->whereNumber('tracking_status')->middleware('permission:tracking-status.update')->name('edit');
            Route::put('/{tracking_status}', 'update')->whereNumber('tracking_status')->middleware('permission:tracking-status.update')->name('update');
            Route::delete('/{tracking_status}', 'destroy')->whereNumber('tracking_status')->middleware('permission:tracking-status.delete')->name('destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Customs clearance records
    |--------------------------------------------------------------------------
    */
    Route::prefix('customs-clearance')
        ->name('customs-clearance.')
        ->controller(CustomsClearancesController::class)
        ->group(function () {
            Route::get('/', 'index')->middleware('permission:customs-clearance.view')->name('index');
            Route::get('/{shipment}', 'show')->whereNumber('shipment')->middleware('permission:customs-clearance.view')->name('show');
            Route::put('/{shipment}', 'update')->whereNumber('shipment')->middleware('permission:customs-clearance.update')->name('update');

            Route::post('/{customsClearance}/responses', [CustomsClearanceResponsesController::class, 'store'])
                ->whereNumber('customsClearance')
                ->middleware('permission:customs-clearance-response.create|customs-clearance.create')
                ->name('responses.store');
        });

    /*
    |--------------------------------------------------------------------------
    | Customs clearance responses (history)
    |--------------------------------------------------------------------------
    */
    Route::prefix('customs-clearance-response')
        ->name('customs-clearance-response.')
        ->controller(CustomsClearanceResponsesController::class)
        ->group(function () {
            Route::get('/', 'index')->middleware('permission:customs-clearance-response.view|customs-clearance.view')->name('index');
            Route::get('/{response}', 'show')->whereNumber('response')->middleware('permission:customs-clearance-response.view|customs-clearance.view')->name('show');
        });

    /*
    |--------------------------------------------------------------------------
    | Knowledge MGM
    |--------------------------------------------------------------------------
    */

    Route::prefix('knowledge-mgm')
        ->name('knowledge-mgm.')
        ->controller(KnowledgeArticleController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/knowledge/create', 'create')->name('knowledge.create');
            Route::post('/knowledge', 'store')->name('knowledge.store');
            Route::get('/knowledge/{article}/edit', 'edit')->name('knowledge.edit');
            Route::put('/knowledge/{article}', 'update')->name('knowledge.update');
            Route::delete('/knowledge/{article}', 'destroy')->name('knowledge.destroy');
            Route::get('/knowledge/{article}', 'show')->name('knowledge.show');
        });
    Route::prefix('knowledge-mgm')
        ->name('knowledge-mgm.')
        ->controller(KnowledgeArticleController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
        });

    /*
    |--------------------------------------------------------------------------
    | Import shipments
    |--------------------------------------------------------------------------
    */

    Route::prefix('tracking')
        ->name('tracking.')
        ->controller(TrackingController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/import', 'import')->name('import');
            Route::get('/local', 'local')->name('local');
            Route::get('/schedule', 'schedule')->name('schedule');
            Route::get('/warehouse', 'warehouse')->name('warehouse');
            Route::get('/project', 'project')->name('project');
        });

    Route::middleware(['auth'])->group(function () {
        Route::post(
            '/shipments/import',
            [ShipmentImportController::class, 'store']
        )->name('shipments.import');
    });

});

require __DIR__.'/settings.php';
