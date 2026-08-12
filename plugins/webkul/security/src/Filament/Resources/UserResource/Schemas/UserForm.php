<?php

namespace Webkul\Security\Filament\Resources\UserResource\Schemas;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Filament\Resources\CompanyResource;
use Webkul\Security\Filament\Resources\TeamResource;
use Webkul\Security\Filament\Resources\UserResource;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Scopes\AllowedCompanyScope;

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
                                            ->revealable(filament()->arePasswordsRevealable())
                                            ->hiddenOn('edit')
                                            ->maxLength(255)
                                            ->rule('min:8'),
                                        TextInput::make('password_confirmation')
                                            ->label(__('security::filament/resources/user.form.sections.general-information.fields.password-confirmation'))
                                            ->password()
                                            ->revealable(filament()->arePasswordsRevealable())
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
                                            ->required()
                                            ->preload()
                                            ->rule(function (?User $record) {
                                                return function (string $attribute, $value, Closure $fail) use ($record): void {
                                                    try {
                                                        UserResource::ensureAdminRoleConstraints($record, (array) $value);
                                                    } catch (ValidationException $exception) {
                                                        $messages = $exception->errors()['roles'] ?? [];

                                                        foreach ($messages as $message) {
                                                            $fail($message);
                                                        }
                                                    }
                                                };
                                            })
                                            ->searchable(),
                                        Select::make('resource_permission')
                                            ->label(__('security::filament/resources/user.form.sections.permissions.fields.resource-permission'))
                                            ->live()
                                            ->options(PermissionType::class)
                                            ->required()
                                            ->disabled(fn (?User $record): bool => filled($record) && Auth::id() === $record->id)
                                            ->dehydrated(fn (?User $record): bool => ! (filled($record) && Auth::id() === $record->id))
                                            ->helperText(fn (?User $record): ?string => filled($record) && Auth::id() === $record->id
                                                ? __('security::filament/resources/user.form.sections.permissions.fields.resource-permission-self-change-disabled')
                                                : null)
                                            ->preload()
                                            ->default(PermissionType::GLOBAL->value)
                                            ->afterStateUpdated(function (Get $get, Set $set) {
                                                if ($get('resource_permission') != PermissionType::GROUP) {
                                                    $set('teams', []);
                                                }
                                            })
                                            ->searchable(),
                                        Select::make('teams')
                                            ->label(__('security::filament/resources/user.form.sections.permissions.fields.teams'))
                                            ->relationship(
                                                name: 'teams',
                                                titleAttribute: 'name'
                                            )
                                            ->live()
                                            ->multiple()
                                            ->preload()
                                            ->searchable()
                                            ->required(fn (Get $get) => $get('resource_permission') == PermissionType::GROUP)
                                            ->createOptionForm(fn (Schema $schema) => TeamResource::form($schema)),
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
                                            ->automaticallyResizeImagesMode('cover')
                                            ->image()
                                            ->imageEditor()
                                            ->directory('users/avatars')
                                            ->visibility('public'),
                                    ])
                                    ->columns(1),
                                Section::make(__('security::filament/resources/user.form.sections.lang-and-status.title'))
                                    ->schema([
                                        Select::make('language')
                                            ->label(__('security::filament/resources/user.form.sections.lang-and-status.fields.language'))
                                            ->options(
                                                collect(config('app.supported_locales', []))
                                                    ->mapWithKeys(fn ($meta, $code) => [
                                                        $code => ($meta['native'] ?? $code).' ('.($meta['label'] ?? $code).')',
                                                    ])
                                                    ->all()
                                            )
                                            ->default(config('app.locale'))
                                            ->native(false)
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
                                            ->relationship('allowedCompanies', 'name', fn (Builder $query) => $query->withoutGlobalScope(AllowedCompanyScope::class))
                                            ->multiple()
                                            ->preload()
                                            ->searchable(),
                                        Select::make('default_company_id')
                                            ->label(__('security::filament/resources/user.form.sections.multi-company.default-company'))
                                            ->relationship(
                                                'defaultCompany',
                                                'name',
                                                modifyQueryUsing: fn (Builder $query) => $query->withTrashed()->withoutGlobalScope(AllowedCompanyScope::class),
                                            )
                                            ->getOptionLabelFromRecordUsing(function ($record): string {
                                                return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                            })
                                            ->disableOptionWhen(fn ($label) => str_contains($label, ' (Deleted)'))
                                            ->required()
                                            ->rule(fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                                $allowed = $get('allowed_companies') ?? [];

                                                if (! empty($value) && ! in_array((string) $value, array_map('strval', (array) $allowed), true)) {
                                                    $fail(__('security::filament/resources/user.form.sections.multi-company.default-company-not-allowed'));
                                                }
                                            })
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
                                            ->afterStateHydrated(function (Select $component, $state) {
                                                if (empty($state)) {
                                                    $component->state(null);

                                                    return;
                                                }

                                                $company = Company::find($state);

                                                if (! $company) {
                                                    $component->state(null);
                                                }
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
