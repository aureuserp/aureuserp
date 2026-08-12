<?php

namespace Webkul\Website\Filament\Admin\Actions\Portal;

use Filament\Actions\ActionGroup;

class PortalAccessActionGroup
{
    public static function make(): ActionGroup
    {
        return ActionGroup::make([
            GrantPortalAccessAction::make(),
            ChangePortalPasswordAction::make(),
            SendPortalPasswordResetAction::make(),
            RevokePortalAccessAction::make(),
        ])
            ->label(__('website::filament/admin/portal-access.group.label'))
            ->icon('heroicon-o-globe-alt')
            ->tooltip(__('website::filament/admin/portal-access.group.label'));
    }
}
