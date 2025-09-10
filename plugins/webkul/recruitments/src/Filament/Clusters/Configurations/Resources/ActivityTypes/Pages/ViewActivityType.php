<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ActivityTypes\Pages;

use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ActivityTypes\ActivityTypeResource;
use Webkul\Support\Filament\Resources\ActivityTypes\Pages\ViewActivityType as BaseViewActivityType;

class ViewActivityType extends BaseViewActivityType
{
    protected static string $resource = ActivityTypeResource::class;
}
