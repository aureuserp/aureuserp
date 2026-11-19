<?php

namespace Webkul\Asset\Filament\Resources\ProductResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Validation\ValidationException;
use Webkul\Asset\Filament\Resources\ProductResource;
use Webkul\Asset\Models\AssetProduct;
use Webkul\Chatter\Filament\Actions\ChatterAction;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->resource(static::$resource),
            EditAction::make(),
            DeleteAction::make()
                ->using(function (AssetProduct $record, DeleteAction $action): void {
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
