<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Webkul\Inventory\Filament\Clusters\Configurations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductCategoryResource\Pages;
use Webkul\Inventory\Models\Category;
use Webkul\Inventory\Settings\WarehouseSettings;
use Webkul\Product\Filament\Resources\CategoryResource;

class ProductCategoryResource extends CategoryResource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 8;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/configurations/resources/product-category.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/configurations/resources/product-category.navigation.title');
    }

    public static function form(Form $form): Form
    {
        $form = CategoryResource::form($form);

        $components = $form->getComponents();

        $childComponents = $components[1]->getChildComponents();

        $childComponents[] = Forms\Components\Section::make(__('inventories::filament/clusters/configurations/resources/product-category.form.sections.inventory.title'))
            ->schema([
                Forms\Components\Fieldset::make(__('inventories::filament/clusters/configurations/resources/product-category.form.sections.inventory.fieldsets.logistics.title'))
                    ->schema([
                        Forms\Components\Select::make('routes')
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

        $form->components($components);

        return $form;
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        $infolist = CategoryResource::infolist($infolist);

        $components = $infolist->getComponents();

        $firstGroupChildComponents = $components[0]->getChildComponents();

        $firstGroupChildComponents[] = Infolists\Components\Section::make(__('inventories::filament/clusters/configurations/resources/product-category.infolist.sections.inventory.title'))
            ->schema([
                Infolists\Components\Section::make(__('inventories::filament/clusters/configurations/resources/product-category.infolist.sections.inventory.subsections.logistics.title'))
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('routes')
                            ->label(__('inventories::filament/clusters/configurations/resources/product-category.infolist.sections.inventory.subsections.logistics.entries.routes'))
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
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

        $infolist->components($components);

        return $infolist;
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
            Pages\ViewProductCategory::class,
            Pages\EditProductCategory::class,
            Pages\ManageProducts::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'    => Pages\ListProductCategories::route('/'),
            'create'   => Pages\CreateProductCategory::route('/create'),
            'view'     => Pages\ViewProductCategory::route('/{record}'),
            'edit'     => Pages\EditProductCategory::route('/{record}/edit'),
            'products' => Pages\ManageProducts::route('/{record}/products'),
        ];
    }
}
