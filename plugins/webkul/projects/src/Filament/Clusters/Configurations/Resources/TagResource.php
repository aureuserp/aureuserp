<?php

namespace Webkul\Project\Filament\Clusters\Configurations\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Project\Filament\Clusters\Configurations;
use Webkul\Project\Filament\Clusters\Configurations\Resources\TagResource\Pages\ManageTags;
use Webkul\Project\Filament\Clusters\Configurations\Resources\TagResource\Schemas\TagForm;
use Webkul\Project\Filament\Clusters\Configurations\Resources\TagResource\Tables\TagsTable;
use Webkul\Project\Models\Tag;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = Configurations::class;

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/clusters/configurations/resources/tag.navigation.title');
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
