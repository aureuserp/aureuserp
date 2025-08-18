<?php

namespace Webkul\Website\Http\Responses;

use Filament\Auth\Http\Responses\Contracts\LogoutResponse as BaseLogoutResponse;
use Illuminate\Http\RedirectResponse;
use Webkul\Website\Filament\Customer\Pages\Homepage;

class LogoutResponse implements BaseLogoutResponse
{
    public function toResponse($request): RedirectResponse
    {
        if ($request->route()->getName() == 'filament.customer.auth.logout') {
            return redirect()->route(Homepage::getRouteName());
        } else {
            return redirect()->route('filament.admin..');
        }
    }
}
