<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources;

use Webkul\Account\Filament\Resources\PaymentTermResource as BasePaymentTermResource;
use Webkul\Invoice\Filament\Clusters\Configuration;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\PaymentTermResource\Pages;
use Webkul\Invoice\Models\PaymentTerm;

class PaymentTermResource extends BasePaymentTermResource
{
    protected static ?string $model = PaymentTerm::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Configuration::class;

    public static function getModelLabel(): string
    {
        return __('invoices::filament/clusters/configurations/resources/payment-term.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/configurations/resources/payment-term.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('invoices::filament/clusters/configurations/resources/payment-term.navigation.group');
    }

    public static function getPages(): array
    {
        return [
            'index'             => Pages\ListPaymentTerms::route('/'),
            'create'            => Pages\CreatePaymentTerm::route('/create'),
            'view'              => Pages\ViewPaymentTerm::route('/{record}'),
            'edit'              => Pages\EditPaymentTerm::route('/{record}/edit'),
            'payment-due-terms' => Pages\ManagePaymentDueTerm::route('/{record}/payment-due-terms'),
        ];
    }
}
