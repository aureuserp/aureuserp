<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Actions\Print;

use Filament\Actions\Action;
use Livewire\Component;
use Webkul\Inventory\Models\Operation;

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
            ->action(function (Operation $record, Component $livewire): void {});
    }
}
