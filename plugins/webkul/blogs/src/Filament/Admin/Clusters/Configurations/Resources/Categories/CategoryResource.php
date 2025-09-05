<?php

namespace Webkul\Blog\Filament\Admin\Clusters\Configurations\Resources\Categories;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Blog\Filament\Admin\Clusters\Configurations\Resources\Categories\Pages\ManageCategories;
use Webkul\Blog\Filament\Admin\Clusters\Configurations\Resources\Categories\Schemas\CategoryForm;
use Webkul\Blog\Filament\Admin\Clusters\Configurations\Resources\Categories\Tables\CategoryTable;
use Webkul\Blog\Models\Category;
use Webkul\Website\Filament\Admin\Clusters\Configurations;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $cluster = Configurations::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-folder';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('blogs::filament/admin/clusters/configurations/resources/category.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('blogs::filament/admin/clusters/configurations/resources/category.navigation.group');
    }

    public static function form(Schema $schema): Schema
    {
        return CategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoryTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCategories::route('/'),
        ];
    }
}
