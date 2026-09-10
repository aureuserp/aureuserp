<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Inventory\Filament\Clusters\Configurations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackagingResource\Pages\ManagePackagings;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackagingResource\Schemas\PackagingForm;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackagingResource\Schemas\PackagingInfolist;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackagingResource\Tables\PackagingsTable;
use Webkul\Inventory\Models\Packaging;
use Webkul\Inventory\Settings\OperationSettings;
use Webkul\Inventory\Settings\WarehouseSettings;
use Webkul\Product\Filament\Resources\PackagingResource as BasePackagingResource;
use Webkul\Product\Settings\ProductSettings;

class PackagingResource extends BasePackagingResource
{
    protected static ?string $model = Packaging::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-gift';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 10;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isGloballySearchable = false;

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return static::getProductSettings()->enable_packagings;
    }

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/configurations/resources/packaging.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/configurations/resources/packaging.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return PackagingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PackagingsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PackagingInfolist::configure($schema);
    }

    public static function getOperationSettings(): OperationSettings
    {
        return settings(OperationSettings::class);
    }

    public static function getProductSettings(): ProductSettings
    {
        return settings(ProductSettings::class);
    }

    public static function getWarehouseSettings(): WarehouseSettings
    {
        return settings(WarehouseSettings::class);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePackagings::route('/'),
        ];
    }
}
