<?php

namespace Webkul\Account\Filament\Resources\CustomerResource\Pages;

use Filament\Actions\CreateAction;
use Webkul\Account\Filament\Resources\CustomerResource;
use Webkul\Account\Filament\Resources\PartnerResource\Pages\ListPartners;

class ListCustomers extends ListPartners
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('accounts::filament/resources/customer/pages/list-customers.header-actions.create.title'))
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
