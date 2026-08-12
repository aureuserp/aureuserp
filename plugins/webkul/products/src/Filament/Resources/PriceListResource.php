<?php

namespace Webkul\Product\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Product\Filament\Resources\PriceListResource\Pages\CreatePriceList;
use Webkul\Product\Filament\Resources\PriceListResource\Pages\EditPriceList;
use Webkul\Product\Filament\Resources\PriceListResource\Pages\ListPriceLists;
use Webkul\Product\Filament\Resources\PriceListResource\Pages\ViewPriceList;
use Webkul\Product\Filament\Resources\PriceListResource\Schemas\PriceListForm;
use Webkul\Product\Filament\Resources\PriceListResource\Tables\PriceListsTable;
use Webkul\Product\Models\PriceList;

class PriceListResource extends Resource
{
    protected static ?string $model = PriceList::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-list-bullet';

    public static function getNavigationLabel(): string
    {
        return 'Price Lists';
    }

    public static function form(Schema $schema): Schema
    {
        return PriceListForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PriceListsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPriceLists::route('/'),
            'create' => CreatePriceList::route('/create'),
            'view'   => ViewPriceList::route('/{record}'),
            'edit'   => EditPriceList::route('/{record}/edit'),
        ];
    }
}
