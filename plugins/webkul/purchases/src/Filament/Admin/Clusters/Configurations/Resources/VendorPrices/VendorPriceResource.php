<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\VendorPrices;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\VendorPrices\Pages\CreateVendorPrice;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\VendorPrices\Pages\EditVendorPrice;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\VendorPrices\Pages\ListVendorPrices;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\VendorPrices\Pages\ViewVendorPrice;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\VendorPrices\Schemas\VendorPriceForm;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\VendorPrices\Schemas\VendorPriceInfolist;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\VendorPrices\Tables\VendorPricesTable;
use Webkul\Purchase\Models\ProductSupplier;

class VendorPriceResource extends Resource
{
    protected static ?string $model = ProductSupplier::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?int $navigationSort = 10;

    protected static ?string $cluster = Configurations::class;

    public static function getNavigationLabel(): string
    {
        return __('purchases::filament/admin/clusters/configurations/resources/vendor-price.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return VendorPriceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VendorPricesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VendorPriceInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListVendorPrices::route('/'),
            'create' => CreateVendorPrice::route('/create'),
            'view'   => ViewVendorPrice::route('/{record}'),
            'edit'   => EditVendorPrice::route('/{record}/edit'),
        ];
    }
}
