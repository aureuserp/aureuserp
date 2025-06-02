<?php

namespace Webkul\Account\Filament\Resources\TaxResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\QueryException;
use Webkul\Account\Filament\Resources\TaxResource;
use Webkul\Account\Models\Tax;

class ViewTax extends ViewRecord
{
    protected static string $resource = TaxResource::class;

    public function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make()
                ->action(function (Tax $record) {
                    try {
                        $record->delete();
                    } catch (QueryException $e) {
                        Notification::make()
                            ->danger()
                            ->title(__('accounts::filament/resources/tax/pages/view-tax.header-actions.delete.notification.error.title'))
                            ->body(__('accounts::filament/resources/tax/pages/view-tax.header-actions.delete.notification.error.body'))
                            ->send();
                    }
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('accounts::filament/resources/tax/pages/view-tax.header-actions.delete.notification.title'))
                        ->body(__('accounts::filament/resources/tax/pages/view-tax.header-actions.delete.notification.body'))
                ),
        ];
    }
}
