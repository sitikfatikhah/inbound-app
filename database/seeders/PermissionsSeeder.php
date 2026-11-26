<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)
            ->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | All permissions
        |--------------------------------------------------------------------------
        */

        $permissions = [
            'dashboard.view',

            'purchase-order.view',
            'purchase-order.create',
            'purchase-order.update',
            'purchase-order.delete',

            'custom-clearances.view',
            'custom-clearances.create',
            'custom-clearances.update',
            'custom-clearances.delete',

            'containers.view',
            'containers.create',
            'containers.update',
            'containers.delete',

            'payment.view',
            'payment.create',
            'payment.update',
            'payment.delete',

            'shipment.view',
            'shipment.create',
            'shipment.update',
            'shipment.delete',

            'shipment-document.view',
            'shipment-document.create',
            'shipment-document.update',
            'shipment-document.delete',

            'local-shipment.view',
            'local-shipment.create',
            'local-shipment.update',
            'local-shipment.delete',

            'shipment.customs-clearance.update',
            'shipment.schedule-revision.create',

            'customs-clearance.view',
            'customs-clearance.create',
            'customs-clearance.update',

            'customs-clearance-response.view',
            'customs-clearance-response.create',

            'warehouse-receiving.view',
            'warehouse-receiving.create',
            'warehouse-receiving.update',

            'port.view',
            'port.create',
            'port.update',
            'port.delete',

            'warehouse.view',
            'warehouse.create',
            'warehouse.update',
            'warehouse.delete',

            'shipping-line.view',
            'shipping-line.create',
            'shipping-line.update',
            'shipping-line.delete',

            'product.view',
            'product.create',
            'product.update',
            'product.delete',

            'supplier.view',
            'supplier.create',
            'supplier.update',
            'supplier.delete',

            'tracking-status.view',
            'tracking-status.create',
            'tracking-status.update',
            'tracking-status.delete',

            'master-data.view',
            'master-data.create',
            'master-data.update',
            'master-data.delete',

            'forwarder.view',
            'forwarder.create',
            'forwarder.update',
            'forwarder.delete',

            'reports.view',
            'reports.export',

            'users.view',
            'users.create',
            'users.update',
            'users.delete',

            'settings.view',
            'settings.update',

            'activity-log.view',
            'activity-logs.view',

            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        */

        $superAdmin = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web',
        ]);

        $superAdmin->syncPermissions(
            Permission::where('guard_name', 'web')->get()
        );

        /*
        |--------------------------------------------------------------------------
        | Admin
        |--------------------------------------------------------------------------
        */

        $admin = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web',
        ]);

        $admin->syncPermissions($permissions);

        /*
        |--------------------------------------------------------------------------
        | Import Manager
        |--------------------------------------------------------------------------
        */

        $importManager = Role::firstOrCreate([
            'name' => 'Import Manager',
            'guard_name' => 'web',
        ]);

        $importManager->syncPermissions([
            'dashboard.view',

            'purchase-order.view',
            'purchase-order.create',
            'purchase-order.update',

            'shipment.view',
            'shipment.create',
            'shipment.update',

            'customs-clearance.view',
            'customs-clearance.create',
            'customs-clearance.update',

            'warehouse-receiving.view',
            'warehouse-receiving.create',
            'warehouse-receiving.update',

            'master-data.view',
            'reports.view',
            'reports.export',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Import Staff
        |--------------------------------------------------------------------------
        */

        $importStaff = Role::firstOrCreate([
            'name' => 'Import Staff',
            'guard_name' => 'web',
        ]);

        $importStaff->syncPermissions([
            'dashboard.view',

            'purchase-order.view',
            'purchase-order.create',
            'purchase-order.update',

            'shipment.view',
            'shipment.create',
            'shipment.update',

            'customs-clearance.view',
            'warehouse-receiving.view',

            'reports.view',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Customs Staff
        |--------------------------------------------------------------------------
        */

        $customsStaff = Role::firstOrCreate([
            'name' => 'Customs Staff',
            'guard_name' => 'web',
        ]);

        $customsStaff->syncPermissions([
            'dashboard.view',
            'shipment.view',

            'customs-clearance.view',
            'customs-clearance.create',
            'customs-clearance.update',

            'reports.view',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Warehouse Staff
        |--------------------------------------------------------------------------
        */

        $warehouseStaff = Role::firstOrCreate([
            'name' => 'Warehouse Staff',
            'guard_name' => 'web',
        ]);

        $warehouseStaff->syncPermissions([
            'dashboard.view',
            'shipment.view',

            'warehouse-receiving.view',
            'warehouse-receiving.create',
            'warehouse-receiving.update',

            'reports.view',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Viewer
        |--------------------------------------------------------------------------
        */

        $viewer = Role::firstOrCreate([
            'name' => 'Viewer',
            'guard_name' => 'web',
        ]);

        $viewer->syncPermissions([
            'dashboard.view',
            'purchase-order.view',
            'shipment.view',
            'customs-clearance.view',
            'warehouse-receiving.view',
            'master-data.view',
            'forwarder.view',
            'reports.view',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Assign Super Admin to existing account
        |--------------------------------------------------------------------------
        |
        | This runs during seeding, not during login.
        | Change the email if your Super Admin account is different.
        |
        */

        $user = User::where('email', 'admin@test.com')->first();

        if ($user) {
            $user->syncRoles([$superAdmin]);
        }

        app(PermissionRegistrar::class)
            ->forgetCachedPermissions();
    }
}
