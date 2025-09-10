<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\Lots;

use BackedEnum;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Inventory\Filament\Clusters\Products;
use Webkul\Inventory\Filament\Clusters\Products\Resources\Lots\Pages\CreateLot;
use Webkul\Inventory\Filament\Clusters\Products\Resources\Lots\Pages\EditLot;
use Webkul\Inventory\Filament\Clusters\Products\Resources\Lots\Pages\ListLots;
use Webkul\Inventory\Filament\Clusters\Products\Resources\Lots\Pages\ViewLot;
use Webkul\Inventory\Filament\Clusters\Products\Resources\Lots\Schemas\LotForm;
use Webkul\Inventory\Filament\Clusters\Products\Resources\Lots\Schemas\LotInfolist;
use Webkul\Inventory\Filament\Clusters\Products\Resources\Lots\Tables\LotsTable;
use Webkul\Inventory\Filament\Clusters\Products\Resources\Products\Pages\ManageQuantities;
use Webkul\Inventory\Models\Lot;
use Webkul\Inventory\Settings\TraceabilitySettings;

class LotResource extends Resource
{
    protected static ?string $model = Lot::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Products::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 3;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return app(TraceabilitySettings::class)->enable_lots_serial_numbers;
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/products/resources/lot.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return LotForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LotsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LotInfolist::configure($schema);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewLot::class,
            EditLot::class,
            ManageQuantities::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'      => ListLots::route('/'),
            'create'     => CreateLot::route('/create'),
            'view'       => ViewLot::route('/{record}'),
            'edit'       => EditLot::route('/{record}/edit'),
            'quantities' => Pages\ManageQuantities::route('/{record}/quantities'),
        ];
    }
}
