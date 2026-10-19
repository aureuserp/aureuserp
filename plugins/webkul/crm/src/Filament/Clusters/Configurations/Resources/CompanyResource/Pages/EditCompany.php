<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\CompanyResource\Pages;

use Webkul\Crm\Filament\Clusters\Configurations\Resources\CompanyResource;
use Webkul\Crm\Filament\Concerns\HasCrmClusterNavigation;
use Webkul\Security\Filament\Resources\CompanyResource\Pages\EditCompany as BaseEditCompany;

class EditCompany extends BaseEditCompany
{
    use HasCrmClusterNavigation;

    protected static string $resource = CompanyResource::class;
}
