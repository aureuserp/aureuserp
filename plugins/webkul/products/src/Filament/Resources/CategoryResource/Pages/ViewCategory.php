<?php

namespace Webkul\Product\Filament\Resources\CategoryResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\QueryException;
use Webkul\Product\Filament\Resources\CategoryResource;
use Webkul\Product\Models\Category;

class ViewCategory extends ViewRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make()
                ->action(function (Category $record) {
                    try {
                        $record->delete();
                    } catch (QueryException $e) {
                        Notification::make()
                            ->danger()
                            ->title(__('products::filament/resources/category/pages/view-category.header-actions.delete.notification.error.title'))
                            ->body(__('products::filament/resources/category/pages/view-category.header-actions.delete.notification.error.body'))
                            ->send();
                    }
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('products::filament/resources/category/pages/view-category.header-actions.delete.notification.success.title'))
                        ->body(__('products::filament/resources/category/pages/view-category.header-actions.delete.notification.success.body')),
                ),
        ];
    }
}
