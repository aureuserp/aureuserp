<?php

namespace Webkul\Security\Filament\Resources\CompanyResource\Pages;

use Webkul\Security\Filament\Resources\CompanyResource;
use Webkul\Support\Filament\Resources\CompanyResource\Pages\ListCompanies as BaseListCompanies;

class ListCompanies extends BaseListCompanies
{
    protected static string $resource = CompanyResource::class;
}
