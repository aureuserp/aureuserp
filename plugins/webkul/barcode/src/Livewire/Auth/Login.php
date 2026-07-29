<?php

namespace Webkul\Barcode\Livewire\Auth;

use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Webkul\Barcode\Http\Responses\LoginResponse as BarcodeLoginResponse;

class Login extends BaseLogin
{
    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(route('barcode.dashboard'));
        }

        $this->form->fill();
    }

    public function authenticate(): ?LoginResponse
    {
        $response = parent::authenticate();

        if ($response === null) {
            return null;
        }

        return app(BarcodeLoginResponse::class);
    }

    public function getTitle(): string
    {
        return __('barcode::app.auth.login-title');
    }

    public function getHeading(): string
    {
        return __('barcode::app.auth.login-heading');
    }

    public function getSubheading(): ?string
    {
        return __('barcode::app.auth.login-subheading');
    }
}
