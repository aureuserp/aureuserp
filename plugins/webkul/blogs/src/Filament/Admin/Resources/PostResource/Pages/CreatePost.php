<?php

namespace Webkul\Blog\Filament\Admin\Resources\PostResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use Webkul\Blog\Filament\Admin\Resources\PostResource;
use Webkul\Support\Filament\Concerns\TranslatableCreateRecord;

class CreatePost extends CreateRecord
{
    use TranslatableCreateRecord;

    protected static string $resource = PostResource::class;

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
            ->title(__('blogs::filament/admin/resources/post/pages/create-post.notification.title'))
            ->body(__('blogs::filament/admin/resources/post/pages/create-post.notification.body'));
    }
}
