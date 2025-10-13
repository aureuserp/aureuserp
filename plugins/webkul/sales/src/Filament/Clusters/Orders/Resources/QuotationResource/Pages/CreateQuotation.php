<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Webkul\Partner\Models\Partner;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Facades\SaleOrder;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource;
use Webkul\Support\Concerns\HasRepeaterColumnManager;

class CreateQuotation extends CreateRecord
{
    use HasRepeaterColumnManager;

    protected static string $resource = QuotationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('sales::filament/clusters/orders/resources/quotation/pages/create-quotation.notification.title'))
            ->body(__('sales::filament/clusters/orders/resources/quotation/pages/create-quotation.notification.body'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        $data['creator_id'] = $user->id;
        $data['user_id'] = $user->id;
        $data['company_id'] = $user->default_company_id;
        $data['state'] = OrderState::DRAFT;
        $data['create_date'] = now();

        if ($data['partner_id']) {
            $partner = Partner::find($data['partner_id']);
            $data['commercial_partner_id'] = $partner->id;
            $data['partner_shipping_id'] = $partner->id;
            $data['partner_invoice_id'] = $partner->id;
            $data['order_partner_id'] = $partner->id;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        SaleOrder::computeSaleOrder($this->getRecord());
    }
}
