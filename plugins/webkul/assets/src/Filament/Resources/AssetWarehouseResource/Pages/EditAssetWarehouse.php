<?php

namespace Webkul\Asset\Filament\Resources\AssetWarehouseResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;
use Webkul\Asset\Filament\Resources\AssetWarehouseResource;
use Webkul\Asset\Models\AssetWarehouse;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Support\Filament\Concerns\HandlesCrossCompanyException;

class EditAssetWarehouse extends EditRecord
{
    use HandlesCrossCompanyException;

    protected static string $resource = AssetWarehouseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            ChatterAction::make()
                ->resource(static::$resource),
            DeleteAction::make()
                ->using(function (AssetWarehouse $record, DeleteAction $action): void {
                    try {
                        $record->delete();
                    } catch (ValidationException $e) {
                        Notification::make()
                            ->danger()
                            ->title(collect($e->errors())->flatten()->first())
                            ->send();

                        $action->halt();
                    }
                }),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('assets::filament/resources/asset-warehouse.pages.edit.notification.title'))
            ->body(__('assets::filament/resources/asset-warehouse.pages.edit.notification.body'));
    }
}
