<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\PermissionRegistrar;
use Webkul\Security\Models\Permission;
use Webkul\Security\Models\User;

class SecurityHelper
{
    private const GUARDS = ['web', 'sanctum'];

    public static function disableUserEvents(): void
    {
        // no-op: User::withoutEvents() is used during user creation and
        // unsetting the dispatcher globally disables events for all models.
    }

    public static function restoreUserEvents(): void
    {
        // no-op: keep model events enabled for API behavior under test.
    }

    public static function authenticateWithPermissions(array $permissionNames): User
    {
        $user = static::createUser();

        if (! empty($permissionNames)) {
            static::ensurePermissionsExist($permissionNames);
            static::assignPermissionsToUser($user, $permissionNames);
        }

        Auth::guard('web')->login($user);
        Auth::guard('web')->setUser($user);
        Auth::guard('sanctum')->setUser($user);
        Auth::shouldUse('sanctum');
        Sanctum::actingAs($user, ['*']);

        return $user;
    }

    private static function createUser(): User
    {
        return User::withoutEvents(
            fn (): User => User::query()->create([
                'name'     => fake()->name(),
                'email'    => fake()->unique()->safeEmail(),
                'password' => Hash::make('password'),
            ])
        );
    }

    private static function ensurePermissionsExist(array $permissionNames): void
    {
        $records = collect($permissionNames)
            ->crossJoin(self::GUARDS)
            ->map(fn ($pair) => [
                'name'       => $pair[0],
                'guard_name' => $pair[1],
            ])
            ->all();

        Permission::query()->upsert(
            $records,
            uniqueBy: ['name', 'guard_name'],
            update: []  // no-op on conflict - just ensure rows exist
        );
    }

    private static function assignPermissionsToUser(User $user, array $permissionNames): void
    {
        static::flushPermissionCache();

        $permissions = Permission::query()
            ->whereIn('name', $permissionNames)
            ->whereIn('guard_name', self::GUARDS)
            ->get();

        $user->givePermissionTo($permissions);

        static::flushPermissionCache();
    }

    private static function flushPermissionCache(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
