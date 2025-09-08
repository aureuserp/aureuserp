<?php

namespace Webkul\FullCalendar\Filament\Actions;

use Filament\Actions\DeleteAction as BaseDeleteAction;
use Illuminate\Database\Eloquent\Model;
use Webkul\FullCalendar\Filament\Widgets\FullCalendarWidget;

class DeleteAction extends BaseDeleteAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->model(fn(FullCalendarWidget $livewire) => $livewire->getModel());

        $this->record(fn(FullCalendarWidget $livewire) => $livewire->getRecord());

        $this->after(
            function (FullCalendarWidget $livewire) {
                $livewire->record = null;
                $livewire->refreshRecords();
            }
        );

        $this->hidden(static function (?Model $record): bool {
            if (! $record) {
                return true;
            }

            if (! method_exists($record, 'trashed')) {
                return false;
            }

            return $record->trashed();
        });

        $this->cancelParentActions();
    }
}
