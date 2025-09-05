<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityTypes\Pages;

use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityTypes\ActivityTypeResource;
use Webkul\Support\Filament\Resources\ActivityTypes\Pages\CreateActivityType as BaseCreateActivityType;

class CreateActivityType extends BaseCreateActivityType
{
    protected static string $resource = ActivityTypeResource::class;
}
