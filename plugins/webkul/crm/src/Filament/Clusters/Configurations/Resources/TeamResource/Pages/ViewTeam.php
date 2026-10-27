<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages;

use Filament\Actions\EditAction;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource;
use Webkul\Crm\Filament\Concerns\HasCRMClusterNavigation;
use Webkul\Crm\Filament\Concerns\ManagesCRMTeam;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\TeamResource\Pages\ViewTeam as BaseViewTeam;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewTeam extends BaseViewTeam
{
    use HasCRMClusterNavigation, HasRecordNavigationTabs, ManagesCRMTeam {
        HasCRMClusterNavigation::getSubNavigation insteadof HasRecordNavigationTabs;
        HasCRMClusterNavigation::getSubNavigationPosition insteadof HasRecordNavigationTabs;
        ManagesCRMTeam::getHeaderActions as getTeamHeaderActions;
    }

    protected static string $resource = TeamResource::class;

    protected function getHeaderActions(): array
    {
        return array_values(array_filter(
            $this->getTeamHeaderActions(),
            fn ($action): bool => ! $action instanceof EditAction,
        ));
    }
}
