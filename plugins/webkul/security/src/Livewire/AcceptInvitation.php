<?php

namespace Webkul\Security\Livewire;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\SimplePage;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Password;
use Webkul\Project\Filament\Pages\Dashboard;
use Webkul\Security\Models\Invitation;
use Webkul\Security\Models\User;
use Webkul\Security\Settings\UserSettings;

class AcceptInvitation extends SimplePage
{
    use InteractsWithFormActions;
    use InteractsWithForms;

    protected string $view = 'security::livewire.accept-invitation';

    public int $invitation;

    private Invitation $invitationModel;

    public ?array $data = [];

    public function mount(): void
    {
        $this->invitationModel = Invitation::findOrFail($this->invitation);

        $this->form->fill([
            'email' => $this->invitationModel->email,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('security::livewire/accept-invitation.form.name.label'))
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),
                TextInput::make('email')
                    ->label(__('security::livewire/accept-invitation.form.email.label'))
                    ->disabled(),
                TextInput::make('password')
                    ->label(__('security::livewire/accept-invitation.form.password.label'))
                    ->password()
                    ->required()
                    ->rule(Password::default())
                    ->same('passwordConfirmation')
                    ->validationAttribute(__('security::livewire/accept-invitation.form.password.validation_attribute')),
                TextInput::make('passwordConfirmation')
                    ->label(__('security::livewire/accept-invitation.form.password_confirmation.label'))
                    ->password()
                    ->required()
                    ->dehydrated(false),
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        $this->invitationModel = Invitation::find($this->invitation);

        $user = User::create([
            'name'               => $this->form->getState()['name'],
            'password'           => $this->form->getState()['password'],
            'email'              => $this->invitationModel->email,
            'default_company_id' => app(UserSettings::class)->default_company_id,
        ]);

        $user->assignRole(app(UserSettings::class)->default_role_id);

        $this->invitationModel->delete();

        $this->redirect(Dashboard::getUrl());
    }

    /**
     * @return array<Action | ActionGroup>
     */
    public function getFormActions(): array
    {
        return [
            $this->getRegisterFormAction(),
        ];
    }

    public function getRegisterFormAction(): Action
    {
        return Action::make('register')
            ->label(__('security::livewire/accept-invitation.form.actions.register.label'))
            ->submit('register');
    }

    public function getHeading(): string
    {
        return 'Accept Invitation';
    }

    public function hasLogo(): bool
    {
        return false;
    }

    public function getSubHeading(): string
    {
        return __('security::livewire/accept-invitation.header.sub-heading.accept-invitation');
    }
}
