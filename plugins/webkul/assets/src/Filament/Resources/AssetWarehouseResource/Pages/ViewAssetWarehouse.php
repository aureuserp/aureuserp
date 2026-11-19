<?php

namespace Webkul\Asset\Filament\Resources\AssetWarehouseResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Validation\ValidationException;
use Webkul\Asset\Filament\Resources\AssetWarehouseResource;
use Webkul\Asset\Models\AssetWarehouse;
use Webkul\Chatter\Filament\Actions\ChatterAction;

class ViewAssetWarehouse extends ViewRecord
{
    protected static string $resource = AssetWarehouseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
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
}
