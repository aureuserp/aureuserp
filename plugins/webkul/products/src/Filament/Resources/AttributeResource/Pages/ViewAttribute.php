<?php

namespace Webkul\Product\Filament\Resources\AttributeResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Product\Filament\Resources\AttributeResource;
use Webkul\Product\Models\Attribute;

class ViewAttribute extends ViewRecord
{
    protected static string $resource = AttributeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make()
                ->before(function (DeleteAction $action, Attribute $record) {
                    if ($record->productAttributes()->exists()) {
                        Notification::make()
                            ->danger()
                            ->title(__('products::filament/resources/attribute/pages/view-attribute.header-actions.delete.notification.error.title'))
                            ->body(__('products::filament/resources/attribute/pages/view-attribute.header-actions.delete.notification.error.body', [
                                'products' => AttributeResource::blockingProductNames($record),
                            ]))
                            ->send();

                        $action->halt();
                    }
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('products::filament/resources/attribute/pages/view-attribute.header-actions.delete.notification.success.title'))
                        ->body(__('products::filament/resources/attribute/pages/view-attribute.header-actions.delete.notification.success.body')),
                ),
        ];
    }
}
