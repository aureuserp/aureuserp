<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\InternalResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Enums;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\InternalResource;
use Webkul\Inventory\Models\OperationType;

class CreateInternal extends CreateRecord
{
    protected static string $resource = InternalResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    public function getTitle(): string|Htmlable
    {
        return __('inventories::filament/clusters/operations/resources/internal/pages/create-internal.title');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('inventories::filament/clusters/operations/resources/internal/pages/create-internal.notification.title'))
            ->body(__('inventories::filament/clusters/operations/resources/internal/pages/create-internal.notification.body'));
    }

    public function mount(): void
    {
        parent::mount();

        $operationType = OperationType::where('type', Enums\OperationType::INTERNAL)->first();

        $this->data['operation_type_id'] = $operationType?->id;

        $this->data['source_location_id'] = $operationType?->source_location_id;

        $this->data['destination_location_id'] = $operationType?->destination_location_id;

        $this->form->fill($this->data);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $operationType = OperationType::find($data['operation_type_id']);

        $data['company_id'] ??= $operationType->destinationLocation->company_id;

        $data['source_location_id'] ??= $operationType->source_location_id;

        $data['destination_location_id'] ??= $operationType->destination_location_id;

        $data['state'] ??= OperationState::DRAFT;

        $data['creator_id'] = Auth::id();

        return $data;
    }
}
