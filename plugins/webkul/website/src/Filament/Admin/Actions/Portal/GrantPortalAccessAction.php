<?php

namespace Webkul\Website\Filament\Admin\Actions\Portal;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Webkul\Partner\Models\Partner;
use Webkul\Website\Support\PortalAccess;

class GrantPortalAccessAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'website.partners.grant-portal-access';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('website::filament/admin/portal-access.grant.label'))
            ->icon('heroicon-o-globe-alt')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading(__('website::filament/admin/portal-access.grant.modal.heading'))
            ->modalDescription(fn (Partner $record): string => __('website::filament/admin/portal-access.grant.modal.description', ['email' => $record->email]))
            ->visible(fn (Partner $record): bool => PortalAccess::isAvailable() && ! $record->trashed() && ! PortalAccess::hasAccess($record))
            ->action(function (Partner $record): void {
                if (blank($record->email)) {
                    Notification::make()
                        ->danger()
                        ->title(__('website::filament/admin/portal-access.grant.notification.email-missing.title'))
                        ->body(__('website::filament/admin/portal-access.grant.notification.email-missing.body'))
                        ->send();

                    return;
                }

                if (PortalAccess::isEmailTaken($record)) {
                    Notification::make()
                        ->danger()
                        ->title(__('website::filament/admin/portal-access.grant.notification.email-taken.title'))
                        ->body(__('website::filament/admin/portal-access.grant.notification.email-taken.body'))
                        ->send();

                    return;
                }

                $record->forceFill(['password' => Hash::make(Str::password(40))])->save();

                $status = PortalAccess::sendSetPasswordLink($record);

                if ($status === Password::RESET_LINK_SENT) {
                    Notification::make()
                        ->success()
                        ->title(__('website::filament/admin/portal-access.grant.notification.granted.title'))
                        ->body(__('website::filament/admin/portal-access.grant.notification.granted.body', ['email' => $record->email]))
                        ->send();

                    return;
                }

                Notification::make()
                    ->warning()
                    ->title(__('website::filament/admin/portal-access.grant.notification.email-failed.title'))
                    ->body(__($status))
                    ->send();
            });
    }
}
