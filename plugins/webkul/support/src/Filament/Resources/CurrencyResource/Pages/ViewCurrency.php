<?php

namespace Webkul\Support\Filament\Resources\CurrencyResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Support\Filament\Resources\CurrencyResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewCurrency extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = CurrencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('support::filament/resources/currency/pages/view-currency.header-actions.delete.notification.title'))
                        ->body(__('support::filament/resources/currency/pages/view-currency.header-actions.delete.notification.body')),
                ),
        ];
    }
}
