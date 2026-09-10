<?php

namespace Webkul\Website\Filament\Admin\Actions\Portal;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Webkul\Partner\Models\Partner;
use Webkul\Website\Support\PortalAccess;

class RevokePortalAccessAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'website.partners.revoke-portal-access';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('website::filament/admin/portal-access.revoke.label'))
            ->icon('heroicon-o-no-symbol')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading(__('website::filament/admin/portal-access.revoke.modal.heading'))
            ->modalDescription(__('website::filament/admin/portal-access.revoke.modal.description'))
            ->visible(fn (Partner $record): bool => PortalAccess::isAvailable() && PortalAccess::hasAccess($record))
            ->action(function (Partner $record): void {
                PortalAccess::revoke($record);

                Notification::make()
                    ->success()
                    ->title(__('website::filament/admin/portal-access.revoke.notification.revoked.title'))
                    ->body(__('website::filament/admin/portal-access.revoke.notification.revoked.body'))
                    ->send();
            });
    }
}
