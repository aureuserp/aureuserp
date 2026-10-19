<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource as ConfigurationTeamResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource;

class ListTeams extends ListRecords
{
    protected static string $resource = TeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label(__('crm::filament/clusters/sales/resources/team/pages/list-teams.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->url(fn (): string => ConfigurationTeamResource::getUrl('create'))
                ->visible(fn (): bool => ConfigurationTeamResource::canCreate()),
        ];
    }
}
