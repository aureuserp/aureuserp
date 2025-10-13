<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\QueryException;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Inventory\Enums\ScrapState;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource;
use Webkul\Inventory\Models\Scrap;

class ViewScrap extends ViewRecord
{
    protected static string $resource = ScrapResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->setResource(static::$resource),
            DeleteAction::make()
                ->hidden(fn () => $this->getRecord()->state == ScrapState::DONE)
                ->action(function (DeleteAction $action, Scrap $record) {
                    try {
                        $record->delete();

                        $action->success();
                    } catch (QueryException $e) {
                        Notification::make()
                            ->danger()
                            ->title(__('inventories::filament/clusters/operations/resources/scrap/pages/view-scrap.header-actions.delete.notification.error.title'))
                            ->body(__('inventories::filament/clusters/operations/resources/scrap/pages/view-scrap.header-actions.delete.notification.error.body'))
                            ->send();

                        $action->failure();
                    }
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('inventories::filament/clusters/operations/resources/scrap/pages/view-scrap.header-actions.delete.notification.success.title'))
                        ->body(__('inventories::filament/clusters/operations/resources/scrap/pages/view-scrap.header-actions.delete.notification.success.body')),
                ),
        ];
    }
}
