<?php

namespace Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource;
use Webkul\Support\Filament\Concerns\HasRepeaterColumnManager;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditBillOfMaterial extends EditRecord
{
    use HasRecordNavigationTabs, HasRepeaterColumnManager;

    protected static string $resource = BillsOfMaterialResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return BillsOfMaterialResource::normalizeProductVariantData($data);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('manufacturing::filament/clusters/products/resources/bill-of-material/pages/edit-bill-of-material.notification.title'))
            ->body(__('manufacturing::filament/clusters/products/resources/bill-of-material/pages/edit-bill-of-material.notification.body'));
    }
}
