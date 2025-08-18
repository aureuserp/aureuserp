<?php

namespace Webkul\Account\Filament\Resources\CashRoundingResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Account\Filament\Resources\CashRoundingResource;

class ViewCashRounding extends ViewRecord
{
    protected static string $resource = CashRoundingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('accounts::filament/resources/cash-rounding/pages/view-cash-rounding.header-actions.delete.notification.title'))
                        ->body(__('accounts::filament/resources/cash-rounding/pages/view-cash-rounding.header-actions.delete.notification.body'))
                ),
        ];
    }
}
