<?php

namespace Webkul\Asset\Filament\Resources\AssetResource\Pages;

use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;
use Webkul\Asset\Enums\AssetStatus;
use Webkul\Asset\Filament\Resources\AssetResource;
use Webkul\Asset\Filament\Resources\AssetResource\Actions\IssueAssetAction;
use Webkul\Asset\Filament\Resources\AssetResource\Actions\ReturnAssetAction;
use Webkul\Asset\Filament\Resources\AssetResource\Actions\TerminateAssetAction;
use Webkul\Asset\Filament\Resources\AssetResource\Actions\TransferAssetAction;
use Webkul\Asset\Models\Asset;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Support\Filament\Concerns\HandlesCrossCompanyException;

class EditAsset extends EditRecord
{
    use HandlesCrossCompanyException;

    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            IssueAssetAction::make(),
            TransferAssetAction::make(),
            ReturnAssetAction::make(),
            ViewAction::make(),
            ChatterAction::make()
                ->resource(static::$resource),
            ActionGroup::make([
                TerminateAssetAction::make('scrap', AssetStatus::Scrapped),
                TerminateAssetAction::make('markLost', AssetStatus::Lost),
                DeleteAction::make()
                    ->using(function (Asset $record, DeleteAction $action): void {
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
            ]),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('assets::filament/resources/asset.pages.edit.notification.title'))
            ->body(__('assets::filament/resources/asset.pages.edit.notification.body'));
    }
}
