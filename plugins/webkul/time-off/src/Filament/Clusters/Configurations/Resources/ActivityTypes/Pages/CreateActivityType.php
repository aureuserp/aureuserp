<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources\ActivityTypes\Pages;

use Webkul\Support\Filament\Resources\ActivityTypes\Pages\CreateActivityType as BaseCreateActivityType;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\ActivityTypes\ActivityTypeResource;

class CreateActivityType extends BaseCreateActivityType
{
    protected static string $resource = ActivityTypeResource::class;

    protected static ?string $pluginName = 'time-off';
}
