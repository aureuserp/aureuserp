<?php

namespace Webkul\FullCalendar\Filament\Actions;

use Filament\Actions\ViewAction as BaseViewAction;
use Webkul\FullCalendar\Filament\Widgets\FullCalendarWidget;

class ViewAction extends BaseViewAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->model(fn (FullCalendarWidget $livewire) => $livewire->getModel());

        // $this->schema(fn (FullCalendarWidget $livewire) => $livewire->getFormSchema());

        $this->modalFooterActions(fn (ViewAction $action, FullCalendarWidget $livewire) => [
            ...$livewire->getCachedModalActions(),
            $action->getModalCancelAction(),
        ]);

        $this->after(fn (FullCalendarWidget $livewire) => $livewire->refreshRecords());

        $this->cancelParentActions();
    }
}
