<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ActivityTypes\Pages;

use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ActivityTypes\ActivityTypeResource;
use Webkul\Support\Filament\Resources\ActivityTypes\Pages\ListActivityTypes as BaseListActivityTypes;

class ListActivityTypes extends BaseListActivityTypes
{
    protected static string $resource = ActivityTypeResource::class;

    protected static ?string $pluginName = 'recruitments';
}
