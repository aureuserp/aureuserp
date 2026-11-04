<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\RevealRuleResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RevealRuleResource;
use Webkul\Crm\Filament\Concerns\HasCRMClusterNavigation;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewRevealRule extends ViewRecord
{
    use HasCRMClusterNavigation, HasRecordNavigationTabs {
        HasCRMClusterNavigation::getSubNavigation insteadof HasRecordNavigationTabs;
        HasCRMClusterNavigation::getSubNavigationPosition insteadof HasRecordNavigationTabs;
    }

    protected static string $resource = RevealRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
