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
