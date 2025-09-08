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

        $this
            ->model(fn (FullCalendarWidget $livewire) => $livewire->getModel())
            ->record(fn (FullCalendarWidget $livewire) => $livewire->getRecord())
            ->after(
                function (FullCalendarWidget $livewire) {
                    $livewire->record = null;
                    $livewire->refreshRecords();
                }
            )
            ->successNotification(null)
            ->hidden(static function (?Model $record): bool {
                if (! $record) {
                    return true;
                }

                if (! method_exists($record, 'trashed')) {
                    return false;
                }

                return $record->trashed();
            })
            ->cancelParentActions();
    }
}
