<?php

namespace Webkul\Account\Filament\Resources\BillResource\Pages;

use Filament\Actions\CreateAction;
use Webkul\Account\Filament\Resources\BillResource;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\ListInvoices as BaseListBills;

class ListBills extends BaseListBills
{
    protected static string $resource = BillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
