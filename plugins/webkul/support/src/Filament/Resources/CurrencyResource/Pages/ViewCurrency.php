<?php

namespace Webkul\Support\Filament\Resources\CurrencyResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\QueryException;
use Webkul\Support\Filament\Resources\CurrencyResource;
use Webkul\Support\Models\Currency;
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
                ->action(function (Currency $record, DeleteAction $action) {
                    try {
                        $record->delete();

                        $action->success();
                    } catch (QueryException $e) {
                        $action->failure();

                        Notification::make()
                            ->danger()
                            ->title(__('support::filament/resources/currency/pages/view-currency.header-actions.delete.notification.error.title'))
                            ->body(__('support::filament/resources/currency/pages/view-currency.header-actions.delete.notification.error.body'))
                            ->send();
                    }
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('support::filament/resources/currency/pages/view-currency.header-actions.delete.notification.title'))
                        ->body(__('support::filament/resources/currency/pages/view-currency.header-actions.delete.notification.body')),
                ),
        ];
    }
}
