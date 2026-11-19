<?php

namespace Webkul\Asset\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Seed the asset plugin permissions and standard operational roles.
     */
    public function run(): void
    {
        if (! class_exists(Role::class) || ! class_exists(Permission::class)) {
            return;
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $allAssetPermissions = $this->getAllAssetPermissions();

        $permissionsByName = collect($allAssetPermissions)->mapWithKeys(function (string $permissionName): array {
            $permission = Permission::query()->firstOrCreate([
                'name'       => $permissionName,
                'guard_name' => 'web',
            ]);

            return [$permissionName => $permission];
        });

        $superAdminRoleName = config('filament-shield.super_admin.name', 'super_admin');
        $panelUserRoleName = config('filament-shield.panel_user.name', 'Admin');

        $adminRoles = Role::query()
            ->where('guard_name', 'web')
            ->where(fn ($query) => $query
                ->where('is_default', true)
                ->orWhereIn('name', array_values(array_unique(array_filter([
                    $superAdminRoleName,
                    $panelUserRoleName,
                    'super_admin',
                    'Admin',
                ])))))
            ->get();

        foreach ($adminRoles as $adminRole) {
            $adminRole->givePermissionTo($permissionsByName->values()->all());
        }

        foreach ($this->getStandardRolePermissions() as $roleName => $permissionNames) {
            $role = Role::query()->firstOrCreate([
                'name'       => $roleName,
                'guard_name' => 'web',
            ]);

            $rolePermissions = $permissionsByName
                ->only($permissionNames)
                ->values()
                ->all();

            $role->givePermissionTo($rolePermissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * @return list<string>
     */
    public function getAllAssetPermissions(): array
    {
        return [
            'view_any_asset_asset',
            'view_asset_asset',
            'create_asset_asset',
            'update_asset_asset',
            'delete_asset_asset',
            'delete_any_asset_asset',
            'restore_asset_asset',
            'restore_any_asset_asset',
            'force_delete_asset_asset',
            'force_delete_any_asset_asset',

            'view_any_asset_product',
            'view_asset_product',
            'create_asset_product',
            'update_asset_product',
            'delete_asset_product',
            'delete_any_asset_product',
            'restore_asset_product',
            'restore_any_asset_product',
            'force_delete_asset_product',
            'force_delete_any_asset_product',

            'view_any_asset_asset::site',
            'view_asset_asset::site',
            'create_asset_asset::site',
            'update_asset_asset::site',
            'delete_asset_asset::site',
            'delete_any_asset_asset::site',
            'restore_asset_asset::site',
            'restore_any_asset_asset::site',
            'force_delete_asset_asset::site',
            'force_delete_any_asset_asset::site',

            'view_any_asset_asset::warehouse',
            'view_asset_asset::warehouse',
            'create_asset_asset::warehouse',
            'update_asset_asset::warehouse',
            'delete_asset_asset::warehouse',
            'delete_any_asset_asset::warehouse',
            'restore_asset_asset::warehouse',
            'restore_any_asset_asset::warehouse',
            'force_delete_asset_asset::warehouse',
            'force_delete_any_asset_asset::warehouse',

            'view_any_asset_asset::category',
            'view_asset_asset::category',
            'create_asset_asset::category',
            'update_asset_asset::category',
            'delete_asset_asset::category',
            'delete_any_asset_asset::category',

            'view_any_asset_asset::movement',
            'view_asset_asset::movement',

            'page_asset_manage_assets',
        ];
    }

    /**
     * @return array<string, list<string>>
     */
    public function getStandardRolePermissions(): array
    {
        return [
            'Asset Manager'   => $this->getAllAssetPermissions(),
            'Asset Officer'   => [
                'view_any_asset_asset',
                'view_asset_asset',
                'create_asset_asset',
                'update_asset_asset',
                'view_any_asset_product',
                'view_asset_product',
                'create_asset_product',
                'update_asset_product',
                'view_any_asset_asset::category',
                'view_asset_asset::category',
                'create_asset_asset::category',
                'update_asset_asset::category',
                'view_any_asset_asset::site',
                'view_asset_asset::site',
                'create_asset_asset::site',
                'update_asset_asset::site',
                'view_any_asset_asset::warehouse',
                'view_asset_asset::warehouse',
                'create_asset_asset::warehouse',
                'update_asset_asset::warehouse',
                'view_any_asset_asset::movement',
                'view_asset_asset::movement',
            ],
            'Custody Manager' => [
                'view_any_asset_asset',
                'view_asset_asset',
                'update_asset_asset',
                'delete_asset_asset',
                'delete_any_asset_asset',
                'view_any_asset_asset::site',
                'view_asset_asset::site',
                'create_asset_asset::site',
                'update_asset_asset::site',
                'delete_asset_asset::site',
                'delete_any_asset_asset::site',
                'restore_asset_asset::site',
                'restore_any_asset_asset::site',
                'force_delete_asset_asset::site',
                'force_delete_any_asset_asset::site',
                'view_any_asset_asset::warehouse',
                'view_asset_asset::warehouse',
                'view_any_asset_product',
                'view_asset_product',
                'view_any_asset_asset::category',
                'view_asset_asset::category',
                'view_any_asset_asset::movement',
                'view_asset_asset::movement',
            ],
            'Custody Officer' => [
                'view_any_asset_asset',
                'view_asset_asset',
                'update_asset_asset',
                'view_any_asset_asset::site',
                'view_asset_asset::site',
                'view_any_asset_asset::warehouse',
                'view_asset_asset::warehouse',
                'view_any_asset_product',
                'view_asset_product',
                'view_any_asset_asset::category',
                'view_asset_asset::category',
                'view_any_asset_asset::movement',
                'view_asset_asset::movement',
            ],
        ];
    }
}
