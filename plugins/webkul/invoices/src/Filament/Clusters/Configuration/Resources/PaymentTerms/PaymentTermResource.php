<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\PaymentTerms;

use Filament\Resources\Pages\Page;
use Webkul\Account\Filament\Resources\PaymentTerms\PaymentTermResource as BasePaymentTermResource;
use Webkul\Invoice\Filament\Clusters\Configuration;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\PaymentTerms\Pages\CreatePaymentTerm;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\PaymentTerms\Pages\EditPaymentTerm;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\PaymentTerms\Pages\ListPaymentTerms;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\PaymentTerms\Pages\ManagePaymentDueTerm;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\PaymentTerms\Pages\ViewPaymentTerm;
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

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewPaymentTerm::class,
            Pages\EditPaymentTerm::class,
            Pages\ManagePaymentDueTerm::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'             => ListPaymentTerms::route('/'),
            'create'            => CreatePaymentTerm::route('/create'),
            'view'              => ViewPaymentTerm::route('/{record}'),
            'edit'              => EditPaymentTerm::route('/{record}/edit'),
            'payment-due-terms' => ManagePaymentDueTerm::route('/{record}/payment-due-terms'),
        ];
    }
}
