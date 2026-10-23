<?php

namespace Webkul\Crm\Filament\Concerns;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

trait UsesAbsoluteDownloadUrls
{
    public static function modifyCompletedNotification(Notification $notification, Model $transfer): Notification
    {
        foreach ($notification->getActions() as $action) {
            if (! $action instanceof Action) {
                continue;
            }

            $url = $action->getUrl();

            if (is_string($url) && str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
                $action->url(url($url), $action->shouldOpenUrlInNewTab());
            }
        }

        return $notification;
    }
}
