<?php

namespace Webkul\Asset\Filament\Resources\AssetSiteResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;
use Webkul\Asset\Filament\Resources\AssetSiteResource;
use Webkul\Asset\Models\AssetSite;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Support\Filament\Concerns\HandlesCrossCompanyException;

class EditAssetSite extends EditRecord
{
    use HandlesCrossCompanyException;

    protected static string $resource = AssetSiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            ChatterAction::make()
                ->resource(static::$resource),
            DeleteAction::make()
                ->using(function (AssetSite $record, DeleteAction $action): void {
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
            ->title(__('assets::filament/resources/asset-site.pages.edit.notification.title'))
            ->body(__('assets::filament/resources/asset-site.pages.edit.notification.body'));
    }
}
