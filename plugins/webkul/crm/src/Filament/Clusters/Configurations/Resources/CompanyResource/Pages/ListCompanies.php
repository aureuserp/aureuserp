<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\CompanyResource\Pages;

use Webkul\Crm\Filament\Clusters\Configurations\Resources\CompanyResource;
use Webkul\Security\Filament\Resources\CompanyResource\Pages\ListCompanies as BaseListCompanies;

class ListCompanies extends BaseListCompanies
{
    protected static string $resource = CompanyResource::class;
}
