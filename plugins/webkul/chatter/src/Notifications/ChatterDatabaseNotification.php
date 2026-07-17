<?php

namespace Webkul\Chatter\Notifications;

use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ChatterDatabaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $title,
        public ?string $body = null,
        public string $icon = 'heroicon-o-bell',
        public string $color = 'primary',
        public ?string $url = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $notification = FilamentNotification::make()
            ->title($this->title)
            ->icon($this->icon)
            ->iconColor($this->color);

        if (filled($this->body)) {
            $notification->body($this->body);
        }

        if (filled($this->url)) {
            $notification->actions([
                Action::make('view')
                    ->label(__('chatter::notifications.database.actions.view'))
                    ->url($this->url)
                    ->markAsRead(),
            ]);
        }

        return $notification->getDatabaseMessage();
    }
}
