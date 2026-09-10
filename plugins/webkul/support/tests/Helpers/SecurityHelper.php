<?php

use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\PermissionRegistrar;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\Permission;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;

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

    public static function authenticateWithPermissions(array $permissionNames, bool $bypassCompanyScope = false): User
    {
        $user = static::createUser();

        if ($user->default_company_id) {
            $user->allowedCompanies()->sync([$user->default_company_id]);

            session([CompanyContext::SESSION_KEY => [$user->default_company_id]]);
        }

        if ($bypassCompanyScope) {
            $permissionNames[] = 'bypass_company_scope';
        }

        if (! empty($permissionNames)) {
            static::ensurePermissionsExist($permissionNames);
            static::assignPermissionsToUser($user, $permissionNames);
        }

        Auth::guard('web')->login($user);
        Auth::guard('web')->setUser($user);
        Auth::guard('sanctum')->setUser($user);
        Auth::shouldUse('sanctum');
        Sanctum::actingAs($user, ['*']);

        app()->forgetInstance(CompanyContext::class);

        return $user;
    }

    public static function actingAsTagApiUser(
        array $permissionNames = [],
        bool $useGlobalResourcePermission = false
    ): User {
        $user = static::authenticateWithPermissions($permissionNames);

        if ($useGlobalResourcePermission) {
            $user->forceFill([
                'resource_permission' => PermissionType::GLOBAL,
            ])->saveQuietly();
        }

        return $user;
    }

    private static function createUser(): User
    {
        $companyId = Company::query()->value('id') ?? Company::factory()->create()->id;

        return User::withoutEvents(fn (): User => User::factory()->create([
            'default_company_id' => $companyId,
        ]));
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
