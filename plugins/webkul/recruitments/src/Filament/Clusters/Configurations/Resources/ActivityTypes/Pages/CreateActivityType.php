<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ActivityTypes\Pages;

use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ActivityTypes\ActivityTypeResource;
use Webkul\Support\Filament\Resources\ActivityTypes\Pages\CreateActivityType as BaseCreateActivityType;

class CreateActivityType extends BaseCreateActivityType
{
    protected static string $resource = ActivityTypeResource::class;

    protected static ?string $pluginName = 'recruitments';
}
