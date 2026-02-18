<?php

namespace Webkul\Project\Filament\Clusters\Configurations\Resources\ActivityPlanResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Webkul\Project\Filament\Clusters\Configurations\Resources\ActivityPlanResource;
use Webkul\Support\Models\ActivityPlan;

class ListActivityPlans extends ListRecords
{
    protected static string $resource = ActivityPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('projects::filament/clusters/configurations/resources/activity-plan/pages/list-activity-plans.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->mutateDataUsing(function ($data) {
                    $data['plugin'] = 'projects';

                    return $data;
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('projects::filament/clusters/configurations/resources/activity-plan/pages/list-activity-plans.header-actions.create.notification.title'))
                        ->body(__('projects::filament/clusters/configurations/resources/activity-plan/pages/list-activity-plans.header-actions.create.notification.body')),
                ),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('projects::filament/clusters/configurations/resources/activity-plan/pages/list-activity-plans.tabs.all'))
                ->badge(ActivityPlan::where('plugin', 'projects')->count()),
            'archived' => Tab::make(__('projects::filament/clusters/configurations/resources/activity-plan/pages/list-activity-plans.tabs.archived'))
                ->badge(ActivityPlan::where('plugin', 'projects')->onlyTrashed()->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->onlyTrashed();
                }),
        ];
    }
}
