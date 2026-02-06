<?php

namespace Webkul\Product\Filament\Resources\CategoryResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Webkul\Product\Filament\Resources\CategoryResource;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('products::filament/resources/category/pages/list-categories.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('products::filament/resources/category/pages/list-categories.header-actions.create.notification.title'))
                        ->body(__('products::filament/resources/category/pages/list-categories.header-actions.create.notification.body')),
                ),
        ];
    }
}
