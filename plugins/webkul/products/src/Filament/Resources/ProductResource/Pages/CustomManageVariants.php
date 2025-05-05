<?php

namespace Webkul\Product\Filament\Resources\ProductResource\Pages;

use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Pages\SubNavigationPosition;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Webkul\Product\Filament\Resources\CustomProductResource as ProductResource;

class CustomManageVariants extends ManageVariants
{
    public function table(Table $table): Table
    {
        $table = ProductResource::table($table);

        [$actions] = $table->getActions();

        $flatActions = $actions->getFlatActions();

        if (isset($flatActions['edit'])) {
            $flatActions['edit']
                ->modalWidth(MaxWidth::SevenExtraLarge);
        }

        if (isset($flatActions['view'])) {
            $flatActions['view']
                ->modalWidth(MaxWidth::SevenExtraLarge);
        }

        $table->columns(Arr::except($table->getColumns(), ['variants_count']));

        $table->columns([
            Tables\Columns\TextColumn::make('combinations')
                ->label(__('products::filament/resources/product/pages/manage-variants.table.columns.variant-values'))
                ->state(function ($record) {
                    return $record->combinations->map(function ($combination) {
                        $attributeName = $combination->productAttributeValue?->attribute?->name;
                        $optionName = $combination->productAttributeValue?->attributeOption?->name;

                        return $attributeName && $optionName ? "{$attributeName}: {$optionName}" : $optionName;
                    });
                })
                ->badge(),
                // ->sortable(),
            ...$table->getColumns(),
        ]);

        $table->modelLabel(__('products::filament/resources/product/pages/manage-variants.title'));

        return $table;
    }
}
