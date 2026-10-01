<?php

namespace Webkul\Barcode\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Webkul\Barcode\Filament\Pages\LaunchBarcode;

class Authenticate
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Filament::auth()->check()) {
            return redirect()->guest(route('barcode.login'));
        }

        $user = Filament::auth()->user();

        if (($user instanceof FilamentUser) && (! $user->canAccessPanel(Filament::getCurrentOrDefaultPanel()))) {
            Filament::auth()->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->guest(route('barcode.login'));
        }

        abort_unless(LaunchBarcode::canAccess(), 403, __('barcode::app.auth.forbidden'));

        return $next($request);
    }
}
