<?php

namespace Webkul\Support\Filament\Resources\CurrencyResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Support\Filament\Resources\CurrencyResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditCurrency extends EditRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = CurrencyResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('support::filament/resources/currency/pages/edit-currency.notification.title'))
            ->body(__('support::filament/resources/currency/pages/edit-currency.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('support::filament/resources/currency/pages/edit-currency.header-actions.delete.notification.title'))
                        ->body(__('support::filament/resources/currency/pages/edit-currency.header-actions.delete.notification.body')),
                ),
        ];
    }
}
