<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\UserResource\Pages;

use Webkul\Crm\Filament\Clusters\Configurations\Resources\UserResource;
use Webkul\Security\Filament\Resources\UserResource\Pages\ListUsers as BaseListUsers;

class ListUsers extends BaseListUsers
{
    protected static string $resource = UserResource::class;
}
