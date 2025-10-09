<?php

namespace Webkul\Accounting\Filament\Clusters\Customer\Resources;

use Webkul\Account\Filament\Resources\PaymentsResource as BasePaymentsResource;
use Webkul\Accounting\Filament\Clusters\Customer;
use Webkul\Accounting\Filament\Clusters\Customer\Resources\PaymentsResource\Pages\CreatePayments;
use Webkul\Accounting\Filament\Clusters\Customer\Resources\PaymentsResource\Pages\EditPayments;
use Webkul\Accounting\Filament\Clusters\Customer\Resources\PaymentsResource\Pages\ListPayments;
use Webkul\Accounting\Filament\Clusters\Customer\Resources\PaymentsResource\Pages\ViewPayments;
use Webkul\Accounting\Models\Payment;

class PaymentsResource extends BasePaymentsResource
{
    protected static ?string $model = Payment::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = Customer::class;

    public static function getModelLabel(): string
    {
        return __('accounting::filament/clusters/customers/resources/payment.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/customers/resources/payment.navigation.title');
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
