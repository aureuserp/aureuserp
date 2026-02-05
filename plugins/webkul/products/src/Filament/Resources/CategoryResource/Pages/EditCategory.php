<?php

namespace Webkul\Product\Filament\Resources\CategoryResource\Pages;

use Exception;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\QueryException;
use Webkul\Product\Filament\Resources\CategoryResource;
use Webkul\Product\Models\Category;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditCategory extends EditRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->action(function (DeleteAction $action, Category $record) {
                    try {
                        $record->delete();

                        $action->success();
                    } catch (QueryException $e) {
                        Notification::make()
                            ->danger()
                            ->title(__('products::filament/resources/category/pages/edit-category.header-actions.delete.notification.error.title'))
                            ->body(__('products::filament/resources/category/pages/edit-category.header-actions.delete.notification.error.body'))
                            ->send();

                        $action->failure();
                    }
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('products::filament/resources/category/pages/edit-category.header-actions.delete.notification.success.title'))
                        ->body(__('products::filament/resources/category/pages/edit-category.header-actions.delete.notification.success.body')),
                ),
        ];
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        try {
            parent::save($shouldRedirect, $shouldSendSavedNotification);
        } catch (Exception $e) {
            Notification::make()
                ->danger()
                ->title(__('products::filament/resources/category/pages/edit-category.save.notification.error.title'))
                ->body($e->getMessage())
                ->send();
        }
    }

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('products::filament/resources/category/pages/edit-category.notification.title'))
            ->body(__('products::filament/resources/category/pages/edit-category.notification.body'));
    }
}
