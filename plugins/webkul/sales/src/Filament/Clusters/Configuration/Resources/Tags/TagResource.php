<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\Tags;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Sale\Filament\Clusters\Configuration;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\Tags\Pages\ListTags;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\Tags\Schemas\TagForm;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\Tags\Schemas\TagInfolist;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\Tags\Tables\TagsTable;
use Webkul\Sale\Models\Tag;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $cluster = Configuration::class;

    public static function getModelLabel(): string
    {
        return __('sales::filament/clusters/configurations/resources/tag.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/configurations/resources/tag.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('sales::filament/clusters/configurations/resources/tag.navigation.group');
    }

    public static function form(Schema $schema): Schema
    {
        return TagForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TagsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTags::route('/'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return TagInfolist::configure($schema);
    }
}
