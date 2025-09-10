<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityTypes\Pages;

use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityTypes\ActivityTypeResource;
use Webkul\Support\Filament\Resources\ActivityTypes\Pages\ListActivityTypes as BaseListActivityTypes;

class ListActivityTypes extends BaseListActivityTypes
{
    protected static string $resource = ActivityTypeResource::class;
}
