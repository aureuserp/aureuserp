<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources;

use Webkul\Account\Filament\Resources\TaxGroupResource as BaseTaxGroupResource;
use Webkul\Accounting\Filament\Clusters\Configuration;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\TaxGroupResource\Pages\CreateTaxGroup;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\TaxGroupResource\Pages\EditTaxGroup;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\TaxGroupResource\Pages\ListTaxGroups;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\TaxGroupResource\Pages\ViewTaxGroup;
use Webkul\Accounting\Models\TaxGroup;

class TaxGroupResource extends BaseTaxGroupResource
{
    protected static ?string $model = TaxGroup::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 7;

    protected static ?string $cluster = Configuration::class;

    public static function getModelLabel(): string
    {
        return __('accounting::filament/clusters/configurations/resources/tax-group.model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/configurations/resources/tax-group.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('accounting::filament/clusters/configurations/resources/tax-group.navigation.group');
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
