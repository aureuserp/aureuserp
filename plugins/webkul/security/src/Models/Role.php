<?php

namespace Webkul\Security\Models;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role as BaseRole;
use Spatie\Permission\PermissionRegistrar;

class Role extends BaseRole
{
    protected const SYSTEM_ROLE_FALLBACKS = [
        'admin',
        'super_admin',
    ];

    public function getNameAttribute(string $value): string
    {
        return Str::ucfirst($value);
    }

    protected static function booted(): void
    {
        static::updating(function (self $role): void {
            if (! $role->isSystemRole()) {
                return;
            }

            if ($role->isDirty(['name', 'guard_name'])) {
                throw new AuthorizationException(__('You are not allowed to modify this system role.'));
            }
        });

        static::deleting(function (self $role): void {
            if ($role->isSystemRole()) {
                throw new AuthorizationException(__('You are not allowed to delete this system role.'));
            }
        });
    }

    public function isSystemRole(): bool
    {
        $name = $this->getRawOriginal('name') ?: $this->attributes['name'] ?? null;

        if ((! is_string($name) || $name === '') && $this->exists) {
            $name = static::query()
                ->whereKey($this->getKey())
                ->value('name');
        }

        if (! is_string($name) || $name === '') {
            return false;
        }

        return in_array(static::normalizeRoleName($name), static::getSystemRoleNames(), true);
    }

    public static function getSystemRoleNames(): array
    {
        $configuredNames = [
            config('filament-shield.panel_user.name'),
            config('filament-shield.super_admin.name'),
        ];

        return collect(array_merge(static::SYSTEM_ROLE_FALLBACKS, $configuredNames))
            ->filter(fn ($name) => is_string($name) && $name !== '')
            ->map(fn (string $name) => static::normalizeRoleName($name))
            ->unique()
            ->values()
            ->all();
    }

    protected static function normalizeRoleName(string $name): string
    {
        return Str::of($name)->trim()->lower()->toString();
    }

    /**
     * Sync permissions by their names.
     * Creates missing permissions and syncs them to the role.
     */
    public function syncPermissionsByNames(Collection|array $permissionNames): void
    {
        $permissionNames = collect($permissionNames)->unique()->values();

        if ($permissionNames->isEmpty()) {
            $this->syncPermissions([]);

            return;
        }

        $permissionIds = $this->ensurePermissionsExist($permissionNames);

        $this->syncPermissionsToRole($permissionIds);
    }

    /**
     * Ensure all permissions exist in the database and return their IDs.
     */
    private function ensurePermissionsExist(Collection $permissionNames): Collection
    {
        $permissionModel = Utils::getPermissionModel();

        $guard = $this->guard_name;

        $chunkSize = 500;

        $allPermissionIds = collect();

        $permissionNames->chunk($chunkSize)->each(function ($chunk) use ($permissionModel, $guard, &$allPermissionIds) {
            $existingPermissions = $permissionModel::whereIn('name', $chunk)
                ->where('guard_name', $guard)
                ->pluck('id', 'name');

            $missingPermissions = $chunk->diff($existingPermissions->keys());

            if ($missingPermissions->isNotEmpty()) {
                $this->createMissingPermissions($permissionModel, $missingPermissions, $guard);

                $newPermissions = $permissionModel::whereIn('name', $missingPermissions)
                    ->where('guard_name', $guard)
                    ->pluck('id', 'name');

                $existingPermissions = $existingPermissions->merge($newPermissions);
            }

            $allPermissionIds = $allPermissionIds->merge($existingPermissions->values());
        });

        return $allPermissionIds->unique()->values();
    }

    /**
     * Create missing permissions in bulk.
     */
    private function createMissingPermissions(string $permissionModel, Collection $permissionNames, string $guard): void
    {
        $insertData = $permissionNames->map(fn ($name) => [
            'name'       => $name,
            'guard_name' => $guard,
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();

        $permissionModel::insertOrIgnore($insertData);
    }

    /**
     * Sync permissions to the role in the pivot table.
     */
    private function syncPermissionsToRole(Collection $permissionIds): void
    {
        $tableName = config('permission.table_names.role_has_permissions');

        $permissionRegistrar = app(PermissionRegistrar::class);

        $roleColumn = $permissionRegistrar->pivotRole;

        $permissionColumn = $permissionRegistrar->pivotPermission;

        $existingPermissionIds = DB::table($tableName)
            ->where($roleColumn, $this->id)
            ->pluck($permissionColumn)
            ->map(fn ($permissionId) => (int) $permissionId);

        $permissionIds = $permissionIds
            ->map(fn ($permissionId) => (int) $permissionId)
            ->unique()
            ->values();

        $permissionIdsToDelete = $existingPermissionIds->diff($permissionIds)->values();
        $permissionIdsToInsert = $permissionIds->diff($existingPermissionIds)->values();

        if ($permissionIdsToDelete->isNotEmpty()) {
            DB::table($tableName)
                ->where($roleColumn, $this->id)
                ->whereIn($permissionColumn, $permissionIdsToDelete)
                ->delete();
        }

        if ($permissionIdsToInsert->isNotEmpty()) {
            $chunkSize = 1000;

            $permissionIdsToInsert->chunk($chunkSize)->each(function ($chunk) use ($tableName, $roleColumn, $permissionColumn) {
                $insertData = $chunk->map(fn ($permissionId) => [
                    $roleColumn       => $this->id,
                    $permissionColumn => $permissionId,
                ])->toArray();

                DB::table($tableName)->insert($insertData);
            });
        }

        $this->forgetCachedPermissions();
    }
}
