<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Actions;

use Filament\Actions\Action;
use Livewire\Component;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Models\Operation;

class CancelAction extends Action
{
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
            ->action(function (Operation $record, Component $livewire): void {
                $record = Inventory::cancelTransfer($record);

                $livewire->updateForm();
            })
            ->visible(fn () => ! in_array($this->getRecord()->state, [
                OperationState::DONE,
                OperationState::CANCELED,
            ]));
    }
}
