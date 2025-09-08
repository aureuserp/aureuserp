<?php

namespace Webkul\FullCalendar\Filament\Actions;

use Filament\Actions\ViewAction as BaseViewAction;
use Webkul\FullCalendar\Filament\Widgets\FullCalendarWidget;

class ViewAction extends BaseViewAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->model(fn(FullCalendarWidget $livewire) => $livewire->getModel())
            ->record(fn(FullCalendarWidget $livewire) => $livewire->getRecord())
            ->schema(fn(FullCalendarWidget $livewire) => $livewire->getInfolistSchema())
            ->modalFooterActions(fn(ViewAction $action, FullCalendarWidget $livewire) => [
                ...$livewire->getCachedModalActions(),
                $action->getModalCancelAction(),
            ])
            ->after(fn(FullCalendarWidget $livewire) => $livewire->refreshRecords())
            ->cancelParentActions();
    }
}
