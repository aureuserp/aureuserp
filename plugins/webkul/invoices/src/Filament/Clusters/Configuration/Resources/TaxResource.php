<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources;

use Webkul\Account\Filament\Resources\TaxResource as BaseTaxResource;
use Webkul\Invoice\Filament\Clusters\Configuration;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxResource\Pages;
use Webkul\Invoice\Models\Tax;

class TaxResource extends BaseTaxResource
{
    protected static ?string $model = Tax::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Configuration::class;

    public static function getModelLabel(): string
    {
        return __('invoices::filament/clusters/configurations/resources/tax.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/configurations/resources/tax.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('invoices::filament/clusters/configurations/resources/tax.navigation.group');
    }

    public static function getPages(): array
    {
        return [
            'index'                           => Pages\ListTaxes::route('/'),
            'create'                          => Pages\CreateTax::route('/create'),
            'view'                            => Pages\ViewTax::route('/{record}'),
            'edit'                            => Pages\EditTax::route('/{record}/edit'),
            'manage-distribution-for-invoice' => Pages\ManageDistributionForInvoice::route('/{record}/manage-distribution-for-invoice'),
            'manage-distribution-for-refunds' => Pages\ManageDistributionForRefund::route('/{record}/manage-distribution-for-refunds'),
        ];
    }
}
