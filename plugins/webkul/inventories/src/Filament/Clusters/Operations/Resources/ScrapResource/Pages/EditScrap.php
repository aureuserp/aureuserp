<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\QueryException;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Inventory\Enums\ScrapState;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource;
use Webkul\Inventory\Models\Scrap;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditScrap extends EditRecord
{
    use HasRecordNavigationTabs;
    
    protected ?bool $hasDatabaseTransactions = true;

    protected static string $resource = ScrapResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('inventories::filament/clusters/operations/resources/scrap/pages/edit-scrap.notification.title'))
            ->body(__('inventories::filament/clusters/operations/resources/scrap/pages/edit-scrap.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->resource(static::$resource)
                ->activityPlans($this->getRecord()->activityPlans()),
            Action::make('validate')
                ->label(__('inventories::filament/clusters/operations/resources/scrap/pages/edit-scrap.header-actions.validate.label'))
                ->color('gray')
                ->action(function (Scrap $record) {
                    if (! $record->validate()) {
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/operations/resources/scrap/pages/edit-scrap.header-actions.validate.notification.warning.title'))
                            ->body(__('inventories::filament/clusters/operations/resources/scrap/pages/edit-scrap.header-actions.validate.notification.warning.body'))
                            ->warning()
                            ->send();
                    }
                })
                ->hidden(fn () => $this->getRecord()->state == ScrapState::DONE),
            DeleteAction::make()
                ->hidden(fn () => $this->getRecord()->state == ScrapState::DONE)
                ->action(function (DeleteAction $action, Scrap $record) {
                    try {
                        $record->delete();

                        $action->success();
                    } catch (QueryException $e) {
                        Notification::make()
                            ->danger()
                            ->title(__('inventories::filament/clusters/operations/resources/scrap/pages/edit-scrap.header-actions.delete.notification.error.title'))
                            ->body(__('inventories::filament/clusters/operations/resources/scrap/pages/edit-scrap.header-actions.delete.notification.error.body'))
                            ->send();

                        $action->failure();
                    }
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('inventories::filament/clusters/operations/resources/scrap/pages/edit-scrap.header-actions.delete.notification.success.title'))
                        ->body(__('inventories::filament/clusters/operations/resources/scrap/pages/edit-scrap.header-actions.delete.notification.success.body')),
                ),
        ];
    }
}
