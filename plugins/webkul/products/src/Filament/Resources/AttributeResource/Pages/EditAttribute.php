<?php

namespace Webkul\Product\Filament\Resources\AttributeResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Product\Filament\Resources\AttributeResource;
use Webkul\Product\Models\Attribute;

class EditAttribute extends EditRecord
{
    protected static string $resource = AttributeResource::class;

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('products::filament/resources/attribute/pages/edit-attribute.notification.title'))
            ->body(__('products::filament/resources/attribute/pages/edit-attribute.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->before(function (DeleteAction $action, Attribute $record) {
                    if ($record->productAttributes()->exists()) {
                        Notification::make()
                            ->danger()
                            ->title(__('products::filament/resources/attribute/pages/edit-attribute.header-actions.delete.notification.error.title'))
                            ->body(__('products::filament/resources/attribute/pages/edit-attribute.header-actions.delete.notification.error.body', [
                                'products' => AttributeResource::blockingProductNames($record),
                            ]))
                            ->send();

                        $action->halt();
                    }
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('products::filament/resources/attribute/pages/edit-attribute.header-actions.delete.notification.success.title'))
                        ->body(__('products::filament/resources/attribute/pages/edit-attribute.header-actions.delete.notification.success.body')),
                ),
        ];
    }
}
