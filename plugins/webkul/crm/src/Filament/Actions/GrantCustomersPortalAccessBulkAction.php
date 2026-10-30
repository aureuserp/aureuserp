<?php

namespace Webkul\Crm\Filament\Actions;

use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource;
use Webkul\Partner\Models\Partner;
use Webkul\Website\Support\PortalAccess;

class GrantCustomersPortalAccessBulkAction extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'grantCustomersPortalAccess';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/clusters/sales/resources/customer.actions.portal.label'))
            ->icon('heroicon-o-globe-alt')
            ->requiresConfirmation()
            ->modalDescription(__('crm::filament/clusters/sales/resources/customer.actions.portal.description'))
            ->visible(fn (): bool => class_exists(PortalAccess::class) && PortalAccess::isAvailable())
            ->deselectRecordsAfterCompletion()
            ->action(function (Collection $records): void {
                $granted = $records
                    ->filter(fn (Partner $contact): bool => static::canGrant($contact))
                    ->filter(function (Partner $contact): bool {
                        $contact->forceFill(['password' => Hash::make(Str::password(40))])->save();

                        return PortalAccess::sendSetPasswordLink($contact) === Password::RESET_LINK_SENT;
                    })
                    ->count();

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/clusters/sales/resources/customer.actions.portal.notification', ['granted' => $granted, 'skipped' => $records->count() - $granted]))
                    ->send();
            });
    }

    public static function canGrant(Partner $contact): bool
    {
        return ! $contact->trashed()
            && filled($contact->email)
            && CustomerResource::canEdit($contact)
            && ! PortalAccess::hasAccess($contact)
            && ! PortalAccess::isEmailTaken($contact);
    }
}
