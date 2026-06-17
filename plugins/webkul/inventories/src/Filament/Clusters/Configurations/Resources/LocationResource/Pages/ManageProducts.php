<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\LocationResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Webkul\Employee\Enums\Colors;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\LocationResource;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Product\Enums\ProductType;
use Webkul\Support\Filament\Infolists\Components\RepeatableEntry;
use Webkul\Support\Filament\Infolists\Components\Repeater\TableColumn as InfolistTableColumn;
use Webkul\Support\Traits\HasRecordNavigationTabs;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ManageProducts extends ManageRelatedRecords
{
    use HasTableViews, HasRecordNavigationTabs;

    protected static string $resource = LocationResource::class;

    protected static string $relationship = 'quantities';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/configurations/resources/location/pages/manage-products.title');
    }

    public function getPresetTableViews(): array
    {
        return [
            'default' => PresetView::make(__('inventories::filament/clusters/configurations/resources/location/pages/manage-products.tabs.default'))
                ->icon('heroicon-s-rectangle-stack')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('product', fn (Builder $productQuery) => $productQuery->whereNull('deleted_at'))),
            'goods' => PresetView::make(__('inventories::filament/clusters/configurations/resources/location/pages/manage-products.tabs.goods'))
                ->icon('heroicon-s-squares-plus')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('product', fn (Builder $productQuery) => $productQuery->where('type', ProductType::GOODS))),
            'favorite' => PresetView::make(__('inventories::filament/clusters/configurations/resources/location/pages/manage-products.tabs.favorite'))
                ->icon('heroicon-s-star')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('product', fn (Builder $productQuery) => $productQuery->where('is_favorite', true))),
            'archived' => PresetView::make(__('inventories::filament/clusters/configurations/resources/location/pages/manage-products.tabs.archived'))
                ->icon('heroicon-s-archive-box')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('product', fn (Builder $productQuery) => $productQuery->withTrashed()->whereNotNull('deleted_at'))),
        ];
    }

    public function table(Table $table): Table
    {
        $location = $this->getOwnerRecord();

        return $table
            ->recordUrl(fn ($record) => ProductResource::getUrl('view', ['record' => $record->product->parent_id ?? $record->product_id]))
            ->modifyQueryUsing(function (Builder $query) use ($location) {
                $query->select('inventories_product_quantities.*')
                    ->addSelect('products_products.type as product_type')
                    ->join('products_products', 'inventories_product_quantities.product_id', '=', 'products_products.id')
                    ->leftJoin('products_categories', 'products_products.category_id', '=', 'products_categories.id')
                    ->addSelect('products_categories.name as product_category_name')
                    ->whereIn('inventories_product_quantities.id', function ($subQuery) use ($location) {
                        $subQuery->select(DB::raw('MIN(quantities.id)'))
                            ->from('inventories_product_quantities as quantities')
                            ->join('products_products as products', 'quantities.product_id', '=', 'products.id')
                            ->where('quantities.location_id', $location->id)
                            ->groupBy(DB::raw('COALESCE(products.parent_id, products.id)'));
                    })
                    ->with(['product' => fn ($productQuery) => $productQuery->withTrashed()
                        ->withCount('variants')
                        ->with([
                            'uom',
                            'parent' => fn ($parentQuery) => $parentQuery->withTrashed()->withCount('variants')->with('uom'),
                        ])]);
            })
            ->columns([
                TextColumn::make('product.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/location/pages/manage-products.table.columns.product'))
                    ->state(fn ($record) => $record->product->parent ? $record->product->parent->name : $record->product->name)
                    ->searchable(query: function (Builder $query, $search) {
                        return $query->where(function (Builder $searchQuery) use ($search) {
                            $searchQuery->whereHas('product', function (Builder $productSearchQuery) use ($search) {
                                $productSearchQuery->where('name', 'like', "%{$search}%")
                                    ->orWhereHas('parent', fn (Builder $parentProductQuery) => $parentProductQuery->where('name', 'like', "%{$search}%"));
                            });
                        });
                    })
                    ->sortable(),
                TextColumn::make('variants_count')
                    ->label(__('inventories::filament/clusters/configurations/resources/location/pages/manage-products.table.columns.variants'))
                    ->state(function ($record) {
                        $count = ($record->product->parent ?? $record->product)->variants_count;

                        return $count > 0 ? $count : '—';
                    })
                    ->badge(fn ($record) => ($record->product->parent ?? $record->product)->variants_count > 0)
                    ->color(fn ($record) => ($record->product->parent ?? $record->product)->variants_count > 0 ? Colors::Success->value : Colors::Gray->value)
                    ->action(
                        Action::make('view_variants')
                            ->modal()
                            ->modalWidth('7xl')
                            ->modalHeading(fn ($record) => ($record->product->parent ?? $record->product)->name.' - '.__('inventories::filament/clusters/configurations/resources/location/pages/manage-products.variants-infolist.variants'))
                            ->infolist(fn ($record) => $this->getVariantsInfolist($record))
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel(__('inventories::filament/clusters/configurations/resources/location/pages/manage-products.variants-infolist.close'))
                            ->disabled(fn ($record) => ($record->product->parent ?? $record->product)->variants_count === 0)
                    ),
                TextColumn::make('price')
                    ->label(__('inventories::filament/clusters/configurations/resources/location/pages/manage-products.table.columns.sales-price'))
                    ->state(fn ($record) => ($record->product->parent ?? $record->product)->price)
                    ->money()
                    ->sortable(),
                TextColumn::make('cost')
                    ->label(__('inventories::filament/clusters/configurations/resources/location/pages/manage-products.table.columns.cost-price'))
                    ->state(fn ($record) => ($record->product->parent ?? $record->product)->cost)
                    ->money()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->label(__('inventories::filament/clusters/configurations/resources/location/pages/manage-products.table.columns.on-hand'))
                    ->state(function ($record) {
                        $templateProduct = $record->product->parent ?? $record->product;
                        if ($templateProduct->is_configurable) {
                            return ProductQuantity::whereIn('product_id', $templateProduct->variants()->pluck('id'))
                                ->where('location_id', $record->location_id)
                                ->sum('quantity');
                        }

                        return $record->quantity;
                    })
                    ->sortable(),
                TextColumn::make('forecast')
                    ->label(__('inventories::filament/clusters/configurations/resources/location/pages/manage-products.table.columns.forecast'))
                    ->state(function ($record) {
                        $templateProduct = $record->product->parent ?? $record->product;
                        $templateProduct->setContext(['location_id' => $record->location_id]);

                        return $templateProduct->virtual_available_qty;
                    })
                    ->sortable(),
                TextColumn::make('uom_name')
                    ->label(__('inventories::filament/clusters/configurations/resources/location/pages/manage-products.table.columns.unit-of-measure'))
                    ->state(fn ($record) => ($record->product->parent ?? $record->product)->uom?->name)
                    ->placeholder('—'),
            ])
            ->recordActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(fn (Collection $records) => $records->each(
                            fn (ProductQuantity $record) => ($record->product->parent ?? $record->product)->delete()
                        )),
                    RestoreBulkAction::make()
                        ->action(fn (Collection $records) => $records->each(function (ProductQuantity $record) {
                            $product = $record->product->parent ?? $record->product;
                            $product->restore();
                            $product->variants()->onlyTrashed()->restore();
                        })),
                ]),
            ])
            ->groups([
                Group::make('product_type')
                    ->label(__('inventories::filament/clusters/configurations/resources/location/pages/manage-products.table.groups.product-type'))
                    ->getTitleFromRecordUsing(fn ($record) => $record->product->type?->getLabel() ?? $record->product_type),
                Group::make('product_category_name')
                    ->label(__('inventories::filament/clusters/configurations/resources/location/pages/manage-products.table.groups.category'))
                    ->getTitleFromRecordUsing(fn ($record) => $record->product_category_name
                        ?? __('inventories::filament/clusters/configurations/resources/location/pages/manage-products.table.groups.uncategorized')),
            ]);
    }

    public function getVariantsInfolist(ProductQuantity $record): array
    {
        $templateProduct = $record->product->parent ?? $record->product;
        $locationId = $record->location_id;

        return [
            RepeatableEntry::make('variants')
                ->hiddenLabel()
                ->state(fn () => $templateProduct->variants->map(function ($variant) use ($locationId) {
                    $onHand = ProductQuantity::where('product_id', $variant->id)
                        ->where('location_id', $locationId)
                        ->sum('quantity');

                    $variant->setContext(['location_id' => $locationId]);
                    $forecast = $variant->virtual_available_qty;

                    return [
                        'id'       => $variant->id,
                        'name'     => $variant->name,
                        'price'    => $variant->price,
                        'cost'     => $variant->cost,
                        'on_hand'  => ($onHand == (int) $onHand) ? (int) $onHand : number_format($onHand, 2),
                        'forecast' => ($forecast == (int) $forecast) ? (int) $forecast : number_format($forecast, 2),
                        'unit'     => $variant->uom?->name ?? '—',
                    ];
                })->toArray())
                ->table([
                    InfolistTableColumn::make('name')
                        ->label(__('inventories::filament/clusters/configurations/resources/location/pages/manage-products.variants-infolist.name')),
                    InfolistTableColumn::make('price')
                        ->alignEnd()
                        ->label(__('inventories::filament/clusters/configurations/resources/location/pages/manage-products.variants-infolist.sales-price')),
                    InfolistTableColumn::make('cost')
                        ->alignEnd()
                        ->label(__('inventories::filament/clusters/configurations/resources/location/pages/manage-products.variants-infolist.cost-price')),
                    InfolistTableColumn::make('on_hand')
                        ->alignEnd()
                        ->label(__('inventories::filament/clusters/configurations/resources/location/pages/manage-products.variants-infolist.on-hand')),
                    InfolistTableColumn::make('forecast')
                        ->alignEnd()
                        ->label(__('inventories::filament/clusters/configurations/resources/location/pages/manage-products.variants-infolist.forecast')),
                    InfolistTableColumn::make('unit')
                        ->alignCenter()
                        ->label(__('inventories::filament/clusters/configurations/resources/location/pages/manage-products.variants-infolist.unit')),
                ])
                ->schema([
                    TextEntry::make('name')
                        ->label(__('inventories::filament/clusters/configurations/resources/location/pages/manage-products.variants-infolist.name')),
                    TextEntry::make('price')
                        ->label(__('inventories::filament/clusters/configurations/resources/location/pages/manage-products.variants-infolist.sales-price'))
                        ->money(),
                    TextEntry::make('cost')
                        ->label(__('inventories::filament/clusters/configurations/resources/location/pages/manage-products.variants-infolist.cost-price'))
                        ->money(),
                    TextEntry::make('on_hand')
                        ->label(__('inventories::filament/clusters/configurations/resources/location/pages/manage-products.variants-infolist.on-hand')),
                    TextEntry::make('forecast')
                        ->label(__('inventories::filament/clusters/configurations/resources/location/pages/manage-products.variants-infolist.forecast')),
                    TextEntry::make('unit')
                        ->label(__('inventories::filament/clusters/configurations/resources/location/pages/manage-products.variants-infolist.unit')),
                ])
                ->extraItemActions([
                    Action::make('viewVariants')
                        ->iconButton()
                        ->icon('heroicon-m-arrow-top-right-on-square')
                        ->url(fn (): string => ProductResource::getUrl('variants', ['record' => $templateProduct->id])),
                ]),
        ];
    }
}
