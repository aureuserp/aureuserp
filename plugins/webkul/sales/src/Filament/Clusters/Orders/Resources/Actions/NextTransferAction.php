<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\Actions;

use Webkul\Inventory\Filament\Clusters\Operations\Actions\NextTransferAction as BaseNextTransferAction;
use Webkul\Inventory\Models\Operation;

class NextTransferAction extends BaseNextTransferAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->url(function (Operation $record, $livewire): ?string {
            $nextTransfer = $record->nextTransfersQuery()->orderBy('id')->first();

            if (! $nextTransfer) {
                return null;
            }

            return $livewire->getResource()::getUrl('edit', ['record' => $nextTransfer], shouldGuessMissingParameters: true);
        });
    }
}
