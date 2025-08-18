<?php

namespace Webkul\Blog\Filament\Admin\Resources\PostResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Blog\Filament\Admin\Resources\PostResource;

class ViewPost extends ViewRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('blogs::filament/admin/resources/post/pages/view-post.header-actions.delete.notification.title'))
                        ->body(__('blogs::filament/admin/resources/post/pages/view-post.header-actions.delete.notification.body')),
                ),
        ];
    }
}
