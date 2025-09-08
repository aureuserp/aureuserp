<?php

namespace Webkul\FullCalendar\Filament\Actions;

use Filament\Actions\CreateAction as BaseCreateAction;
use Webkul\FullCalendar\Filament\Widgets\FullCalendarWidget;

class CreateAction extends BaseCreateAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->model(fn (FullCalendarWidget $livewire) => $livewire->getModel())
            ->schema(fn (FullCalendarWidget $livewire) => $livewire->getFormSchema())
            ->after(fn (FullCalendarWidget $livewire) => $livewire->refreshRecords())
            ->cancelParentActions();
    }
}
