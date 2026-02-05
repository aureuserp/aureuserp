<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources;

use Filament\Pages\Page;
use Webkul\Account\Filament\Resources\PaymentTermResource as BasePaymentTermResource;
use Webkul\Accounting\Filament\Clusters\Configuration;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\PaymentTermResource\Pages\CreatePaymentTerm;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\PaymentTermResource\Pages\EditPaymentTerm;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\PaymentTermResource\Pages\ListPaymentTerms;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\PaymentTermResource\Pages\ViewPaymentTerm;
use Webkul\Accounting\Models\PaymentTerm;

class PaymentTermResource extends BasePaymentTermResource
{
    protected static ?string $model = PaymentTerm::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 1;

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

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewPaymentTerm::class,
            EditPaymentTerm::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPaymentTerms::route('/'),
            'create' => CreatePaymentTerm::route('/create'),
            'edit'   => EditPaymentTerm::route('/{record}/edit'),
            'view'   => ViewPaymentTerm::route('/{record}'),
        ];
    }
}
