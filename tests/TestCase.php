<?php

namespace Tests;

use Spatie\Permission\Models\Role;
use Webkul\Support\Models\Company;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Webkul\Security\Settings\UserSettings;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use BezhanSalleh\FilamentShield\Support\Utils as ShieldUtils;

abstract class TestCase extends BaseTestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh');
        app(UserSettings::class)->default_company_id = 1;
        Artisan::call('shield:generate', [
            '--all'    => true,
            '--option' => 'permissions',
            '--panel'  => 'admin',
        ]);

        $adminRole = Role::firstOrCreate([
            'name'       => ShieldUtils::getPanelUserRoleName(),
            'is_default' => true,
        ]);
        $adminRole->syncPermissions(Permission::all());
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    protected function makeAdminUser()
    {
        $userModel = app(ShieldUtils::getAuthProviderFQCN());

        $adminUser = $userModel::factory()
            ->for(Company::factory(), 'defaultCompany')
            ->create();
        $adminUser->assignRole(ShieldUtils::getPanelUserRoleName());

        return $adminUser;
    }
}
