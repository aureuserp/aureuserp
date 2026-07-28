<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Livewire\Component;
use Throwable;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Models\Operation;

class CheckAvailabilityAction extends Action
{
    protected bool|Closure $hasDatabaseTransactions = true;

    public static function getDefaultName(): ?string
    {
        return 'inventories.operations.check_availability';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('inventories::filament/clusters/operations/actions/check-availability.label'))
            ->action(function (Operation $record, Component $livewire): void {
                try {
                    $record = Inventory::assignTransfer($record);

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
            ->hidden(function () {
                if (! in_array($this->getRecord()->state, [OperationState::WAITING, OperationState::CONFIRMED, OperationState::ASSIGNED])) {
                    return true;
                }

                return ! $this->getRecord()->moves->contains(fn ($move) => in_array($move->state, [
                    MoveState::WAITING,
                    MoveState::CONFIRMED,
                    MoveState::PARTIALLY_ASSIGNED,
                ]));
            });
    }
}
