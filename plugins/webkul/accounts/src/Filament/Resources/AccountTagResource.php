<?php

namespace Webkul\Account\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Account\Filament\Resources\AccountTagResource\Pages\ListAccountTags;
use Webkul\Account\Filament\Resources\AccountTagResource\Schemas\AccountTagForm;
use Webkul\Account\Filament\Resources\AccountTagResource\Schemas\AccountTagInfolist;
use Webkul\Account\Filament\Resources\AccountTagResource\Tables\AccountTagsTable;
use Webkul\Account\Models\Tag;

class AccountTagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return AccountTagForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AccountTagsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AccountTagInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAccountTags::route('/'),
        ];
    }
}
