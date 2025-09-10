<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Actions;

use Filament\Actions\Action;
use Livewire\Component;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Models\Operation;

class CheckAvailabilityAction extends Action
{
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
                $record = Inventory::checkTransferAvailability($record);

                $livewire->updateForm();
            })
            ->hidden(function () {
                if (! in_array($this->getRecord()->state, [OperationState::CONFIRMED, OperationState::ASSIGNED])) {
                    return true;
                }

                return ! $this->getRecord()->moves->contains(fn ($move) => in_array($move->state, [MoveState::CONFIRMED, MoveState::PARTIALLY_ASSIGNED]));
            });
    }
}
