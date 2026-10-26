<?php

namespace Webkul\Inventory\Filament\Clusters\Reporting\Resources\QuantityResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Filament\Clusters\Reporting\Resources\QuantityResource;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Inventory\Models\Warehouse;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ManageQuantities extends ManageRecords
{
    use HasTableViews;

    protected static string $resource = QuantityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->mutateDataUsing(function (array $data): array {
                    $data['location_id'] = $data['location_id'] ?? Warehouse::query()->when(Product::find($data['product_id'])?->company_id, fn ($query, $scopedCompanyId) => $query->where(owned_by_company($scopedCompanyId)))->value('lot_stock_location_id');

                    $data['company_id'] = Product::find($data['product_id'])?->company_id;

                    $data['inventory_diff_quantity'] = $data['quantity'];

                    return $data;
                })
                ->before(function (CreateAction $action, array $data) {
                    $existingQuantity = ProductQuantity::where('location_id', $data['location_id'] ?? Warehouse::query()->when(Product::find($data['product_id'])?->company_id, fn ($query, $scopedCompanyId) => $query->where(owned_by_company($scopedCompanyId)))->value('lot_stock_location_id'))
                        ->where('product_id', $data['product_id'])
                        ->where('package_id', $data['package_id'] ?? null)
                        ->where('lot_id', $data['lot_id'] ?? null)
                        ->exists();

                    if ($existingQuantity) {
                        Notification::make()
                            ->title(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.header-actions.create.before.notification.title'))
                            ->body(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.header-actions.create.before.notification.body'))
                            ->warning()
                            ->send();

                        $action->halt();
                    }
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.header-actions.create.notification.title'))
                        ->body(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.header-actions.create.notification.body')),
                ),
        ];
    }

    public function getPresetTableViews(): array
    {
        return [
            'internal_locations' => PresetView::make(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.tabs.internal-locations'))
                ->favorite()
                ->setAsDefault()
                ->icon('heroicon-s-building-office')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->whereHas('location', function (Builder $query) {
                        $query->where('type', LocationType::INTERNAL);
                    });
                }),
            'transit_locations' => PresetView::make(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.tabs.transit-locations'))
                ->favorite()
                ->icon('heroicon-s-truck')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->whereHas('location', function (Builder $query) {
                        $query->where('type', LocationType::TRANSIT);
                    });
                }),
            'on_hand' => PresetView::make(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.tabs.on-hand'))
                ->favorite()
                ->icon('heroicon-s-clipboard-document-list')
                ->modifyQueryUsing(function (Builder $query) {
                    $query
                        ->where('quantity', '>', 0)
                        ->whereHas('location', function (Builder $query) {
                            $query->where('is_scrap', false);
                        });
                }),
            'to_count' => PresetView::make(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.tabs.to-count'))
                ->favorite()
                ->icon('heroicon-s-calculator')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('scheduled_at', '>', now())),
            'to_apply' => PresetView::make(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.tabs.to-apply'))
                ->favorite()
                ->icon('heroicon-s-check-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('inventory_quantity_set', true)),
        ];
    }
}
