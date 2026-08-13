<?php

namespace Webkul\Website\Filament\Admin\Resources\PageResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;
use Webkul\Website\Filament\Admin\Resources\PageResource;

class CreatePage extends CreateRecord
{
    use Translatable;

    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('website::filament/admin/resources/page/pages/create-record.notification.title'))
            ->body(__('website::filament/admin/resources/page/pages/create-record.notification.body'));
    }
}
