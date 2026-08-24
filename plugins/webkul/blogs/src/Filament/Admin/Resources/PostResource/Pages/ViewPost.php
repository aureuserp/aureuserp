<?php

namespace Webkul\Blog\Filament\Admin\Resources\PostResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use Webkul\Blog\Filament\Admin\Resources\PostResource;
use Webkul\Support\Filament\Concerns\TranslatableViewRecord;

class ViewPost extends ViewRecord
{
    use TranslatableViewRecord;

    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
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
