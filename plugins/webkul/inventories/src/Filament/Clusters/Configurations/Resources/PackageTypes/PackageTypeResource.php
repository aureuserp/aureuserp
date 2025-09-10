<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackageTypes;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Inventory\Filament\Clusters\Configurations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackageTypes\Pages\CreatePackageType;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackageTypes\Pages\EditPackageType;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackageTypes\Pages\ListPackageTypes;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackageTypes\Pages\ViewPackageType;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackageTypes\Schemas\PackageTypeForm;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackageTypes\Schemas\PackageTypeInfolist;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackageTypes\tables\PackageTypesTable;
use Webkul\Inventory\Models\PackageType;
use Webkul\Inventory\Settings\OperationSettings;

class PackageTypeResource extends Resource
{
    protected static ?string $model = PackageType::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?int $navigationSort = 10;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isGloballySearchable = false;

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/configurations/resources/package-type.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/configurations/resources/package-type.navigation.title');
    }

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return app(OperationSettings::class)->enable_packages;
    }

    public static function form(Schema $schema): Schema
    {
        return PackageTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PackageTypesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PackageTypeInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPackageTypes::route('/'),
            'create' => CreatePackageType::route('/create'),
            'view'   => ViewPackageType::route('/{record}'),
            'edit'   => EditPackageType::route('/{record}/edit'),
        ];
    }
}
