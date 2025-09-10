<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ActivityTypes\Pages;

use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ActivityTypes\ActivityTypeResource;
use Webkul\Support\Filament\Resources\ActivityTypes\Pages\EditActivityType as BaseEditActivityType;

class EditActivityType extends BaseEditActivityType
{
    protected static string $resource = ActivityTypeResource::class;

    protected static ?string $pluginName = 'recruitments';
}
