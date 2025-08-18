<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources;

use Webkul\Account\Filament\Resources\TaxResource as BaseTaxResource;
use Webkul\Invoice\Filament\Clusters\Configuration;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxResource\Pages\CreateTax;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxResource\Pages\EditTax;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxResource\Pages\ListTaxes;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxResource\Pages\ManageDistributionForInvoice;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxResource\Pages\ManageDistributionForRefund;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxResource\Pages\ViewTax;
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
            'index'                           => ListTaxes::route('/'),
            'create'                          => CreateTax::route('/create'),
            'view'                            => ViewTax::route('/{record}'),
            'edit'                            => EditTax::route('/{record}/edit'),
            'manage-distribution-for-invoice' => ManageDistributionForInvoice::route('/{record}/manage-distribution-for-invoice'),
            'manage-distribution-for-refunds' => ManageDistributionForRefund::route('/{record}/manage-distribution-for-refunds'),
        ];
    }
}
