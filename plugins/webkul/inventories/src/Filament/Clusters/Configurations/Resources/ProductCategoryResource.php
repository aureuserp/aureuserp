<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources;

use Filament\Forms\Components\Select;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Inventory\Filament\Clusters\Configurations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductCategoryResource\Pages\CreateProductCategory;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductCategoryResource\Pages\EditProductCategory;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductCategoryResource\Pages\ListProductCategories;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductCategoryResource\Pages\ManageProducts;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductCategoryResource\Pages\ViewProductCategory;
use Webkul\Inventory\Models\Category;
use Webkul\Inventory\Settings\WarehouseSettings;
use Webkul\Product\Filament\Resources\CategoryResource;

class ProductCategoryResource extends CategoryResource
{
    protected static ?string $model = Category::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-folder';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 8;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isGloballySearchable = false;

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/configurations/resources/product-category.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/configurations/resources/product-category.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        $schema = CategoryResource::form($schema);

        $components = $schema->getComponents();

        $childComponents = $components[1]->getDefaultChildComponents();

        $childComponents[] = Section::make(__('inventories::filament/clusters/configurations/resources/product-category.form.sections.inventory.title'))
            ->schema([
                Fieldset::make(__('inventories::filament/clusters/configurations/resources/product-category.form.sections.inventory.fieldsets.logistics.title'))
                    ->schema([
                        Select::make('routes')
                            ->label(__('inventories::filament/clusters/configurations/resources/product-category.form.sections.inventory.fieldsets.logistics.fields.routes'))
                            ->relationship('routes', 'name')
                            ->searchable()
                            ->preload()
                            ->multiple(),
                    ])
                    ->columns(1),
            ])
            ->visible(fn (WarehouseSettings $settings) => $settings->enable_multi_steps_routes);

        $components[1]->childComponents($childComponents);

        $schema->components($components);

        return $schema;
    }

    public static function infolist(Schema $schema): Schema
    {
        $schema = CategoryResource::infolist($schema);

        $components = $schema->getComponents();

        $firstGroupChildComponents = $components[0]->getDefaultChildComponents();

        $firstGroupChildComponents[] = Section::make(__('inventories::filament/clusters/configurations/resources/product-category.infolist.sections.inventory.title'))
            ->schema([
                Section::make(__('inventories::filament/clusters/configurations/resources/product-category.infolist.sections.inventory.subsections.logistics.title'))
                    ->schema([
                        RepeatableEntry::make('routes')
                            ->label(__('inventories::filament/clusters/configurations/resources/product-category.infolist.sections.inventory.subsections.logistics.entries.routes'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/product-category.infolist.sections.inventory.subsections.logistics.entries.route_name'))
                                    ->icon('heroicon-o-truck'),
                            ])
                            ->columns(1),
                    ])
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsible(),
            ])
            ->visible(fn (WarehouseSettings $settings) => $settings->enable_multi_steps_routes);

        $components[0]->childComponents($firstGroupChildComponents);

        $schema->components($components);

        return $schema;
    }

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        $route = request()->route()?->getName() ?? session('current_route');

        if ($route && $route != 'livewire.update') {
            session(['current_route' => $route]);
        } else {
            $route = session('current_route');
        }

        if ($route === self::getRouteBaseName().'.index') {
            return SubNavigationPosition::Start;
        }

        return SubNavigationPosition::Top;
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewProductCategory::class,
            EditProductCategory::class,
            ManageProducts::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'    => ListProductCategories::route('/'),
            'create'   => CreateProductCategory::route('/create'),
            'view'     => ViewProductCategory::route('/{record}'),
            'edit'     => EditProductCategory::route('/{record}/edit'),
            'products' => ManageProducts::route('/{record}/products'),
        ];
    }
}
