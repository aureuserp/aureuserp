<?php

namespace Webkul\Account\Filament\Resources\VendorResource\Pages;

use Filament\Actions\CreateAction;
use Webkul\Account\Filament\Resources\PartnerResource\Pages\ListPartners;
use Webkul\Account\Filament\Resources\VendorResource;

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

    public function getPresetTableViews(): array
    {
        $views = parent::getPresetTableViews();

        unset($views['employees'], $views['customers'], $views['vendors']);

        return $views;
    }
}
