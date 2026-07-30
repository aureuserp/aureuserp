<?php

namespace Webkul\Support\Filament\Concerns;

use Filament\Notifications\Notification;
use Webkul\Support\Exceptions\CrossCompanyException;

/**
 * Presents a CrossCompanyException thrown by the model layer as a notification.
 *
 * The check itself always lives on the model, so every write path (Filament, API,
 * console) is guarded by the same rule. This trait only decides how the failure
 * is shown inside a panel.
 */
trait HandlesCrossCompanyException
{
    public function create(bool $another = false): void
    {
        try {
            parent::create($another);
        } catch (CrossCompanyException $exception) {
            $this->notifyCrossCompany($exception);
        }
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        try {
            parent::save($shouldRedirect, $shouldSendSavedNotification);
        } catch (CrossCompanyException $exception) {
            $this->notifyCrossCompany($exception);
        }
    }

    protected function notifyCrossCompany(CrossCompanyException $exception): void
    {
        Notification::make()
            ->danger()
            ->title($exception->title())
            ->body($exception->getMessage())
            ->send();
    }
}
