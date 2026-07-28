<?php

use Filament\Facades\Filament;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

require_once __DIR__.'/SecurityHelper.php';

class FilamentHelper
{
    public static function bootAdminPanel(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Filament::bootCurrentPanel();
    }

    public static function actingAs(array $permissions = [], bool $global = true): User
    {
        $user = SecurityHelper::authenticateWithPermissions($permissions);

        $attributes = ['default_company_id' => Company::query()->value('id')];

        if ($global) {
            $attributes['resource_permission'] = PermissionType::GLOBAL;
        }

        $user->forceFill($attributes)->saveQuietly();

        static::bootAdminPanel();

        return $user;
    }
}
