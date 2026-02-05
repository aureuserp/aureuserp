<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources;

use Webkul\Account\Filament\Resources\TaxResource as BaseTaxResource;
use Webkul\Accounting\Filament\Clusters\Configuration;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\TaxResource\Pages\CreateTax;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\TaxResource\Pages\EditTax;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\TaxResource\Pages\ListTaxes;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\TaxResource\Pages\ViewTax;
use Webkul\Accounting\Models\Tax;

class TaxResource extends BaseTaxResource
{
    protected static ?string $model = Tax::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 8;

    protected static ?string $cluster = Configuration::class;

    public static function getModelLabel(): string
    {
        return __('accounting::filament/clusters/configurations/resources/tax.model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/configurations/resources/tax.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('accounting::filament/clusters/configurations/resources/tax.navigation.group');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTaxes::route('/'),
            'create' => CreateTax::route('/create'),
            'edit'   => EditTax::route('/{record}/edit'),
            'view'   => ViewTax::route('/{record}'),
        ];
    }
}
