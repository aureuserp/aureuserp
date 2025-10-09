<?php

namespace Webkul\Support\Filament\Pages;

use Exception;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class Profile extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'support::pages.profile';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = null;

    public ?array $profileData = [];

    public ?array $passwordData = [];

    public function mount(): void
    {
        $this->fillForms();
    }

    protected function getForms(): array
    {
        return [
            'editProfileForm',
            'editPasswordForm',
        ];
    }

    public function editProfileForm(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('support::filament/pages/profile.information_section'))
                    ->description(__('support::filament/pages/profile.information_description'))
                    ->icon('heroicon-o-user')
                    ->schema([
                        FileUpload::make('avatar')
                            ->label(__('support::filament/pages/profile.fields.avatar'))
                            ->avatar()
                            ->directory('users/avatars')
                            ->visibility('public')
                            ->disk('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
                            ->maxSize(2048)
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '1:1',
                            ])
                            ->columnSpanFull()
                            ->helperText(__('support::filament/pages/profile.fields.avatar').': '.__('support::filament/pages/profile.information_description'))
                            ->deletable(true)
                            ->downloadable(false),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('support::filament/pages/profile.fields.name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->autocomplete('name')
                                    ->validationAttribute(__('support::filament/pages/profile.fields.name'))
                                    ->rules(['required', 'string', 'max:255'])
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        $set('name', trim($state));
                                    }),

                                TextInput::make('email')
                                    ->label(__('support::filament/pages/profile.fields.email'))
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(table: 'users', column: 'email', ignoreRecord: true)
                                    ->autocomplete('email')
                                    ->validationAttribute(__('support::filament/pages/profile.fields.email'))
                                    ->rules(['required', 'email', 'max:255'])
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        $set('email', strtolower(trim($state)));
                                    }),
                            ]),
                    ]),
            ])
            ->model($this->getUser())
            ->statePath('profileData')
            ->operation('edit');
    }

    public function editPasswordForm(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('support::filament/pages/profile.password.section'))
                    ->description(__('support::filament/pages/profile.password.description'))
                    ->icon('heroicon-o-lock-closed')
                    ->schema([
                        TextInput::make('current_password')
                            ->label(__('support::filament/pages/profile.password.current'))
                            ->password()
                            ->revealable()
                            ->required()
                            ->autocomplete('current-password')
                            ->validationAttribute(__('support::filament/pages/profile.password.current'))
                            ->currentPassword()
                            ->helperText(__('support::filament/pages/profile.password.current-helper')),

                        TextInput::make('password')
                            ->label(__('support::filament/pages/profile.password.new'))
                            ->password()
                            ->revealable()
                            ->required()
                            ->rule(Password::default()->min(6))
                            ->autocomplete('new-password')
                            ->validationAttribute(__('support::filament/pages/profile.password.new'))
                            ->live(debounce: 500)
                            ->confirmed()
                            ->helperText(__('support::filament/pages/profile.password.helper'))
                            ->different('current_password')
                            ->dehydrateStateUsing(fn ($state): ?string => $state ? Hash::make($state) : null),

                        TextInput::make('password_confirmation')
                            ->label(__('support::filament/pages/profile.password.confirm'))
                            ->password()
                            ->revealable()
                            ->required()
                            ->dehydrated(false)
                            ->autocomplete('new-password')
                            ->validationAttribute(__('support::filament/pages/profile.password.confirm'))
                            ->same('password'),
                    ]),
            ])
            ->model($this->getUser())
            ->statePath('passwordData')
            ->operation('edit');
    }

    public function updateProfile(): void
    {
        try {
            $this->editProfileForm->validate();

            $data = $this->editProfileForm->getState();
            $user = $this->getUser();

            if (array_key_exists('avatar', $data)) {
                if (
                    $user->avatar
                    && $data['avatar'] !== $user->avatar
                ) {
                    Storage::disk('public')->delete($user->avatar);
                }

                $user->partner->avatar = $data['avatar'];
                $user->partner->save();
            }

            $user->fill([
                'name'  => trim($data['name']),
                'email' => strtolower(trim($data['email'])),
            ]);

            $user->save();

            $this->fillProfileForm();

            $this->dispatch('profile-updated');

            Notification::make()
                ->title(__('support::filament/pages/profile.notification.success.title'))
                ->body(__('support::filament/pages/profile.notification.success.body'))
                ->success()
                ->duration(3000)
                ->send();
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            Notification::make()
                ->title(__('support::filament/pages/profile.notification.error.title'))
                ->body(__('support::filament/pages/profile.notification.error.body'))
                ->danger()
                ->duration(5000)
                ->send();
        }
    }

    public function updatePassword(): mixed
    {
        try {
            $this->editPasswordForm->validate();

            $data = $this->editPasswordForm->getState();
            $user = $this->getUser();

            if (Hash::check($this->passwordData['password'], $user->password)) {
                throw ValidationException::withMessages([
                    'passwordData.password' => [__('support::filament/pages/profile.password.errors.same-as-current')],
                ]);
            }

            $user->password = $data['password'];
            $user->save();

            $this->editPasswordForm->fill([
                'current_password'      => '',
                'password'              => '',
                'password_confirmation' => '',
            ]);

            $this->dispatch('password-updated');

            Notification::make()
                ->title(__('support::filament/pages/profile.password.notification.success.title'))
                ->body(__('support::filament/pages/profile.password.notification.success.body'))
                ->success()
                ->duration(3000)
                ->send();
                
            if (request()->hasSession()) {
                request()->session()->invalidate();
                request()->session()->regenerateToken();
            }

            return redirect()->to(filament()->getCurrentPanel()->getLoginUrl());
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            Notification::make()
                ->title(__('support::filament/pages/profile.password.notification.error.title'))
                ->body(__('support::filament/pages/profile.password.notification.error.body'))
                ->danger()
                ->duration(5000)
                ->send();
        }

        return null;
    }

    protected function getUser(): Authenticatable&Model
    {
        $user = Filament::auth()->user();

        if (! $user instanceof Model) {
            throw new Exception('The authenticated user object must be an Eloquent model to allow the profile page to update it.');
        }

        return $user;
    }

    protected function fillForms(): void
    {
        $this->fillProfileForm();
        $this->fillPasswordForm();
    }

    protected function fillProfileForm(): void
    {
        $user = $this->getUser();

        $userData = $user->only(['name', 'email', 'avatar']);

        $userData['avatar'] = $user->partner->avatar;

        $this->editProfileForm->fill($userData);
    }

    protected function fillPasswordForm(): void
    {
        $this->editPasswordForm->fill([
            'current_password'      => '',
            'password'              => '',
            'password_confirmation' => '',
        ]);
    }

    public function getTitle(): string
    {
        return __('support::filament/pages/profile.title');
    }

    public function getHeading(): string
    {
        return __('support::filament/pages/profile.heading');
    }

    public function getSubheading(): ?string
    {
        return __('support::filament/pages/profile.subheading');
    }

    protected function getUpdateProfileFormActions(): array
    {
        return [
            Action::make('updateProfile')
                ->label(__('support::filament/pages/profile.actions.save'))
                ->color('primary')
                ->action('updateProfile'),
        ];
    }

    protected function getUpdatePasswordFormActions(): array
    {
        return [
            Action::make('updatePassword')
                ->label(__('support::filament/pages/profile.password.section'))
                ->color('warning')
                ->action('updatePassword'),
        ];
    }

    protected function getViewData(): array
    {
        return [
            'user' => $this->getUser(),
        ];
    }
}
