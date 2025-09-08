<?php

namespace Webkul\FullCalendar\Filament\Actions;

use Filament\Actions\EditAction as BaseEditAction;
use Webkul\FullCalendar\Filament\Widgets\FullCalendarWidget;

class EditAction extends BaseEditAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->schema(fn(FullCalendarWidget $livewire) => $livewire->getFormSchema());

        $this->model(fn(FullCalendarWidget $livewire) => $livewire->getModel());

        $this->record(fn(FullCalendarWidget $livewire) => $livewire->getRecord());

        $this->schema(fn(FullCalendarWidget $livewire) => $livewire->getFormSchema());

        $this->after(fn(FullCalendarWidget $livewire) => $livewire->refreshRecords());

        $this->cancelParentActions();
    }
}
