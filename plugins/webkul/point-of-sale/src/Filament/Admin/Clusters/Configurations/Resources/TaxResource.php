<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources;

use BackedEnum;
use Webkul\Account\Filament\Resources\TaxResource as BaseTaxResource;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\TaxResource\Pages\CreateTax;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\TaxResource\Pages\EditTax;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\TaxResource\Pages\ListTaxes;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\TaxResource\Pages\ViewTax;
use Webkul\PointOfSale\Models\Tax;

class TaxResource extends BaseTaxResource
{
    protected static ?string $model = Tax::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 8;

    protected static ?string $cluster = Configurations::class;

    public static function getModelLabel(): string
    {
        return __('point-of-sale::filament/admin/clusters/configurations/resources/tax.model-label');
    }

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/admin/clusters/configurations/resources/tax.navigation.title');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTaxes::route('/'),
            'create' => CreateTax::route('/create'),
            'view'   => ViewTax::route('/{record}'),
            'edit'   => EditTax::route('/{record}/edit'),
        ];
    }
}
