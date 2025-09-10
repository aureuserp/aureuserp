<?php

namespace Webkul\Contact\Filament\Clusters\Configurations\Resources\Industries\Pages;

use Webkul\Contact\Filament\Clusters\Configurations\Resources\Industries\IndustryResource;
use Webkul\Partner\Filament\Resources\Industries\Pages\ManageIndustries as BaseManageIndustries;

class ManageIndustries extends BaseManageIndustries
{
    protected static string $resource = IndustryResource::class;
}
