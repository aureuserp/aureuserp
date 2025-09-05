<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\Payments;

use Filament\Pages\Enums\SubNavigationPosition;
use Webkul\Account\Filament\Resources\Payments\PaymentsResource as BasePaymentsResource;
use Webkul\Invoice\Filament\Clusters\Vendors;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\Payments\Pages\CreatePayments;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\Payments\Pages\EditPayments;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\Payments\Pages\ListPayments;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\Payments\Pages\ViewPayments;
use Webkul\Invoice\Models\Payment;

class PaymentsResource extends BasePaymentsResource
{
    protected static ?string $model = Payment::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 3;

    protected static ?string $cluster = Vendors::class;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

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
            'index'  => ListPayments::route('/'),
            'create' => CreatePayments::route('/create'),
            'view'   => ViewPayments::route('/{record}'),
            'edit'   => EditPayments::route('/{record}/edit'),
        ];
    }
}
