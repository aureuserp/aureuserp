<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityTypeResource\Pages;

use Filament\Actions\ViewAction;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityTypeResource;
use Webkul\Support\Filament\Resources\ActivityTypeResource\Pages\EditActivityType as BaseEditActivityType;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditActivityType extends BaseEditActivityType
{
    use HasRecordNavigationTabs;

    protected static string $resource = ActivityTypeResource::class;

    protected static ?string $pluginName = 'crm';

    protected function getHeaderActions(): array
    {
        return array_values(array_filter(
            parent::getHeaderActions(),
            fn ($action): bool => ! $action instanceof ViewAction,
        ));
    }
}
