<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Actions;

use Filament\Actions\Action;
use Livewire\Component;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource;
use Webkul\Inventory\Models\Operation;

class ReturnAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'inventories.operations.return';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('inventories::filament/clusters/operations/actions/return.label'))
            ->color('gray')
            ->requiresConfirmation()
            ->action(function (Operation $record, Component $livewire) {
                $newRecord = Inventory::returnTransfer($record);

                $livewire->updateForm();

                return redirect()->to(OperationResource::getUrl('edit', ['record' => $newRecord]));
            })
            ->visible(fn () => $this->getRecord()->state == OperationState::DONE);
    }
}
