<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\EmploymentTypes\Pages;

use Webkul\Employee\Filament\Clusters\Configurations\Resources\EmploymentTypes\Pages\ListEmploymentTypes as BaseListEmploymentTypes;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\EmploymentTypes\EmploymentTypeResource;

class ListEmploymentTypes extends BaseListEmploymentTypes
{
    protected static string $resource = EmploymentTypeResource::class;
}
