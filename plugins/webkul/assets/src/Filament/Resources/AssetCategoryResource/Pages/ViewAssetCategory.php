<?php

namespace Webkul\Asset\Filament\Resources\AssetCategoryResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Validation\ValidationException;
use Webkul\Asset\Filament\Resources\AssetCategoryResource;
use Webkul\Asset\Models\AssetCategory;

class ViewAssetCategory extends ViewRecord
{
    protected static string $resource = AssetCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make()
                ->using(function (AssetCategory $record, DeleteAction $action): void {
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
