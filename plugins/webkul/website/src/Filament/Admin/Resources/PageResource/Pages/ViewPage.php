<?php

namespace Webkul\Website\Filament\Admin\Resources\PageResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Website\Filament\Admin\Resources\PageResource;

class ViewPage extends ViewRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
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
