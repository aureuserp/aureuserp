<?php

namespace Webkul\Security\Filament\Resources\RoleResource\Pages;

use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Webkul\Security\Filament\Resources\RoleResource;
use Spatie\Permission\PermissionRegistrar;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    public Collection $permissions;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->permissions = collect($data)
            ->filter(function ($permission, $key) {
                return ! in_array($key, ['name', 'guard_name', 'select_all']);
            })
            ->values()
            ->flatten()
            ->unique();

        return [
            'name'       => $data['name'],
            'guard_name' => Utils::getFilamentAuthGuard(),
        ];
    }

    protected function afterCreate(): void
    {
        DB::transaction(function () {
            $this->syncPermissions();
        });
    }

    private function syncPermissions(): void
    {
        $permissionModel = Utils::getPermissionModel();

        $guard = $this->data['guard_name'];

        $permissionNames = $this->permissions->unique()->values();

        if ($permissionNames->isEmpty()) {
            $this->record->syncPermissions([]);

            return;
        }

        $chunkSize = 500;
        $allPermissionIds = collect();

        $permissionNames->chunk($chunkSize)->each(function ($chunk) use ($permissionModel, $guard, &$allPermissionIds) {
            $existingPermissions = $permissionModel::whereIn('name', $chunk)
                ->where('guard_name', $guard)
                ->pluck('id', 'name');

            $missingPermissions = $chunk->diff($existingPermissions->keys());

            if ($missingPermissions->isNotEmpty()) {
                $insertData = $missingPermissions->map(fn($name) => [
                    'name' => $name,
                    'guard_name' => $guard,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->toArray();

                $permissionModel::insertOrIgnore($insertData);

                $newPermissions = $permissionModel::whereIn('name', $missingPermissions)
                    ->where('guard_name', $guard)
                    ->pluck('id', 'name');

                $existingPermissions = $existingPermissions->merge($newPermissions);
            }

            $allPermissionIds = $allPermissionIds->merge($existingPermissions->values());
        });

        $this->syncPermissionsByIds($allPermissionIds->unique()->values()->toArray());
    }

    private function syncPermissionsByIds(array $permissionIds): void
    {
        $roleId = $this->record->id;

        $tableName = config('permission.table_names.role_has_permissions');

        $permissionRegistrar = app(PermissionRegistrar::class);

        $roleColumn = $permissionRegistrar->pivotRole;

        $permissionColumn = $permissionRegistrar->pivotPermission;

        DB::table($tableName)->where($roleColumn, $roleId)->delete();

        if (! empty($permissionIds)) {
            $chunkSize = 1000;

            $chunks = array_chunk($permissionIds, $chunkSize);

            foreach ($chunks as $chunk) {
                $insertData = array_map(function ($permissionId) use ($roleId, $roleColumn, $permissionColumn) {
                    return [
                        $roleColumn => $roleId,

                        $permissionColumn => $permissionId,
                    ];
                }, $chunk);

                DB::table($tableName)->insert($insertData);
            }
        }

        $this->record->forgetCachedPermissions();

        $this->record->refresh();
    }
}
