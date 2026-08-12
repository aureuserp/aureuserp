<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductCategoryResource\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Inventory\Settings\WarehouseSettings;
use Webkul\Product\Filament\Resources\CategoryResource;

class ProductCategoryInfolist
{
    public static function configure(Schema $schema): Schema
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
}
