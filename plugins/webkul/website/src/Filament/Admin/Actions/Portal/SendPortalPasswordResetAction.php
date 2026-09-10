<?php

namespace Webkul\Website\Filament\Admin\Actions\Portal;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Password;
use Webkul\Partner\Models\Partner;
use Webkul\Website\Support\PortalAccess;

class SendPortalPasswordResetAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'website.partners.send-portal-password-reset';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('website::filament/admin/portal-access.password-reset.label'))
            ->icon('heroicon-o-key')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading(__('website::filament/admin/portal-access.password-reset.modal.heading'))
            ->modalDescription(fn (Partner $record): string => __('website::filament/admin/portal-access.password-reset.modal.description', ['email' => $record->email]))
            ->visible(fn (Partner $record): bool => PortalAccess::isAvailable() && PortalAccess::hasAccess($record) && filled($record->email))
            ->action(function (Partner $record): void {
                $status = PortalAccess::sendSetPasswordLink($record);

                if ($status === Password::RESET_LINK_SENT) {
                    Notification::make()
                        ->success()
                        ->title(__('website::filament/admin/portal-access.password-reset.notification.sent.title'))
                        ->body(__('website::filament/admin/portal-access.password-reset.notification.sent.body', ['email' => $record->email]))
                        ->send();

                    return;
                }

                Notification::make()
                    ->danger()
                    ->title(__('website::filament/admin/portal-access.password-reset.notification.failed.title'))
                    ->body(__($status))
                    ->send();
            });
    }
}
