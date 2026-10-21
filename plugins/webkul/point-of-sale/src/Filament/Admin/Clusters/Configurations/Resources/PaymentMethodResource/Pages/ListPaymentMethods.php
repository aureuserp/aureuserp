<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\PaymentMethodResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Webkul\PointOfSale\Enums\PaymentMethodType;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\PaymentMethodResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListPaymentMethods extends ListRecords
{
    use HasTableViews;

    protected static string $resource = PaymentMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method/pages/list-payment-methods.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method/pages/list-payment-methods.header-actions.create.notification.success.title'))
                        ->body(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method/pages/list-payment-methods.header-actions.create.notification.success.body')),
                ),
        ];
    }

    public function getPresetTableViews(): array
    {
        return [
            'all' => PresetView::make(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method/pages/list-payment-methods.tabs.all'))
                ->icon('heroicon-s-banknotes')
                ->favorite()
                ->setAsDefault()
                ->modifyQueryUsing(fn (Builder $query) => $query->withoutTrashed()),

            'cash' => PresetView::make(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method/pages/list-payment-methods.tabs.cash'))
                ->icon('heroicon-s-currency-rupee')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', PaymentMethodType::CASH)),

            'bank' => PresetView::make(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method/pages/list-payment-methods.tabs.bank'))
                ->icon('heroicon-s-credit-card')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', PaymentMethodType::BANK)),

            'archived' => PresetView::make(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method/pages/list-payment-methods.tabs.archived'))
                ->icon('heroicon-s-archive-box')
                ->modifyQueryUsing(fn (Builder $query) => $query->onlyTrashed()),
        ];
    }
}
