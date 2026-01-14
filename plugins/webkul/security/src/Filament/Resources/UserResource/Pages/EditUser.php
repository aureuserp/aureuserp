<?php

namespace Webkul\Security\Filament\Resources\UserResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Webkul\Security\Filament\Resources\UserResource;
use Webkul\Security\Models\User;
use Webkul\Security\Settings\UserSettings;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('security::filament/resources/user/pages/edit-user.notification.title'))
            ->body(__('security::filament/resources/user/pages/edit-user.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('changePassword')
                ->label(__('security::filament/resources/user/pages/edit-user.header-actions.change-password.label'))
                ->visible(fn (UserSettings $userSettings) => $userSettings->enable_reset_password)
                ->action(function (User $record, array $data): void {
                    $record->update([
                        'password' => Hash::make($data['new_password']),
                    ]);

                    Notification::make()
                        ->title(__('security::filament/resources/user/pages/edit-user.header-actions.change-password.notification.title'))
                        ->body(__('security::filament/resources/user/pages/edit-user.header-actions.change-password.notification.body'))
                        ->success()
                        ->send();
                })
                ->schema([
                    TextInput::make('new_password')
                        ->password()
                        ->label(__('security::filament/resources/user/pages/edit-user.header-actions.change-password.form.new-password'))
                        ->required()
                        ->rule(Password::default()),
                    TextInput::make('new_password_confirmation')
                        ->password()
                        ->label(__('security::filament/resources/user/pages/edit-user.header-actions.change-password.form.confirm-new-password'))
                        ->rule('required', fn ($get) => (bool) $get('new_password'))
                        ->same('new_password'),
                ])
                ->icon('heroicon-o-key'),
            ViewAction::make(),
            DeleteAction::make()
                ->visible(fn (User $record) => self::getResource()::canDeleteUser($record))
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('security::filament/resources/user/pages/edit-user.header-actions.delete.notification.title'))
                        ->body(__('security::filament/resources/user/pages/edit-user.header-actions.delete.notification.body'))
                ),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $partner = $this->record->partner;

        return [
            ...$data,
            ...$partner ? $partner->toArray() : [],
        ];
    }
}
