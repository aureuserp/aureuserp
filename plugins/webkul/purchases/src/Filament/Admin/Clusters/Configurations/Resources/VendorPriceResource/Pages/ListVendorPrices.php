<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\VendorPriceResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\VendorPriceResource;

class ListVendorPrices extends ListRecords
{
    protected static string $resource = VendorPriceResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('purchases::filament/admin/clusters/configurations/resources/vendor-price/pages/list-vendor-prices.navigation.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price/pages/list-vendor-prices.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->mutateDataUsing(function ($data) {
                    $user = Auth::user();

                    $data['creator_id'] = $user->id;

                    $data['company_id'] = $user->default_company_id;

                    return $data;
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('purchases::filament/admin/clusters/configurations/resources/vendor-price/pages/list-vendor-prices.header-actions.create.notification.title'))
                        ->body(__('purchases::filament/admin/clusters/configurations/resources/vendor-price/pages/list-vendor-prices.header-actions.create.notification.body')),
                ),
        ];
    }
}
