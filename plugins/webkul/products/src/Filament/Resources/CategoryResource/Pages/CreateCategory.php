<?php

namespace Webkul\Product\Filament\Resources\CategoryResource\Pages;

use Exception;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Product\Filament\Resources\CategoryResource;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    public function create(bool $another = false): void
    {
        try {
            parent::create($another);
        } catch (Exception $e) {
            Notification::make()
                ->danger()
                ->title(__('products::filament/resources/category/pages/create-category.create.notification.error.title'))
                ->body($e->getMessage())
                ->send();
        }
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('products::filament/resources/category/pages/create-category.notification.title'))
            ->body(__('products::filament/resources/category/pages/create-category.notification.body'));
    }
}
