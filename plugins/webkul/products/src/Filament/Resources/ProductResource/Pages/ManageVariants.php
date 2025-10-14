<?php

namespace Webkul\Product\Filament\Resources\ProductResource\Pages;

use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Webkul\Product\Filament\Resources\ProductResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageVariants extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = ProductResource::class;

    protected static string $relationship = 'variants';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function getNavigationLabel(): string
    {
        return __('products::filament/resources/product/pages/manage-variants.title');
    }

    public function form(Schema $schema): Schema
    {
        return ProductResource::form($schema);
    }

    public function table(Table $table): Table
    {
        $table = ProductResource::table($table);

        [$actions] = $table->getActions();

        $flatActions = $actions->getFlatActions();

        if (isset($flatActions['edit'])) {
            $flatActions['edit']
                ->modalWidth(Width::SevenExtraLarge);
        }

        if (isset($flatActions['view'])) {
            $flatActions['view']
                ->modalWidth(Width::SevenExtraLarge);
        }

        $table->columns(Arr::except($table->getColumns(), ['variants_count']));

        $table->columns([
            TextColumn::make('combinations')
                ->label(__('products::filament/resources/product/pages/manage-variants.table.columns.variant-values'))
                ->state(function ($record) {
                    return $record->combinations->map(function ($combination) {
                        $attributeName = $combination->productAttributeValue?->attribute?->name;
                        $optionName = $combination->productAttributeValue?->attributeOption?->name;

                        return $attributeName && $optionName ? "{$attributeName}: {$optionName}" : $optionName;
                    });
                })
                ->badge()
                ->sortable(),
            ...$table->getColumns(),
        ]);

        $table->modelLabel(__('products::filament/resources/product/pages/manage-variants.title'));

        return $table;
    }

    public function infolist(Schema $schema): Schema
    {
        return ProductResource::infolist($schema);
    }
}
