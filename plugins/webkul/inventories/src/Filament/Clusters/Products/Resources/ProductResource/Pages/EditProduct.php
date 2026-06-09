<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages;

use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Actions\UpdateQuantityAction;
use Webkul\Product\Filament\Resources\ProductResource\Pages\EditProduct as BaseEditProduct;

class EditProduct extends BaseEditProduct
{
    protected static string $resource = ProductResource::class;

    protected function beforeSave(): void
    {
        $record = $this->getRecord();
        $data = $this->form->getState();

        if (isset($data['is_storable']) && $data['is_storable'] != $record->is_storable) {
            if ($record->moveLines()->exists()) {
                Notification::make()
                    ->danger()
                    ->title(__('inventories::filament/clusters/products/resources/product/pages/edit-product.before-save.notification.error.tracking-update.title'))
                    ->body(__('inventories::filament/clusters/products/resources/product/pages/edit-product.before-save.notification.error.tracking-update.body'))
                    ->persistent()
                    ->send();

                throw new Halt();
            }
        }

        if (isset($data['tracking']) && $data['tracking'] != $record->tracking) {
            if ($record->moveLines()->exists()) {
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

                        throw new Halt();
                    }
                }

                Notification::make()
                    ->danger()
                    ->title(__('inventories::filament/clusters/products/resources/product/pages/edit-product.before-save.notification.error.tracking-update.title'))
                    ->body(__('inventories::filament/clusters/products/resources/product/pages/edit-product.before-save.notification.error.tracking-update.body'))
                    ->persistent()
                    ->send();

                throw new Halt();
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return array_merge([
            UpdateQuantityAction::make(),
        ], parent::getHeaderActions());
    }
}
