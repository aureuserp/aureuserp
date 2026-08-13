<?php

namespace Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource;
use Webkul\Support\Filament\Concerns\HasRepeaterColumnManager;

class CreateManufacturingOrder extends CreateRecord
{
    use HasRepeaterColumnManager;

    protected ?bool $hasDatabaseTransactions = true;

    protected static string $resource = ManufacturingOrderResource::class;

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

    public function getTitle(): string
    {
        return __('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/create-manufacturing-order.title');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['destination_location_id'] = $data['final_location_id'] ?? $data['destination_location_id'] ?? null;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/create-manufacturing-order.notification.title'))
            ->body(__('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/create-manufacturing-order.notification.body'));
    }
}
