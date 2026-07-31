<?php

namespace Webkul\Support\Filament\Concerns;

use Filament\Notifications\Notification;
use Webkul\Support\Exceptions\CrossCompanyException;

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
