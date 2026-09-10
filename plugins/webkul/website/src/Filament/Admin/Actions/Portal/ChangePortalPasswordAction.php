<?php

namespace Webkul\Website\Filament\Admin\Actions\Portal;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Webkul\Partner\Models\Partner;
use Webkul\Website\Support\PortalAccess;

class ChangePortalPasswordAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'website.partners.change-portal-password';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('website::filament/admin/portal-access.change-password.label'))
            ->icon('heroicon-o-lock-closed')
            ->modalHeading(__('website::filament/admin/portal-access.change-password.modal.heading'))
            ->modalDescription(fn (Partner $record): string => __('website::filament/admin/portal-access.change-password.modal.description', ['email' => $record->email]))
            ->schema([
                TextInput::make('password')
                    ->label(__('website::filament/admin/portal-access.change-password.form.password.label'))
                    ->password()
                    ->revealable()
                    ->required()
                    ->rule(Password::default())
                    ->same('passwordConfirmation'),
                TextInput::make('passwordConfirmation')
                    ->label(__('website::filament/admin/portal-access.change-password.form.password-confirmation.label'))
                    ->password()
                    ->revealable()
                    ->required()
                    ->dehydrated(false),
            ])
            ->visible(fn (Partner $record): bool => PortalAccess::isAvailable() && PortalAccess::hasAccess($record))
            ->action(function (array $data, Partner $record): void {
                PortalAccess::deleteResetTokens($record);

                $record->forceFill(['password' => Hash::make($data['password'])])->save();

                Notification::make()
                    ->success()
                    ->title(__('website::filament/admin/portal-access.change-password.notification.changed.title'))
                    ->body(__('website::filament/admin/portal-access.change-password.notification.changed.body'))
                    ->send();
            });
    }
}
