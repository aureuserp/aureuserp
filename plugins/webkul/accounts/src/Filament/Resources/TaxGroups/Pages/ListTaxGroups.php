<?php

namespace Webkul\Account\Filament\Resources\TaxGroups\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\Account\Filament\Resources\TaxGroups\TaxGroupResource;

class ListTaxGroups extends ListRecords
{
    protected static string $resource = TaxGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
