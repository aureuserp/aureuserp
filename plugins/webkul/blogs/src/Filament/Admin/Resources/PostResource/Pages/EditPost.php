<?php

namespace Webkul\Blog\Filament\Admin\Resources\PostResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Webkul\Blog\Filament\Admin\Resources\PostResource;
use Webkul\Blog\Models\Post;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['last_editor_id'] = Auth::id();

        return $data;
    }

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('blogs::filament/admin/resources/post/pages/edit-post.notification.title'))
            ->body(__('blogs::filament/admin/resources/post/pages/edit-post.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('publish')
                ->label(__('blogs::filament/admin/resources/post/pages/edit-post.header-actions.publish.label'))
                ->icon('heroicon-o-check-circle')
                ->action(function (Post $record) {
                    $record->update([
                        'last_editor_id' => Auth::id(),
                        'published_at'   => now(),
                        'is_published'   => true,
                    ]);

                    Notification::make()
                        ->success()
                        ->title(__('blogs::filament/admin/resources/post/pages/edit-post.header-actions.publish.notification.title'))
                        ->body(__('blogs::filament/admin/resources/post/pages/edit-post.header-actions.publish.notification.body'))
                        ->send();
                })
                ->visible(fn (Post $record) => ! $record->is_published),
            Action::make('draft')
                ->label(__('blogs::filament/admin/resources/post/pages/edit-post.header-actions.draft.label'))
                ->icon('heroicon-o-archive-box')
                ->action(function (Post $record) {
                    $record->update(['is_published' => false]);

                    Notification::make()
                        ->success()
                        ->title(__('blogs::filament/admin/resources/post/pages/edit-post.header-actions.draft.notification.title'))
                        ->body(__('blogs::filament/admin/resources/post/pages/edit-post.header-actions.draft.notification.body'))
                        ->send();
                })
                ->visible(fn (Post $record) => $record->is_published),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('blogs::filament/admin/resources/post/pages/edit-post.header-actions.delete.notification.title'))
                        ->body(__('blogs::filament/admin/resources/post/pages/edit-post.header-actions.delete.notification.body')),
                ),
        ];
    }
}
