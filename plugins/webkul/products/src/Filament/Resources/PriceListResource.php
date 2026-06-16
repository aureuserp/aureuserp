<?php

namespace Webkul\Product\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Product\Filament\Resources\PriceListResource\Pages\CreatePriceList;
use Webkul\Product\Filament\Resources\PriceListResource\Pages\EditPriceList;
use Webkul\Product\Filament\Resources\PriceListResource\Pages\ListPriceLists;
use Webkul\Product\Filament\Resources\PriceListResource\Pages\ViewPriceList;
use Webkul\Product\Models\PriceList;

class PriceListResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = PriceList::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-list-bullet';

    public static function getNavigationLabel(): string
    {
        return 'Price Lists';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema(static::getCustomFormFields())
                    ->columns(2)
                    ->visible(fn () => filled(static::getCustomFormFields())),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(static::mergeCustomTableColumns([
                //
            ]))
            ->filters(static::mergeCustomTableFilters([
                //
            ]))
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
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
