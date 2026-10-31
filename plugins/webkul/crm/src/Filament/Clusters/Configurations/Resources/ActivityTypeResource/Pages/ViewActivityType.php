<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityTypeResource\Pages;

use Filament\Actions\EditAction;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityTypeResource;
use Webkul\Support\Filament\Resources\ActivityTypeResource\Pages\ViewActivityType as BaseViewActivityType;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewActivityType extends BaseViewActivityType
{
    use HasRecordNavigationTabs;

    protected static string $resource = ActivityTypeResource::class;

    protected function getHeaderActions(): array
    {
        return array_values(array_filter(
            parent::getHeaderActions(),
            fn ($action): bool => ! $action instanceof EditAction,
        ));
    }
}
