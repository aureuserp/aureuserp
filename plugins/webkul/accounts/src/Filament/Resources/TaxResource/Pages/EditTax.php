<?php

namespace Webkul\Account\Filament\Resources\TaxResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\QueryException;
use Webkul\Account\Filament\Resources\TaxResource;
use Webkul\Account\Models\Tax;

class EditTax extends EditRecord
{
    protected static string $resource = TaxResource::class;

    public function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('accounts::filament/resources/tax/pages/edit-tax.notification.title'))
            ->body(__('accounts::filament/resources/tax/pages/edit-tax.notification.body'));
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->action(function (Tax $record) {
                    try {
                        $record->delete();
                    } catch (QueryException $e) {
                        Notification::make()
                            ->danger()
                            ->title(__('accounts::filament/resources/tax/pages/edit-tax.header-actions.delete.notification.error.title'))
                            ->body(__('accounts::filament/resources/tax/pages/edit-tax.header-actions.delete.notification.error.body'))
                            ->send();
                    }
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('accounts::filament/resources/tax/pages/edit-tax.header-actions.delete.notification.success.title'))
                        ->body(__('accounts::filament/resources/tax/pages/edit-tax.header-actions.delete.notification.success.body'))
                ),
        ];
    }
}
