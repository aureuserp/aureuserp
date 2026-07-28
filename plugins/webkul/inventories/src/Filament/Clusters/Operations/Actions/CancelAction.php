<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Livewire\Component;
use Throwable;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Models\Operation;

class CancelAction extends Action
{
    protected bool|Closure $hasDatabaseTransactions = true;

    public static function getDefaultName(): ?string
    {
        return 'inventories.operations.cancel';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('inventories::filament/clusters/operations/actions/cancel.label'))
            ->color('gray')
            ->requiresConfirmation()
            ->action(function (Operation $record, Component $livewire): void {
                try {
                    Inventory::cancelTransfer($record);

                    $livewire->updateForm();
                } catch (Throwable $e) {
                    Notification::make()
                        ->danger()
                        ->body($e->getMessage())
                        ->send();

                    $livewire->unmountAction();

                    $this->halt(shouldRollBackDatabaseTransaction: true);
                }
            })
            ->visible(fn () => ! in_array($this->getRecord()->state, [
                OperationState::DONE,
                OperationState::CANCELED,
            ]));
    }
}
