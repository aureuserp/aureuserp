<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources;

use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Inventory\Filament\Clusters\Operations;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource\Pages\CreateScrap;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource\Pages\EditScrap;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource\Pages\ListScraps;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource\Pages\ManageMoves;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource\Pages\ViewScrap;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource\Schemas\ScrapForm;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource\Schemas\ScrapInfolist;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource\Tables\ScrapsTable;
use Webkul\Inventory\Models\Scrap;
use Webkul\Inventory\Settings\OperationSettings;
use Webkul\Inventory\Settings\TraceabilitySettings;
use Webkul\Inventory\Settings\WarehouseSettings;
use Webkul\Product\Settings\ProductSettings;

class ScrapResource extends Resource
{
    protected static ?string $model = Scrap::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-trash';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $cluster = Operations::class;

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/operations/resources/scrap.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/operations/resources/scrap.navigation.group');
    }

    public static function form(Schema $schema): Schema
    {
        return ScrapForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ScrapsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ScrapInfolist::configure($schema);
    }

    public static function getOperationSettings(): OperationSettings
    {
        return settings(OperationSettings::class);
    }

    public static function getProductSettings(): ProductSettings
    {
        return settings(ProductSettings::class);
    }

    public static function getTraceabilitySettings(): TraceabilitySettings
    {
        return settings(TraceabilitySettings::class);
    }

    public static function getWarehouseSettings(): WarehouseSettings
    {
        return settings(WarehouseSettings::class);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewScrap::class,
            EditScrap::class,
            ManageMoves::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListScraps::route('/'),
            'create' => CreateScrap::route('/create'),
            'view'   => ViewScrap::route('/{record}/view'),
            'edit'   => EditScrap::route('/{record}/edit'),
            'moves'  => ManageMoves::route('/{record}/moves'),
        ];
    }
}
