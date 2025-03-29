<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Actions;

use Filament\Actions\Action;
use Livewire\Component;
use Webkul\Inventory\Enums;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Facades\Inventory;

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
                if (! in_array($this->getRecord()->state, [Enums\OperationState::CONFIRMED, Enums\OperationState::ASSIGNED])) {
                    return true;
                }

                return ! $this->getRecord()->moves->contains(fn ($move) => in_array($move->state, [Enums\MoveState::CONFIRMED, Enums\MoveState::PARTIALLY_ASSIGNED]));
            });
    }
}
