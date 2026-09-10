<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources;

use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Inventory\Filament\Clusters\Products;
use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource\Pages;
use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource\Pages\CreateLot;
use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource\Pages\EditLot;
use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource\Pages\ListLots;
use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource\Pages\ViewLot;
use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource\Schemas\LotForm;
use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource\Schemas\LotInfolist;
use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource\Tables\LotsTable;
use Webkul\Inventory\Models\Lot;
use Webkul\Inventory\Settings\TraceabilitySettings;

class LotResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Lot::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Products::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 3;

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return settings(TraceabilitySettings::class)->enable_lots_serial_numbers;
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/products/resources/lot.navigation.title');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'product.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('inventories::filament/clusters/products/resources/lot.global-search.product') => $record->product?->name ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return LotForm::configure($schema, static::getCustomFormFields());
    }

    public static function table(Table $table): Table
    {
        return LotsTable::configure($table, static::getCustomTableColumns(), static::getCustomTableFilters());
    }

    public static function infolist(Schema $schema): Schema
    {
        return LotInfolist::configure($schema, static::getCustomInfolistEntries());
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewLot::class,
            EditLot::class,
            Pages\ManageQuantities::class,
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
