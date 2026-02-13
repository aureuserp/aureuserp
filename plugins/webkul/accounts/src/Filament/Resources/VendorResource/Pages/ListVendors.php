<?php

namespace Webkul\Account\Filament\Resources\VendorResource\Pages;

use Filament\Actions\CreateAction;
use Webkul\Account\Filament\Resources\VendorResource;
use Webkul\Account\Filament\Resources\PartnerResource\Pages\ListPartners;

class ListVendors extends ListPartners
{
    protected static string $resource = VendorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('accounts::filament/resources/vendor/pages/list-vendors.header-actions.create.title'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
