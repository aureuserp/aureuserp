<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources;

use Webkul\Account\Filament\Resources\TaxGroupResource as BaseTaxGroupResource;
use Webkul\Invoice\Filament\Clusters\Configuration;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxGroupResource\Pages;
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
            'index'  => Pages\ListTaxGroups::route('/'),
            'create' => Pages\CreateTaxGroup::route('/create'),
            'view'   => Pages\ViewTaxGroup::route('/{record}'),
            'edit'   => Pages\EditTaxGroup::route('/{record}/edit'),
        ];
    }
}
