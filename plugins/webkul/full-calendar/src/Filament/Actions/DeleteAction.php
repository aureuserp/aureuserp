<?php

namespace Webkul\FullCalendar\Filament\Actions;

use Filament\Actions\DeleteAction as BaseDeleteAction;
use Webkul\FullCalendar\Filament\Widgets\FullCalendarWidget;

class DeleteAction extends BaseDeleteAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->model(fn (FullCalendarWidget $livewire) => $livewire->getModel());

        $this->after(fn (FullCalendarWidget $livewire) => $livewire->refreshRecords());

        $this->cancelParentActions();
    }
}
