<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityTypes\Pages;

use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityTypes\ActivityTypeResource;
use Webkul\Support\Filament\Resources\ActivityTypes\Pages\EditActivityType as BaseEditActivityType;

class EditActivityType extends BaseEditActivityType
{
    protected static string $resource = ActivityTypeResource::class;
}
