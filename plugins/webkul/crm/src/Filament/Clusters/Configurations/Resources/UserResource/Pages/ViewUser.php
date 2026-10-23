<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\UserResource\Pages;

use Webkul\Crm\Filament\Clusters\Configurations\Resources\UserResource;
use Webkul\Crm\Filament\Concerns\HasCrmClusterNavigation;
use Webkul\Security\Filament\Resources\UserResource\Pages\ViewUsers as BaseViewUsers;

class ViewUser extends BaseViewUsers
{
    use HasCrmClusterNavigation;

    protected static string $resource = UserResource::class;
}
