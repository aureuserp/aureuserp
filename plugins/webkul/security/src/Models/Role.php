<?php

namespace Webkul\Security\Models;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role as BaseRole;
use Spatie\Permission\PermissionRegistrar;

class Role extends BaseRole
{
    public function getNameAttribute($value)
    {
        return Str::ucfirst($value);
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

        DB::table($tableName)->where($roleColumn, $this->id)->delete();

        if ($permissionIds->isNotEmpty()) {
            $chunkSize = 1000;

            $permissionIds->chunk($chunkSize)->each(function ($chunk) use ($tableName, $roleColumn, $permissionColumn) {
                $insertData = $chunk->map(fn ($permissionId) => [
                    $roleColumn       => $this->id,
                    $permissionColumn => $permissionId,
                ])->toArray();

                DB::table($tableName)->insert($insertData);
            });
        }

        $this->forgetCachedPermissions();
        
        $this->refresh();
    }
}
