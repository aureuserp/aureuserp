<?php

namespace Webkul\Product\Filament\Resources\ProductResource\Pages;

use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Webkul\Product\Filament\Resources\ProductResource;

class ManageVariants extends ManageRelatedRecords
{
    protected static string $resource = ProductResource::class;

    protected static string $relationship = 'variants';

    public function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function getNavigationLabel(): string
    {
        return __('products::filament/resources/product/pages/manage-variants.title');
    }

    public function form(Form $form): Form
    {
        return ProductResource::form($form);
    }

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
                ->badge()
                ->sortable()
                ->searchable(),
            ...$table->getColumns(),
        ]);

        $table->modelLabel(__('products::filament/resources/product/pages/manage-variants.title'));

        return $table->modifyQueryUsing(function ($query) {
            $queryBuilder = $query->getQuery();

            $queryBuilder->wheres = collect($queryBuilder->wheres)
                ->filter(fn($where) => ! ($where['column'] == 'parent_id' && $where['type'] == 'Null'))
                ->values()
                ->toArray();
        });
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return ProductResource::infolist($infolist);
    }
}
