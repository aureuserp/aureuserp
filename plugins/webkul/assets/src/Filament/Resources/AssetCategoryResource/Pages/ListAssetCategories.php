<?php

namespace Webkul\Asset\Filament\Resources\AssetCategoryResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Webkul\Asset\Filament\Resources\AssetCategoryResource;

class ListAssetCategories extends ListRecords
{
    protected static string $resource = AssetCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('assets::filament/resources/category.pages.list.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('assets::filament/resources/category.pages.list.header-actions.create.notification.title'))
                        ->body(__('assets::filament/resources/category.pages.list.header-actions.create.notification.body')),
                ),
        ];
    }
}
