<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Actions;

use Closure;
use Filament\Actions\Action;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource;
use Webkul\Inventory\Models\Operation;

class NextTransferAction extends Action
{
    protected bool|Closure $hasDatabaseTransactions = false;

    public static function getDefaultName(): ?string
    {
        return 'inventories.operations.next-transfer';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('inventories::filament/clusters/operations/actions/next-transfer.label'))
            ->icon('heroicon-o-arrow-right-circle')
            ->color('gray')
            ->url(function (Operation $record): ?string {
                $nextTransfer = $record->nextTransfersQuery()->orderBy('id')->first();

                if (! $nextTransfer) {
                    return null;
                }

                return OperationResource::getUrl('edit', ['record' => $nextTransfer]);
            })
            ->visible(fn (Operation $record): bool => $record->show_next_operations);
    }
}
