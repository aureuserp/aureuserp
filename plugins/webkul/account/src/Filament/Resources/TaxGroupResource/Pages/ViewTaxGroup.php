<?php

namespace Webkul\Account\Filament\Resources\TaxGroupResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\QueryException;
use Webkul\Account\Filament\Resources\TaxGroupResource;
use Webkul\Account\Models\TaxGroup;

class ViewTaxGroup extends ViewRecord
{
    protected static string $resource = TaxGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make()
                ->action(function (TaxGroup $record) {
                    try {
                        $record->delete();
                    } catch (QueryException $e) {
                        Notification::make()
                            ->danger()
                            ->title(__('accounts::filament/resources/tax-group/pages/view-tax-group.header-actions.delete.notification.error.title'))
                            ->body(__('accounts::filament/resources/tax-group/pages/view-tax-group.header-actions.delete.notification.error.body'))
                            ->send();
                    }
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('accounts::filament/resources/tax-group/pages/view-tax-group.header-actions.delete.notification.success.title'))
                        ->body(__('accounts::filament/resources/tax-group/pages/view-tax-group.header-actions.delete.notification.success.body'))
                ),
        ];
    }
}
