<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Livewire\Component;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Models\Operation;

class TodoAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'inventories.operations.todo';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('inventories::filament/clusters/operations/actions/todo.label'))
            ->action(function (Operation $record, Component $livewire): void {
                if (! $record->moves->count()) {
                    Notification::make()
                        ->title(__('inventories::filament/clusters/operations/actions/todo.notification.warning.title'))
                        ->body(__('inventories::filament/clusters/operations/actions/todo.notification.warning.body'))
                        ->warning()
                        ->send();

                    return;
                }

                $record = Inventory::todoTransfer($record);

                $livewire->updateForm();

                Notification::make()
                    ->success()
                    ->title(__('inventories::filament/clusters/operations/actions/todo.notification.success.title'))
                    ->body(__('inventories::filament/clusters/operations/actions/todo.notification.success.body'))
                    ->success()
                    ->send();
            })
            ->hidden(fn () => $this->getRecord()->state !== OperationState::DRAFT);
    }
}
