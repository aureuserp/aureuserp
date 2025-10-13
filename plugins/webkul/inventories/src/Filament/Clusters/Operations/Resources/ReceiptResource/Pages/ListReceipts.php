<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListReceipts extends ListRecords
{
    use HasTableViews;

    protected static string $resource = ReceiptResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('inventories::filament/clusters/operations/resources/receipt.navigation.title');
    }

    public function getPresetTableViews(): array
    {
        return OperationResource::getPresetTableViews();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('inventories::filament/clusters/operations/resources/receipt/pages/list-receipts.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
