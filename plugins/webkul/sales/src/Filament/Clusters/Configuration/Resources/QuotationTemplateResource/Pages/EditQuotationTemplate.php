<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\QuotationTemplateResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\EditRecord;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\QuotationTemplateResource;

class EditQuotationTemplate extends EditRecord
{
    protected static string $resource = QuotationTemplateResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('sales::filament/clusters/configurations/resources/quotation-template/pages/edit-quotation-template.notification.title'))
            ->body(__('sales::filament/clusters/configurations/resources/quotation-template/pages/edit-quotation-template.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('sales::filament/clusters/configurations/resources/quotation-template/pages/edit-quotation-template.header-actions.notification.delete.title'))
                        ->body(__('sales::filament/clusters/configurations/resources/quotation-template/pages/edit-quotation-template.header-actions.notification.delete.body'))
                ),
        ];
    }
}
