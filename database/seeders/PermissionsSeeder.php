<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]
            ->forgetCachedPermissions();

        $permissions = [
            'dashboard.view',

            'purchase-order.view',
            'purchase-order.create',
            'purchase-order.update',
            'purchase-order.delete',

            'shipment.view',
            'shipment.create',
            'shipment.update',
            'shipment.delete',

            'customs-clearance.view',
            'customs-clearance.create',
            'customs-clearance.update',

            'warehouse-receiving.view',
            'warehouse-receiving.create',
            'warehouse-receiving.update',

            'master-data.view',
            'master-data.create',
            'master-data.update',
            'master-data.delete',

            'reports.view',
            'reports.export',

            'users.view',
            'users.create',
            'users.update',
            'users.delete',

            'settings.view',
            'settings.update',

            'activity-log.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $allPermissions = Permission::where(
            'guard_name',
            'web'
        )->get();

        // Super Admin
        $superAdmin = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web',
        ]);

        $superAdmin->syncPermissions($allPermissions);

        // Admin
        $admin = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web',
        ]);

        $admin->syncPermissions([
            'dashboard.view',

            'purchase-order.view',
            'purchase-order.create',
            'purchase-order.update',
            'purchase-order.delete',

            'shipment.view',
            'shipment.create',
            'shipment.update',
            'shipment.delete',

            'customs-clearance.view',
            'customs-clearance.create',
            'customs-clearance.update',

            'warehouse-receiving.view',
            'warehouse-receiving.create',
            'warehouse-receiving.update',

            'master-data.view',
            'master-data.create',
            'master-data.update',
            'master-data.delete',

            'reports.view',
            'reports.export',

            'users.view',
            'users.create',
            'users.update',
            'users.delete',

            'settings.view',
            'settings.update',

            'activity-log.view',
        ]);

        // Import Manager
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

        // Import Staff
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

        // Customs Staff
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

        // Warehouse Staff
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

        // Viewer
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
            'reports.view',
        ]);
    }
}
