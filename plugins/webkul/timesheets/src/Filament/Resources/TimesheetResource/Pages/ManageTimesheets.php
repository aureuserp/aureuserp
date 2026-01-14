<?php

namespace Webkul\Timesheet\Filament\Resources\TimesheetResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Auth;
use Webkul\Project\Models\Timesheet;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;
use Webkul\Timesheet\Filament\Resources\TimesheetResource;

class ManageTimesheets extends ManageRecords
{
    use HasTableViews;

    protected static string $resource = TimesheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('timesheets::filament/resources/timesheet/manage-timesheets.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->mutateDataUsing(function (array $data): array {
                    $data['creator_id'] = Auth::id();

                    return $data;
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('timesheets::filament/resources/timesheet/manage-timesheets.header-actions.create.notification.title'))
                        ->body(__('timesheets::filament/resources/timesheet/manage-timesheets.header-actions.create.notification.body')),
                ),
        ];
    }

    public function getPresetTableViews(): array
    {
        return [
            'my_timesheets' => PresetView::make(__('timesheets::filament/resources/timesheet/manage-timesheets.tabs.my-timesheets'))
                ->badge(fn (): int => Timesheet::where('user_id', Auth::id())->count())
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(function ($query) {
                    return $query->where('user_id', Auth::id());
                })
                ->favorite(),
        ];
    }
}
