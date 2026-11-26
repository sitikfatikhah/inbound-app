<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionService
{
    public function getAllRoles(): Collection
    {
        return Role::query()
            ->where('guard_name', 'web')
            ->withCount('users')
            ->with('permissions:id,name')
            ->orderBy('name')
            ->get();
    }

    public function getAllPermissions(): Collection
    {
        return Permission::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function createRole(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            $role = Role::create([
                'name' => $data['name'],
                'guard_name' => 'web',
            ]);

            $role->syncPermissions($data['permissions'] ?? []);

            return $role;
        });
    }

    public function updateRole(Role $role, array $data): Role
    {
        return DB::transaction(function () use ($role, $data) {
            $role->update([
                'name' => $data['name'],
            ]);

            $role->syncPermissions($data['permissions'] ?? []);

            return $role->load('permissions:id,name');
        });
    }

    public function deleteRole(Role $role): void
    {
        DB::transaction(function () use ($role) {
            $role->delete();
        });
    }
}
