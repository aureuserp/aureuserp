<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\QuotationTemplates\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\QuotationTemplates\QuotationTemplateResource;

class CreateQuotationTemplate extends CreateRecord
{
    protected static string $resource = QuotationTemplateResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('sales::filament/clusters/configurations/resources/quotation-template/pages/create-quotation-template.notification.title'))
            ->body(__('sales::filament/clusters/configurations/resources/quotation-template/pages/create-quotation-template.notification.body'));
    }
}
