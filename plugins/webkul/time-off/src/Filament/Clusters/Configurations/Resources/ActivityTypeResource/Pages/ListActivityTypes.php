<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources\ActivityTypeResource\Pages;

use Webkul\Support\Filament\Resources\ActivityTypes\Pages\ListActivityTypes as BaseListActivityTypes;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\ActivityTypeResource;

class ListActivityTypes extends BaseListActivityTypes
{
    protected static string $resource = ActivityTypeResource::class;

    protected static ?string $pluginName = 'time-off';
}
