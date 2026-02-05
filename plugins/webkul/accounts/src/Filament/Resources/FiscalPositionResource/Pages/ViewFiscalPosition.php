<?php

namespace Webkul\Account\Filament\Resources\FiscalPositionResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Account\Filament\Resources\FiscalPositionResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewFiscalPosition extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = FiscalPositionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('accounts::filament/resources/fiscal-position/pages/view-fiscal-position.header-actions.delete.notification.title'))
                        ->body(__('accounts::filament/resources/fiscal-position/pages/view-fiscal-position.header-actions.delete.notification.body'))
                ),
        ];
    }
}
