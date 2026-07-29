<?php

namespace Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\WorkCenterResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Support\Facades\Auth;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\WorkCenterResource;
use Webkul\Manufacturing\Models\WorkCenter;

class ListWorkCenters extends ListRecords
{
    protected static string $resource = WorkCenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('manufacturing::filament/clusters/configurations/resources/work-center/pages/list-work-centers.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->mutateDataUsing(function (array $data): array {
                    $user = Auth::user();

                    $data['creator_id'] = $user?->id;
                    $data['company_id'] ??= $user?->default_company_id;

                    return $data;
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('manufacturing::filament/clusters/configurations/resources/work-center/pages/list-work-centers.header-actions.create.notification.title'))
                        ->body(__('manufacturing::filament/clusters/configurations/resources/work-center/pages/list-work-centers.header-actions.create.notification.body')),
                ),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('manufacturing::filament/clusters/configurations/resources/work-center/pages/list-work-centers.tabs.all'))
                ->badge(WorkCenter::count())
                ->modifyQueryUsing(function ($query) {
                    return $query->withoutTrashed();
                }),
            'archived' => Tab::make(__('manufacturing::filament/clusters/configurations/resources/work-center/pages/list-work-centers.tabs.archived'))
                ->badge(WorkCenter::onlyTrashed()->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->onlyTrashed();
                }),
        ];
    }
}
