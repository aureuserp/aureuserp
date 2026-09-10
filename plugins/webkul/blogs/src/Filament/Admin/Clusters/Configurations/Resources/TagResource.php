<?php

namespace Webkul\Blog\Filament\Admin\Clusters\Configurations\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Blog\Filament\Admin\Clusters\Configurations\Resources\TagResource\Pages\ManageTags;
use Webkul\Blog\Filament\Admin\Clusters\Configurations\Resources\TagResource\Schemas\TagForm;
use Webkul\Blog\Filament\Admin\Clusters\Configurations\Resources\TagResource\Tables\TagsTable;
use Webkul\Blog\Models\Tag;
use Webkul\Website\Filament\Admin\Clusters\Configurations;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = Configurations::class;

    public static function getNavigationLabel(): string
    {
        return __('blogs::filament/admin/clusters/configurations/resources/tag.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('blogs::filament/admin/clusters/configurations/resources/tag.navigation.group');
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
            'index' => ManageTags::route('/'),
        ];
    }
}
