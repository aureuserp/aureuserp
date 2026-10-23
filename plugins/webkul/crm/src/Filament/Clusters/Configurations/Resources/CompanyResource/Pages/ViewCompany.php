<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\CompanyResource\Pages;

use Webkul\Crm\Filament\Clusters\Configurations\Resources\CompanyResource;
use Webkul\Crm\Filament\Concerns\HasCrmClusterNavigation;
use Webkul\Security\Filament\Resources\CompanyResource\Pages\ViewCompany as BaseViewCompany;

class ViewCompany extends BaseViewCompany
{
    use HasCrmClusterNavigation;

    protected static string $resource = CompanyResource::class;
}
