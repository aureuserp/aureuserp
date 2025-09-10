<?php

namespace Webkul\Security\Filament\Resources\Users\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Filament\Resources\Companies\CompanyResource;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('security::filament/resources/user.form.sections.general-information.title'))
                                    ->schema([
                                        TextInput::make('name')
                                            ->label(__('security::filament/resources/user.form.sections.general-information.fields.name'))
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true),
                                        TextInput::make('email')
                                            ->label(__('security::filament/resources/user.form.sections.general-information.fields.email'))
                                            ->email()
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255),
                                        TextInput::make('password')
                                            ->label(__('security::filament/resources/user.form.sections.general-information.fields.password'))
                                            ->password()
                                            ->required()
                                            ->hiddenOn('edit')
                                            ->maxLength(255)
                                            ->rule('min:8'),
                                        TextInput::make('password_confirmation')
                                            ->label(__('security::filament/resources/user.form.sections.general-information.fields.password-confirmation'))
                                            ->password()
                                            ->hiddenOn('edit')
                                            ->rule('required', fn ($get) => (bool) $get('password'))
                                            ->same('password'),
                                    ])
                                    ->columns(2),

                                Section::make(__('security::filament/resources/user.form.sections.permissions.title'))
                                    ->schema([
                                        Select::make('roles')
                                            ->label(__('security::filament/resources/user.form.sections.permissions.fields.roles'))
                                            ->relationship('roles', 'name')
                                            ->multiple()
                                            ->preload()
                                            ->searchable(),
                                        Select::make('resource_permission')
                                            ->label(__('security::filament/resources/user.form.sections.permissions.fields.resource-permission'))
                                            ->options(PermissionType::options())
                                            ->required()
                                            ->preload()
                                            ->searchable(),
                                        Select::make('teams')
                                            ->label(__('security::filament/resources/user.form.sections.permissions.fields.teams'))
                                            ->relationship('teams', 'name')
                                            ->multiple()
                                            ->preload()
                                            ->searchable(),
                                    ])
                                    ->columns(2),
                            ])
                            ->columnSpan(['lg' => 2]),
                        Group::make()
                            ->schema([
                                Section::make(__('security::filament/resources/user.form.sections.avatar.title'))
                                    ->relationship('partner', 'avatar')
                                    ->schema([
                                        FileUpload::make('avatar')
                                            ->hiddenLabel()
                                            ->imageResizeMode('cover')
                                            ->image()
                                            ->imageEditor()
                                            ->directory('users/avatars')
                                            ->visibility('private'),
                                    ])
                                    ->columns(1),
                                Section::make(__('security::filament/resources/user.form.sections.lang-and-status.title'))
                                    ->schema([
                                        Select::make('language')
                                            ->label(__('security::filament/resources/user.form.sections.lang-and-status.fields.language'))
                                            ->options([
                                                'en' => __('English'),
                                            ])
                                            ->searchable(),
                                        Toggle::make('is_active')
                                            ->label(__('security::filament/resources/user.form.sections.lang-and-status.fields.status'))
                                            ->default(true),
                                    ])
                                    ->columns(1),
                                Section::make(__('security::filament/resources/user.form.sections.multi-company.title'))
                                    ->schema([
                                        Select::make('allowed_companies')
                                            ->label(__('security::filament/resources/user.form.sections.multi-company.allowed-companies'))
                                            ->relationship('allowedCompanies', 'name')
                                            ->multiple()
                                            ->preload()
                                            ->searchable(),
                                        Select::make('default_company_id')
                                            ->label(__('security::filament/resources/user.form.sections.multi-company.default-company'))
                                            ->relationship('defaultCompany', 'name')
                                            ->required()
                                            ->searchable()
                                            ->createOptionForm(fn (Schema $schema) => CompanyResource::form($schema))
                                            ->createOptionAction(function (Action $action) {
                                                $action
                                                    ->fillForm(function (array $arguments): array {
                                                        return [
                                                            'user_id' => Auth::id(),
                                                        ];
                                                    })
                                                    ->mutateDataUsing(function (array $data) {
                                                        $data['user_id'] = Auth::id();

                                                        return $data;
                                                    });
                                            })
                                            ->preload(),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ])
                    ->columns(3),
            ])
            ->columns(1);
    }
}
