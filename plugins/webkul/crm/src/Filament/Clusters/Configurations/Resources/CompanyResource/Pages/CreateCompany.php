<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\CompanyResource\Pages;

use Webkul\Crm\Filament\Clusters\Configurations\Resources\CompanyResource;
use Webkul\Crm\Filament\Concerns\HasCrmClusterNavigation;
use Webkul\Security\Filament\Resources\CompanyResource\Pages\CreateCompany as BaseCreateCompany;

class CreateCompany extends BaseCreateCompany
{
    use HasCrmClusterNavigation;

    protected static string $resource = CompanyResource::class;
}
