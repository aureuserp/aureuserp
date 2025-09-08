<?php

namespace Webkul\FullCalendar\Filament\Actions;

use Filament\Actions\ViewAction as BaseViewAction;
use Webkul\FullCalendar\Filament\Widgets\FullCalendarWidget;

class ViewAction extends BaseViewAction
{
    protected function setUp(): void
    {
        $this
            ->model(fn (FullCalendarWidget $livewire) => $livewire->getModel())
            ->record(fn (FullCalendarWidget $livewire) => $livewire->getRecord())
            ->schema(fn (FullCalendarWidget $livewire) => $livewire->getInfolistSchema())
            ->modalFooterActions(function (ViewAction $action, FullCalendarWidget $livewire) {
                return [
                    ...$livewire->modalActions(),
                    $action->getModalCancelAction(),
                ];
            })
            ->cancelParentActions('view')
            ->after(fn (FullCalendarWidget $livewire) => $livewire->refreshRecords());
    }
}
