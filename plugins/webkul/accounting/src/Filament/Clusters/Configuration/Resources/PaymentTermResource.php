<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources;

use Webkul\Accounting\Filament\Clusters\Configuration;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\PaymentTermResource\Pages\ListPaymentTerms;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\PaymentTermResource\Pages\CreatePaymentTerm;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\PaymentTermResource\Pages\EditPaymentTerm;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\PaymentTermResource\Pages\ViewPaymentTerm;
use Webkul\Account\Filament\Resources\PaymentTermResource as BasePaymentTermResource;

class PaymentTermResource extends BasePaymentTermResource
{
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Configuration::class;

    public static function getModelLabel(): string
    {
        return __('accounting::filament/clusters/configurations/resources/payment-term.model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/configurations/resources/payment-term.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('accounting::filament/clusters/configurations/resources/payment-term.navigation.group');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPaymentTerms::route('/'),
            'create' => CreatePaymentTerm::route('/create'),
            'edit' => EditPaymentTerm::route('/{record}/edit'),
            'view' => ViewPaymentTerm::route('/{record}'),
        ];
    }
}
