<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources\LeaveTypeResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\LeaveTypeResource;
use Webkul\TimeOff\Models\LeaveType;

class ListLeaveTypes extends ListRecords
{
    protected static string $resource = LeaveTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('time-off::filament/clusters/configurations/resources/leave-type/pages/list-leave-type.header-actions.new-leave-type'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('time-off::filament/clusters/configurations/resources/leave-type/pages/list-leave-type.tabs.all'))
                ->badge(LeaveType::whereNull('deleted_at')->count()),
            'archived' => Tab::make(__('time-off::filament/clusters/configurations/resources/leave-type/pages/list-leave-type.tabs.archived'))
                ->badge(LeaveType::onlyTrashed()->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->onlyTrashed();
                }),
        ];
    }
}
