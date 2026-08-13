<?php

namespace Webkul\Website\Filament\Admin\Resources\PageResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\ViewRecord\Concerns\Translatable;
use Webkul\Website\Filament\Admin\Resources\PageResource;

class ViewPage extends ViewRecord
{
    use Translatable;

    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('website::filament/admin/resources/page/pages/view-record.header-actions.delete.notification.title'))
                        ->body(__('website::filament/admin/resources/page/pages/view-record.header-actions.delete.notification.body')),
                ),
        ];
    }
}
