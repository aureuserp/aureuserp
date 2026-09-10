<?php

namespace Webkul\Website\Support;

use Exception;
use Filament\Auth\Notifications\ResetPassword;
use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\PasswordBroker as PasswordBrokerContract;
use Illuminate\Support\Facades\Password;
use Webkul\Partner\Models\Partner;
use Webkul\PluginManager\Package;

class PortalAccess
{
    public static function isAvailable(): bool
    {
        return Package::isPluginInstalled('website') && static::panel() !== null;
    }

    public static function panel(): ?Panel
    {
        return Filament::getPanel('customer', isStrict: false);
    }

    public static function hasAccess(Partner $partner): bool
    {
        return filled($partner->password);
    }

    public static function isEmailTaken(Partner $partner): bool
    {
        return Partner::query()
            ->withoutGlobalScopes()
            ->whereKeyNot($partner->getKey())
            ->where('email', $partner->email)
            ->whereNotNull('password')
            ->exists();
    }

    public static function sendSetPasswordLink(Partner $partner): string
    {
        $panel = static::panel();

        return static::broker()->sendResetLink(
            ['email' => $partner->email],
            function (CanResetPassword $user, string $token) use ($panel): void {
                if (! method_exists($user, 'notify')) {
                    $userClass = $user::class;

                    throw new Exception("Model [{$userClass}] does not have a [notify()] method.");
                }

                $notification = app(ResetPassword::class, ['token' => $token]);
                $notification->url = $panel->getResetPasswordUrl($token, $user);

                $user->notify($notification);
            },
        );
    }

    public static function deleteResetTokens(Partner $partner): void
    {
        $broker = static::broker();

        if ($broker instanceof PasswordBroker) {
            $broker->deleteToken($partner);
        }
    }

    protected static function broker(): PasswordBrokerContract
    {
        return Password::broker(static::panel()?->getAuthPasswordBroker() ?? 'customers');
    }

    public static function revoke(Partner $partner): void
    {
        static::deleteResetTokens($partner);

        $partner->forceFill(['password' => null])->save();
    }
}
