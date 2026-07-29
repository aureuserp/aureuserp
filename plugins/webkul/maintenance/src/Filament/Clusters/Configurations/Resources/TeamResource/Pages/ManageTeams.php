<?php

namespace Webkul\Maintenance\Filament\Clusters\Configurations\Resources\TeamResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Webkul\Maintenance\Filament\Clusters\Configurations\Resources\TeamResource;
use Webkul\Maintenance\Models\Team;

class ManageTeams extends ManageRecords
{
    protected static string $resource = TeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('maintenance::filament/clusters/configurations/resources/team/pages/manage-teams.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('maintenance::filament/clusters/configurations/resources/team/pages/manage-teams.header-actions.create.notification.title'))
                        ->body(__('maintenance::filament/clusters/configurations/resources/team/pages/manage-teams.header-actions.create.notification.body')),
                ),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('maintenance::filament/clusters/configurations/resources/team/pages/manage-teams.tabs.all'))
                ->badge(Team::count()),
            'archived' => Tab::make(__('maintenance::filament/clusters/configurations/resources/team/pages/manage-teams.tabs.archived'))
                ->badge(Team::onlyTrashed()->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->onlyTrashed();
                }),
        ];
    }
}
