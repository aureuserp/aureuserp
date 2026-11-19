<?php

namespace Webkul\Asset\Filament\Resources\AssetCategoryResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;
use Webkul\Asset\Filament\Resources\AssetCategoryResource;
use Webkul\Asset\Models\AssetCategory;
use Webkul\Support\Filament\Concerns\HandlesCrossCompanyException;

class EditAssetCategory extends EditRecord
{
    use HandlesCrossCompanyException;

    protected static string $resource = AssetCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
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

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('assets::filament/resources/category.pages.edit.notification.title'))
            ->body(__('assets::filament/resources/category.pages.edit.notification.body'));
    }
}
