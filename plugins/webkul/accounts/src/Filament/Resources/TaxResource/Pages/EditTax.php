<?php

namespace Webkul\Account\Filament\Resources\TaxResource\Pages;

use Exception;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\QueryException;
use Webkul\Account\Filament\Resources\TaxResource;
use Webkul\Account\Models\Tax;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditTax extends EditRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = TaxResource::class;

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
            ViewAction::make(),
            DeleteAction::make()
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

    protected function beforeSave(): void
    {
        $data = $this->data;

        try {
            TaxResource::validateRepartitionData(
                $data['invoiceRepartitionLines'] ?? [],
                $data['refundRepartitionLines'] ?? []
            );
        } catch (Exception $e) {
            Notification::make()
                ->danger()
                ->title(__('accounts::filament/resources/tax/pages/edit-tax.header-actions.delete.notification.invalid-repartition-lines.title'))
                ->body($e->getMessage())
                ->send();

            $this->halt();
        }
    }
}
