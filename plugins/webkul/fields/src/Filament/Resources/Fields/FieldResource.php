<?php

namespace Webkul\Field\Filament\Resources\Fields;

use BackedEnum;
use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Field\Filament\Resources\Fields\Pages\CreateField;
use Webkul\Field\Filament\Resources\Fields\Pages\EditField;
use Webkul\Field\Filament\Resources\Fields\Pages\ListFields;
use Webkul\Field\Filament\Resources\Fields\Pages\ViewField;
use Webkul\Field\Filament\Resources\Fields\Schemas\FieldInfolist;
use Webkul\Field\Filament\Resources\Fields\Schemas\FieldSchema;
use Webkul\Field\Filament\Resources\Fields\Tables\FieldsTable;
use Webkul\Field\Models\Field;

class FieldResource extends Resource
{
    protected static ?string $model = Field::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?int $navigationSort = 5;

    protected static ?string $slug = 'fields';

    public static function getSlug(?Panel $panel = null): string
    {
        return static::$slug ?? parent::getSlug($panel);
    }

    public static function getModelLabel(): string
    {
        return __('fields::filament/resources/field.navigation.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('fields::filament/resources/field.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('fields::filament/resources/field.navigation.group');
    }

    public static function form(Schema $schema): Schema
    {
        return FieldSchema::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FieldsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FieldInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListFields::route('/'),
            'create' => CreateField::route('/create'),
            'edit'   => EditField::route('/{record}/edit'),
            'view'   => ViewField::route('/{record}'),
        ];
    }
}
