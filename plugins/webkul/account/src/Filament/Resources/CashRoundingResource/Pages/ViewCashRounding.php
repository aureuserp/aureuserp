<?php

namespace Webkul\Account\Filament\Resources\CashRoundingResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Account\Filament\Resources\CashRoundingResource;

class ViewCashRounding extends ViewRecord
{
    protected static string $resource = CashRoundingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('accounts::filament/resources/cash-rounding/pages/view-cash-rounding.header-actions.delete.notification.title'))
                        ->body(__('accounts::filament/resources/cash-rounding/pages/view-cash-rounding.header-actions.delete.notification.body'))
                ),
        ];
    }
}
