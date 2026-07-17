<?php

namespace Webkul\Barcode\Http\Responses;

use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): Redirector|RedirectResponse
    {
        return redirect()->intended(route('barcode.dashboard'));
    }
}
