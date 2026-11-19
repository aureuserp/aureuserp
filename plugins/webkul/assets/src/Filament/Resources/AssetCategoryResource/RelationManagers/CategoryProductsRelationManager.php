<?php

namespace Webkul\Asset\Filament\Resources\AssetCategoryResource\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Asset\Filament\Resources\AssetCategoryResource;
use Webkul\Asset\Filament\Resources\ProductResource;
use Webkul\Asset\Models\AssetCategory;
use Webkul\Asset\Models\AssetProduct;

class CategoryProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('assets::filament/resources/category.relation-manager.category-products.title');
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        if (! $ownerRecord instanceof AssetCategory) {
            return false;
        }

        return AssetCategoryResource::canView($ownerRecord);
    }

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->extraAttributes(['class' => 'fi-ta-sticky-scroll'])
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100])
            ->recordUrl(fn (AssetProduct $record): string => ProductResource::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make('name')
                    ->label(__('assets::filament/resources/product.table.columns.name'))
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('model')
                    ->label(__('assets::filament/resources/product.table.columns.model'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('cost')
                    ->label(__('assets::filament/resources/product.table.columns.cost'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                TextColumn::make('assets_count')
                    ->label(__('assets::filament/resources/product.table.columns.assets-count'))
                    ->counts('assets')
                    ->badge()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label(__('assets::filament/resources/product.table.columns.is-active'))
                    ->boolean(),

                TextColumn::make('company.name')
                    ->label(__('assets::filament/resources/product.table.columns.company'))
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('assets::filament/resources/product.table.filters.is-active')),

                SelectFilter::make('company_id')
                    ->label(__('assets::filament/resources/product.table.filters.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (AssetProduct $record): string => ProductResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
