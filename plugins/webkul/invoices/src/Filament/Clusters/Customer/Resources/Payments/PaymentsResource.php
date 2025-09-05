<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\Payments;

use Filament\Pages\Enums\SubNavigationPosition;
use Webkul\Account\Filament\Resources\Payments\PaymentsResource as BasePaymentsResource;
use Webkul\Invoice\Filament\Clusters\Customer;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\Payments\Pages\CreatePayments;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\Payments\Pages\EditPayments;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\Payments\Pages\ListPayments;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\Payments\Pages\ViewPayments;
use Webkul\Invoice\Models\Payment;

class PaymentsResource extends BasePaymentsResource
{
    protected static ?string $model = Payment::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = Customer::class;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getModelLabel(): string
    {
        return __('invoices::filament/clusters/customers/resources/payment.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/customers/resources/payment.navigation.title');
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
