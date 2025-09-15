<?php

namespace Webkul\Security\Filament\Resources\UserResource\Pages;

use Exception;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Support\Facades\Mail;
use Webkul\Security\Filament\Resources\UserResource;
use Webkul\Security\Mail\UserInvitationMail;
use Webkul\Security\Models\Invitation;
use Webkul\Security\Models\User;
use Webkul\Security\Settings\UserSettings;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('security::filament/resources/user/pages/list-user.tabs.all'))
                ->badge(User::count()),
            'archived' => Tab::make(__('security::filament/resources/user/pages/list-user.tabs.archived'))
                ->badge(User::onlyTrashed()->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->onlyTrashed();
                }),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-user-plus')
                ->label(__('security::filament/resources/user/pages/list-user.header-actions.create.label')),
            Action::make('inviteUser')
                ->label(__('security::filament/resources/user/pages/list-user.header-actions.invite.title'))
                ->icon('heroicon-o-envelope')
                ->modalIcon('heroicon-o-envelope')
                ->modalSubmitActionLabel(__('security::filament/resources/user/pages/list-user.header-actions.invite.modal.submit-action-label'))
                ->visible(fn (UserSettings $userSettings) => $userSettings->enable_user_invitation)
                ->schema([
                    TextInput::make('email')
                        ->email()
                        ->label(__('security::filament/resources/user/pages/list-user.header-actions.invite.form.email'))
                        ->required(),
                ])
                ->action(function ($data) {
                    if (! isset(app(UserSettings::class)->default_company_id)) {
                        Notification::make('invitedFailed')
                            ->title(__('security::filament/resources/user/pages/list-user.header-actions.invite.notification.default-company-error.title'))
                            ->body(__('security::filament/resources/user/pages/list-user.header-actions.invite.notification.default-company-error.body'))
                            ->danger()
                            ->send();

                        return;
                    }

                    $invitation = Invitation::create(['email' => $data['email']]);

                    try {
                        Mail::to($invitation->email)->send(new UserInvitationMail($invitation));

                        Notification::make('invitedSuccess')
                            ->title(__('security::filament/resources/user/pages/list-user.header-actions.invite.notification.success.title'))
                            ->body(__('security::filament/resources/user/pages/list-user.header-actions.invite.notification.success.body'))
                            ->success()
                            ->send();
                    } catch (Exception $e) {
                        report($e);

                        Notification::make('invitedFailed')
                            ->title(__('security::filament/resources/user/pages/list-user.header-actions.invite.notification.error.title'))
                            ->body(__('security::filament/resources/user/pages/list-user.header-actions.invite.notification.error.body'))
                            ->success()
                            ->send();
                    }
                }),
        ];
    }
}
