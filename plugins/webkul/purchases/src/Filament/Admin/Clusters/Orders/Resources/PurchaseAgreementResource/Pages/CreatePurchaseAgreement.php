<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Webkul\Purchase\Enums\RequisitionState;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource;
use Webkul\Support\Concerns\HasRepeaterColumnManager;

class CreatePurchaseAgreement extends CreateRecord
{
    use HasRepeaterColumnManager;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected static string $resource = PurchaseAgreementResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('purchases::filament/admin/clusters/orders/resources/purchase-agreement/pages/create-purchase-agreement.title');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement/pages/create-purchase-agreement.notification.title'))
            ->body(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement/pages/create-purchase-agreement.notification.body'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['creator_id'] = Auth::id();

        $data['state'] ??= RequisitionState::DRAFT;

        return $data;
    }
}
