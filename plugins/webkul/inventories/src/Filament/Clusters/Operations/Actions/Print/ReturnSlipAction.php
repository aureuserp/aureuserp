<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Actions\Print;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Livewire\Component;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Facades\Inventory;

class ReturnSlipAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'inventories.operations.print.return-slip';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('inventories::filament/clusters/operations/actions/todo.label'))
            ->action(function (Operation $record, Component $livewire): void {
                if (! $record->moves->count()) {
                    Notification::make()
                        ->success()
                        ->title(__('inventories::filament/clusters/operations/actions/todo.notification.warning.title'))
                        ->body(__('inventories::filament/clusters/operations/actions/todo.notification.warning.body'))
                        ->warning()
                        ->send();

                    return;
                }

                foreach ($record->moves as $move) {
                    Inventory::updateOrCreateMoveLines($move);
                }

                Inventory::computeTransferState($record);

                $livewire->updateForm();

                Notification::make()
                    ->success()
                    ->title(__('inventories::filament/clusters/operations/actions/todo.notification.success.title'))
                    ->body(__('inventories::filament/clusters/operations/actions/todo.notification.success.body'))
                    ->success()
                    ->send();
            });
    }
}
