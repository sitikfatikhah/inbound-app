<?php

use App\Models\Forwarders;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(PermissionsSeeder::class);
});

it('allows users with resource or master-data access to open forwarder pages', function () {
    $user = User::factory()->create();
    $user->givePermissionTo([
        'master-data.view',
        'forwarder.create',
        'forwarder.update',
    ]);

    $forwarder = Forwarders::create([
        'forwarder_name' => 'Authorized Forwarder',
        'contact_person' => 'Test Contact',
        'email' => 'forwarder@example.com',
        'phone' => '08123456789',
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->get(route('forwarders.index'))
        ->assertOk();

    $this->get(route('forwarders.create'))
        ->assertOk();

    $this->get(route('forwarders.show', $forwarder))
        ->assertInertia(fn (Assert $page) => $page
            ->component('forwarder/show')
            ->where('forwarder.id', $forwarder->id));

    $this->get(route('forwarders.edit', $forwarder))
        ->assertInertia(fn (Assert $page) => $page
            ->component('forwarder/edit')
            ->where('forwarder.id', $forwarder->id));
});

it('allows users with role permissions to access role pages', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(['roles.view', 'roles.create', 'roles.update']);
    $role = Role::findByName('Admin', 'web');

    $this->actingAs($user)
        ->get(route('roles.index'))
        ->assertOk();

    $this->get(route('roles.create'))
        ->assertOk();

    $this->get(route('roles.show', $role))
        ->assertOk();

    $this->get(route('roles.edit', $role))
        ->assertOk();
});

it('denies users without a matching permission', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('forwarders.index'))
        ->assertForbidden();
});

it('resolves permission middleware for payment update routes', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('payment.update');

    $this->actingAs($user)
        ->get('/payment/999999/edit')
        ->assertNotFound();
});

it('assigns the seeded default account the matching super-admin role', function () {
    $this->seed(DatabaseSeeder::class);

    $admin = User::query()->where('email', 'admin@test.com')->firstOrFail();

    expect($admin->hasRole('Super Admin'))->toBeTrue();
});

it('defines every permission used by route middleware in the permission seeder', function () {
    $requiredPermissions = collect(Route::getRoutes()->getRoutes())
        ->flatMap(function (RoutingRoute $route): array {
            return collect($route->getAction()['middleware'] ?? [])
                ->filter(fn (string $middleware): bool => Str::startsWith($middleware, 'permission:'))
                ->flatMap(fn (string $middleware): array => explode('|', Str::after($middleware, 'permission:')))
                ->all();
        })
        ->unique()
        ->values();

    $definedPermissions = Permission::query()
        ->where('guard_name', 'web')
        ->whereIn('name', $requiredPermissions)
        ->pluck('name');

    expect($definedPermissions->sort()->values()->all())
        ->toBe($requiredPermissions->sort()->values()->all());
});
