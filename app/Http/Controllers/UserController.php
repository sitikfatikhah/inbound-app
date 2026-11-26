<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('user/index', [
            'users' => User::query()
                ->with('roles:id,name')
                ->select(
                    'id',
                    'name',
                    'email',
                    'email_verified_at',
                    'created_at'
                )
                ->orderBy('name')
                ->paginate(15)
                ->withQueryString()
                ->through(fn (User $user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at,
                    'roles' => $user->getRoleNames()->values(),
                ]),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('user/create', [
            'availableRoles' => $this->availableRoles(),
        ]);
    }


    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'role' => [
                'required',
                'string',
                Rule::exists('roles', 'name')
                    ->where('guard_name', 'web')
                    ->where('name', '!=', 'Super Admin'),
            ],
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);

            $user->assignRole($validated['role']);
        });

        return redirect()
            ->route('user.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user): Response
    {
        return Inertia::render('user/show', [
            'user' => $user->load('roles:id,name'),
        ]);
    }

    public function edit(User $user): Response
    {
        return Inertia::render('user/edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames(),
            ],

            'availableRoles' => $this->availableRoles(),
        ]);
    }

    public function update(
        Request $request,
        User $user
    ): RedirectResponse {
        $validated = $request->validate([
            'role' => [
                'required',
                'string',
                Rule::exists('roles', 'name')
                    ->where('guard_name', 'web')
                    ->where('name', '!=', 'Super Admin'),
            ],
        ]);

        // Hindari user mengubah role miliknya sendiri.
        if ($request->user()->id === $user->id) {
            return back()->withErrors([
                'role' => 'You cannot change your own role.',
            ]);
        }

        $user->syncRoles([$validated['role']]);

        return redirect()
            ->route('users.index')
            ->with('success', 'User role updated successfully.');
    }

    /**
     * Delete a user.
     */
    public function destroy(
        Request $request,
        User $user
    ): RedirectResponse {
        // Hindari menghapus akun yang sedang digunakan.
        if ($request->user()->id === $user->id) {
            return back()->withErrors([
                'user' => 'You cannot delete your own account.',
            ]);
        }

        $user->delete();

        return redirect()
            ->route('user.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Get assignable roles.
     */
    private function availableRoles()
    {
        return Role::query()
            ->where('guard_name', 'web')
            ->where('name', '!=', 'Super Admin')
            ->orderBy('name')
            ->pluck('name');
    }
}
