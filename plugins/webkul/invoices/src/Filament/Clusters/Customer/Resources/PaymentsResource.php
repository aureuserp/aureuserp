<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources;

use Webkul\Account\Filament\Resources\PaymentsResource as BasePaymentsResource;
use Webkul\Invoice\Filament\Clusters\Customer;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\PaymentsResource\Pages\CreatePayments;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\PaymentsResource\Pages\EditPayments;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\PaymentsResource\Pages\ListPayments;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\PaymentsResource\Pages\ViewPayments;
use Webkul\Invoice\Models\Payment;

class PaymentsResource extends BasePaymentsResource
{
    protected static ?string $model = Payment::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = Customer::class;

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
