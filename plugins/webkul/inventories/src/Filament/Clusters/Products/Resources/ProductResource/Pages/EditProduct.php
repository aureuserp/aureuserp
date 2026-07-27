<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages;

use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource;
use Webkul\Product\Filament\Resources\ProductResource\Pages\EditProduct as BaseEditProduct;

class EditProduct extends BaseEditProduct
{
    protected static string $resource = ProductResource::class;

    protected function beforeSave(): void
    {
        $record = $this->getRecord();

        $data = $this->form->getState();

        if (isset($data['is_storable']) && ! $data['is_storable'] && $record->orderPoints()->exists()) {
            Notification::make()
                ->danger()
                ->title(__('inventories::filament/clusters/products/resources/product/pages/edit-product.before-save.notification.error.reordering-rules.title'))
                ->body(__('inventories::filament/clusters/products/resources/product/pages/edit-product.before-save.notification.error.reordering-rules.body'))
                ->persistent()
                ->send();

            throw new Halt;
        }

        if (isset($data['is_storable']) && $data['is_storable'] != $record->is_storable) {
            if ($record->moveLines()->where('state', MoveState::DONE)->exists()) {
                Notification::make()
                    ->danger()
                    ->title(__('inventories::filament/clusters/products/resources/product/pages/edit-product.before-save.notification.error.tracking-update.title'))
                    ->body(__('inventories::filament/clusters/products/resources/product/pages/edit-product.before-save.notification.error.tracking-update.body'))
                    ->persistent()
                    ->send();

                throw new Halt;
            }

            if ($record->moveLines()->whereIn('state', [MoveState::PARTIALLY_ASSIGNED, MoveState::ASSIGNED])->exists()) {
                Notification::make()
                    ->danger()
                    ->title(__('inventories::filament/clusters/products/resources/product/pages/edit-product.before-save.notification.error.reserved.title'))
                    ->body(__('inventories::filament/clusters/products/resources/product/pages/edit-product.before-save.notification.error.reserved.body'))
                    ->persistent()
                    ->send();

                throw new Halt;
            }
        }

        if (isset($data['is_storable']) && ! $data['is_storable'] && ! float_is_zero($record->available_qty, precisionRounding: $record->uom->rounding)) {
            Notification::make()
                ->danger()
                ->title(__('inventories::filament/clusters/products/resources/product/pages/edit-product.before-save.notification.error.available-quantity.title'))
                ->body(__('inventories::filament/clusters/products/resources/product/pages/edit-product.before-save.notification.error.available-quantity.body'))
                ->persistent()
                ->send();

            throw new Halt;
        }

        if (isset($data['tracking']) && $data['tracking'] != $record->tracking) {
            $oldTracking = $record->tracking;
            $newTracking = is_string($data['tracking']) ? ProductTracking::from($data['tracking']) : $data['tracking'];

            if (
                $oldTracking == ProductTracking::QTY
                && (
                    $newTracking == ProductTracking::LOT
                    || $newTracking == ProductTracking::SERIAL
                )
            ) {
                $hasStockWithoutLot = $record->quantities()
                    ->whereHas('location', function ($query) {
                        $query->where('type', LocationType::INTERNAL);
                    })
                    ->where('quantity', '>', 0)
                    ->whereNull('lot_id')
                    ->exists();

                if ($hasStockWithoutLot) {
                    Notification::make()
                        ->danger()
                        ->title(__('inventories::filament/clusters/products/resources/product/pages/edit-product.before-save.notification.error.track-by-update.title'))
                        ->body(__('inventories::filament/clusters/products/resources/product/pages/edit-product.before-save.notification.error.track-by-update.body'))
                        ->persistent()
                        ->send();

                    throw new Halt;
                }
            }
        }
    }

}
