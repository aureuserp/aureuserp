<?php

namespace Webkul\Account\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Account\Filament\Resources\IncotermResource\Pages\ManageIncoterms;
use Webkul\Account\Filament\Resources\IncotermResource\Schemas\IncotermForm;
use Webkul\Account\Filament\Resources\IncotermResource\Schemas\IncotermInfolist;
use Webkul\Account\Filament\Resources\IncotermResource\Tables\IncotermsTable;
use Webkul\Account\Models\Incoterm;

class IncotermResource extends Resource
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
            'index' => ManageIncoterms::route('/'),
        ];
    }
}
