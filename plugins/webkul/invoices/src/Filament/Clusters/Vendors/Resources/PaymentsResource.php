<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources;

use Filament\Pages\SubNavigationPosition;
use Webkul\Account\Filament\Resources\PaymentsResource as BasePaymentsResource;
use Webkul\Invoice\Filament\Clusters\Vendors;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\PaymentsResource\Pages;
use Webkul\Invoice\Models\Payment;

class PaymentsResource extends BasePaymentsResource
{
    protected static ?string $model = Payment::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 3;

    protected static ?string $cluster = Vendors::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getModelLabel(): string
    {
        return __('invoices::filament/clusters/vendors/resources/payment.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/vendors/resources/payment.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayments::route('/create'),
            'view'   => Pages\ViewPayments::route('/{record}'),
            'edit'   => Pages\EditPayments::route('/{record}/edit'),
        ];
    }
}
