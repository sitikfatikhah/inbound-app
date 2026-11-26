<?php

namespace App\Http\Controllers;

use App\Services\RolePermissionService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct(
        protected RolePermissionService $rolePermissionService
    ) {}

    public function index(): Response
    {
        return Inertia::render('roles/index', [
            'roles' => $this->rolePermissionService->getAllRoles(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('roles/create', [
            'permissions' => $this->rolePermissionService
                ->getAllPermissions(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:125',
                'unique:roles,name',
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [
                'string',
                Rule::exists('permissions', 'name')
                    ->where('guard_name', 'web'),
            ],
        ]);

        $this->rolePermissionService->createRole($data);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil dibuat.');
    }

    public function edit(Role $role): Response
    {
        abort_unless($role->guard_name === 'web', 404);

        return Inertia::render('roles/edit', [
            'role' => $role->load('permissions:id,name'),
            'permissions' => $this->rolePermissionService
                ->getAllPermissions(),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        abort_unless($role->guard_name === 'web', 404);

        // Role Super Admin tidak boleh diubah melalui UI.
        abort_if($role->name === 'Super Admin', 403);

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:125',
                Rule::unique('roles', 'name')
                    ->where('guard_name', 'web')
                    ->ignore($role->id),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [
                'string',
                Rule::exists('permissions', 'name')
                    ->where('guard_name', 'web'),
            ],
        ]);

        $this->rolePermissionService->updateRole($role, $data);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role)
    {
        abort_unless($role->guard_name === 'web', 404);

        abort_if($role->name === 'Super Admin', 403);

        if ($role->users()->exists()) {
            return back()->withErrors([
                'role' => 'Role masih digunakan oleh user.',
            ]);
        }

        $this->rolePermissionService->deleteRole($role);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil dihapus.');
    }
    public function show(Role $role): Response
    {
        abort_unless($role->guard_name === 'web', 404);

        $role->load([
            'permissions:id,name',
            'users:id,name,email',
        ]);

        return Inertia::render('roles/show', [
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'guard_name' => $role->guard_name,
                'created_at' => $role->created_at,
                'updated_at' => $role->updated_at,
                'permissions' => $role->permissions,
                'users' => $role->users,
            ],
        ]);
    }
}
