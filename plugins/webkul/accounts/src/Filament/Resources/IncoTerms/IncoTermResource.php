<?php

namespace Webkul\Account\Filament\Resources\IncoTerms;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Account\Filament\Resources\IncoTerms\Pages\ListIncoTerms;
use Webkul\Account\Filament\Resources\IncoTerms\Schemas\IncotermForm;
use Webkul\Account\Filament\Resources\IncoTerms\Schemas\IncotermInfolist;
use Webkul\Account\Filament\Resources\IncoTerms\Tables\IncotermsTable;
use Webkul\Account\Models\Incoterm;

class IncoTermResource extends Resource
{
    protected static ?string $model = Incoterm::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return IncotermForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IncotermsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return IncotermInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIncoTerms::route('/'),
        ];
    }
}
