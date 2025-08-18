<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources;

use Webkul\Account\Filament\Resources\TaxGroupResource as BaseTaxGroupResource;
use Webkul\Invoice\Filament\Clusters\Configuration;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxGroupResource\Pages\CreateTaxGroup;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxGroupResource\Pages\EditTaxGroup;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxGroupResource\Pages\ListTaxGroups;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxGroupResource\Pages\ViewTaxGroup;
use Webkul\Invoice\Models\TaxGroup;

class TaxGroupResource extends BaseTaxGroupResource
{
    protected static ?string $model = TaxGroup::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Configuration::class;

    public static function getModelLabel(): string
    {
        return __('invoices::filament/clusters/configurations/resources/tax-group.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/configurations/resources/tax-group.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('invoices::filament/clusters/configurations/resources/tax-group.navigation.group');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTaxGroups::route('/'),
            'create' => CreateTaxGroup::route('/create'),
            'view'   => ViewTaxGroup::route('/{record}'),
            'edit'   => EditTaxGroup::route('/{record}/edit'),
        ];
    }
}
