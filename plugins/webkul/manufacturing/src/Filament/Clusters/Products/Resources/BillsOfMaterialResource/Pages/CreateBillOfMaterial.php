<?php

namespace Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource;
use Webkul\Support\Filament\Concerns\HasRepeaterColumnManager;

class CreateBillOfMaterial extends CreateRecord
{
    use HasRepeaterColumnManager;

    protected static string $resource = BillsOfMaterialResource::class;

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Start;
    }

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return BillsOfMaterialResource::normalizeProductVariantData($data);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('manufacturing::filament/clusters/products/resources/bill-of-material/pages/create-bill-of-material.notification.title'))
            ->body(__('manufacturing::filament/clusters/products/resources/bill-of-material/pages/create-bill-of-material.notification.body'));
    }
}
