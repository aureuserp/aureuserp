<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources\ActivityTypes\Pages;

use Webkul\Support\Filament\Resources\ActivityTypes\Pages\EditActivityType as BaseEditActivityType;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\ActivityTypes\ActivityTypeResource;

class EditActivityType extends BaseEditActivityType
{
    protected static string $resource = ActivityTypeResource::class;

    protected static ?string $pluginName = 'time-off';
}
