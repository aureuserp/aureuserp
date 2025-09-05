<?php

namespace Webkul\Product\Filament\Resources\Attributes;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Product\Filament\Resources\Attributes\Schemas\AttributeForm;
use Webkul\Product\Filament\Resources\Attributes\Schemas\AttributeInfolist;
use Webkul\Product\Filament\Resources\Attributes\Tables\AttributesTable;
use Webkul\Product\Models\Attribute;

class AttributeResource extends Resource
{
    protected static ?string $model = Attribute::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-swatch';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return AttributeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AttributesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AttributeInfolist::configure($schema);
    }
}
