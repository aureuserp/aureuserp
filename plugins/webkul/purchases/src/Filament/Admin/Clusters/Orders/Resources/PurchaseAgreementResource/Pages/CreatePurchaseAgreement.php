<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Purchase\Enums\RequisitionState;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource;
use Webkul\Purchase\Models\Requisition;
use Webkul\Support\Filament\Concerns\HasRepeaterColumnManager;

class CreatePurchaseAgreement extends CreateRecord
{
    use HasRepeaterColumnManager;

    protected static string $resource = PurchaseAgreementResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

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

    protected function getCreateFormAction(): Action
    {
        return Action::make('create')
            ->label(__('filament-panels::resources/pages/create-record.form.actions.create.label'))
            ->action(fn () => $this->create())
            ->keyBindings(['mod+s'])
            ->requiresConfirmation(fn () => $this->hasAnotherConfirmedAgreementForSelectedVendor())
            ->modalHeading(fn () => $this->hasAnotherConfirmedAgreementForSelectedVendor()
                ? __('purchases::filament/admin/clusters/orders/resources/purchase-agreement/pages/create-purchase-agreement.confirmation.heading')
                : null
            )
            ->modalDescription(fn () => $this->hasAnotherConfirmedAgreementForSelectedVendor()
                ? __('purchases::filament/admin/clusters/orders/resources/purchase-agreement/pages/create-purchase-agreement.confirmation.description')
                : null
            );
    }

    protected function hasAnotherConfirmedAgreementForSelectedVendor(): bool
    {
        $partnerId = data_get($this->form->getRawState(), 'partner_id', $this->data['partner_id'] ?? null);
        $companyId = data_get($this->form->getRawState(), 'company_id', $this->data['company_id'] ?? null);
        $requisitionType = data_get($this->form->getRawState(), 'type', $this->data['type'] ?? null);

        if (! filled($partnerId) || ! filled($companyId) || ! filled($requisitionType)) {
            return false;
        }

        return Requisition::query()
            ->where('partner_id', $partnerId)
            ->where('company_id', $companyId)
            ->where('type', $requisitionType)
            ->where('state', RequisitionState::CONFIRMED)
            ->exists();
    }
}
