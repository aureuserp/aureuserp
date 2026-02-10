<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\DropshipResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Inventory\Enums;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DropshipResource;
use Webkul\Inventory\Models\OperationType;

class CreateDropship extends CreateRecord
{
    protected static string $resource = DropshipResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    public function getTitle(): string|Htmlable
    {
        return __('inventories::filament/clusters/operations/resources/dropship/pages/create-dropship.title');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('inventories::filament/clusters/operations/resources/dropship/pages/create-dropship.notification.title'))
            ->body(__('inventories::filament/clusters/operations/resources/dropship/pages/create-dropship.notification.body'));
    }

    public function mount(): void
    {
        parent::mount();

        $operationType = OperationType::where('type', Enums\OperationType::DROPSHIP)->first();

        $this->data['operation_type_id'] = $operationType?->id;

        $this->data['source_location_id'] = $operationType?->source_location_id;

        $this->data['destination_location_id'] = $operationType?->destination_location_id;

        $this->form->fill($this->data);
    }
}
