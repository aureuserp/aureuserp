<?php

namespace Webkul\Partner\Filament\Resources\Tags;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Partner\Filament\Resources\Tags\Schemas\TagForm;
use Webkul\Partner\Filament\Resources\Tags\Tables\TagsTable;
use Webkul\Partner\Models\Tag;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function getNavigationLabel(): string
    {
        return __('partners::filament/resources/tag.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return TagForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TagsTable::configure($table);
    }
}
