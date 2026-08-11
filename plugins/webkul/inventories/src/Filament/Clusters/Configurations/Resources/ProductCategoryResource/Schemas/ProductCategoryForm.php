<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductCategoryResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Inventory\Settings\WarehouseSettings;
use Webkul\Product\Filament\Resources\CategoryResource;

class ProductCategoryForm
{
    public static function configure(Schema $schema): Schema
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
}
