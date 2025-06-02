<?php

namespace Webkul\Security\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Filament\Resources\UserResource\Pages;
use Webkul\Security\Models\User;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('security::filament/resources/user.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('security::filament/resources/user.navigation.group');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('security::filament/resources/user.global-search.name')  => $record->name ?? '—',
            __('security::filament/resources/user.global-search.email') => $record->email ?? '—',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make(__('security::filament/resources/user.form.sections.general-information.title'))
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label(__('security::filament/resources/user.form.sections.general-information.fields.name'))
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true),
                                        Forms\Components\TextInput::make('email')
                                            ->label(__('security::filament/resources/user.form.sections.general-information.fields.email'))
                                            ->email()
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('password')
                                            ->label(__('security::filament/resources/user.form.sections.general-information.fields.password'))
                                            ->password()
                                            ->required()
                                            ->hiddenOn('edit')
                                            ->maxLength(255)
                                            ->rule('min:8'),
                                        Forms\Components\TextInput::make('password_confirmation')
                                            ->label(__('security::filament/resources/user.form.sections.general-information.fields.password-confirmation'))
                                            ->password()
                                            ->hiddenOn('edit')
                                            ->rule('required', fn ($get) => (bool) $get('password'))
                                            ->same('password'),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make(__('security::filament/resources/user.form.sections.permissions.title'))
                                    ->schema([
                                        Forms\Components\Select::make('roles')
                                            ->label(__('security::filament/resources/user.form.sections.permissions.fields.roles'))
                                            ->relationship('roles', 'name')
                                            ->multiple()
                                            ->preload()
                                            ->searchable(),
                                        Forms\Components\Select::make('resource_permission')
                                            ->label(__('security::filament/resources/user.form.sections.permissions.fields.resource-permission'))
                                            ->options(PermissionType::options())
                                            ->required()
                                            ->preload()
                                            ->searchable(),
                                        Forms\Components\Select::make('teams')
                                            ->label(__('security::filament/resources/user.form.sections.permissions.fields.teams'))
                                            ->relationship('teams', 'name')
                                            ->multiple()
                                            ->preload()
                                            ->searchable(),
                                    ])
                                    ->columns(2),
                            ])
                            ->columnSpan(['lg' => 2]),
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make(__('security::filament/resources/user.form.sections.avatar.title'))
                                    ->relationship('partner', 'avatar')
                                    ->schema([
                                        Forms\Components\FileUpload::make('avatar')
                                            ->hiddenLabel()
                                            ->imageResizeMode('cover')
                                            ->imageEditor()
                                            ->directory('users/avatars')
                                            ->visibility('private'),
                                    ])
                                    ->columns(1),
                                Forms\Components\Section::make(__('security::filament/resources/user.form.sections.lang-and-status.title'))
                                    ->schema([
                                        Forms\Components\Select::make('language')
                                            ->label(__('security::filament/resources/user.form.sections.lang-and-status.fields.language'))
                                            ->options([
                                                'en' => __('English'),
                                            ])
                                            ->searchable(),
                                        Forms\Components\Toggle::make('is_active')
                                            ->label(__('security::filament/resources/user.form.sections.lang-and-status.fields.status'))
                                            ->default(true),
                                    ])
                                    ->columns(1),
                                Forms\Components\Section::make(__('security::filament/resources/user.form.sections.multi-company.title'))
                                    ->schema([
                                        Forms\Components\Select::make('allowed_companies')
                                            ->label(__('security::filament/resources/user.form.sections.multi-company.allowed-companies'))
                                            ->relationship('allowedCompanies', 'name')
                                            ->multiple()
                                            ->preload()
                                            ->searchable(),
                                        Forms\Components\Select::make('default_company_id')
                                            ->label(__('security::filament/resources/user.form.sections.multi-company.default-company'))
                                            ->relationship('defaultCompany', 'name')
                                            ->required()
                                            ->searchable()
                                            ->createOptionForm(fn (Form $form) => CompanyResource::form($form))
                                            ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                                                $action
                                                    ->fillForm(function (array $arguments): array {
                                                        return [
                                                            'user_id' => Auth::id(),
                                                        ];
                                                    })
                                                    ->mutateFormDataUsing(function (array $data) {
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
            ->columns('full');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('partner.avatar')
                    ->size(50)
                    ->label(__('security::filament/resources/user.table.columns.avatar')),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('security::filament/resources/user.table.columns.name'))
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('security::filament/resources/user.table.columns.email'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('teams.name')
                    ->label(__('security::filament/resources/user.table.columns.teams'))
                    ->badge(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->sortable()
                    ->label(__('security::filament/resources/user.table.columns.role')),
                Tables\Columns\TextColumn::make('resource_permission')
                    ->label(__('security::filament/resources/user.table.columns.resource-permission'))
                    ->formatStateUsing(fn ($state) => PermissionType::options()[$state] ?? $state)
                    ->sortable(),
                Tables\Columns\TextColumn::make('defaultCompany.name')
                    ->label(__('security::filament/resources/user.table.columns.default-company'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('allowedCompanies.name')
                    ->label(__('security::filament/resources/user.table.columns.allowed-company'))
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('security::filament/resources/user.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('security::filament/resources/user.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('resource_permission')
                    ->label(__('security::filament/resources/user.table.filters.resource-permission'))
                    ->searchable()
                    ->options(PermissionType::options())
                    ->preload(),
                Tables\Filters\SelectFilter::make('default_company')
                    ->relationship('defaultCompany', 'name')
                    ->label(__('security::filament/resources/user.table.filters.default-company'))
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('allowed_companies')
                    ->relationship('allowedCompanies', 'name')
                    ->label(__('security::filament/resources/user.table.filters.allowed-companies'))
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('teams')
                    ->relationship('teams', 'name')
                    ->label(__('security::filament/resources/user.table.filters.teams'))
                    ->options(fn (): array => Role::query()->pluck('name', 'id')->all())
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('roles')
                    ->label(__('security::filament/resources/user.table.filters.roles'))
                    ->relationship('roles', 'name')
                    ->options(fn (): array => Role::query()->pluck('name', 'id')->all())
                    ->multiple()
                    ->searchable()
                    ->preload(),
            ])
            ->filtersFormColumns(2)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    Tables\Actions\EditAction::make()
                        ->hidden(fn ($record) => $record->trashed())
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('security::filament/resources/user.table.actions.edit.notification.title'))
                                ->body(__('security::filament/resources/user.table.actions.edit.notification.body')),
                        ),
                    Tables\Actions\DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('security::filament/resources/user.table.actions.delete.notification.title'))
                                ->body(__('security::filament/resources/user.table.actions.delete.notification.body')),
                        ),
                    Tables\Actions\RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('security::filament/resources/user.table.actions.restore.notification.title'))
                                ->body(__('security::filament/resources/user.table.actions.restore.notification.body')),
                        ),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('security::filament/resources/user.table.bulk-actions.delete.notification.title'))
                                ->body(__('security::filament/resources/user.table.bulk-actions.delete.notification.body')),
                        ),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('security::filament/resources/user.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('security::filament/resources/user.table.bulk-actions.force-delete.notification.body')),
                        ),
                    Tables\Actions\RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('security::filament/resources/user.table.bulk-actions.restore.notification.title'))
                                ->body(__('security::filament/resources/user.table.bulk-actions.restore.notification.body')),
                        ),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function ($query) {
                $query->with('roles', 'teams', 'defaultCompany', 'allowedCompanies');
            })
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('security::filament/resources/user.table.empty-state-actions.create.notification.title'))
                            ->body(__('security::filament/resources/user.table.empty-state-actions.create.notification.body')),
                    ),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Grid::make(['default' => 3])
                    ->schema([
                        Infolists\Components\Group::make()
                            ->schema([
                                Infolists\Components\Section::make(__('security::filament/resources/user.infolist.sections.general-information.title'))
                                    ->schema([
                                        Infolists\Components\TextEntry::make('name')
                                            ->icon('heroicon-o-user')
                                            ->placeholder('—')
                                            ->extraAttributes([
                                                'style' => 'word-break: break-all;',
                                            ])
                                            ->label(__('security::filament/resources/user.infolist.sections.general-information.entries.name')),
                                        Infolists\Components\TextEntry::make('email')
                                            ->icon('heroicon-o-envelope')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/user.infolist.sections.general-information.entries.email')),
                                        Infolists\Components\TextEntry::make('language')
                                            ->icon('heroicon-o-language')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/user.infolist.sections.lang-and-status.entries.language')),
                                    ])
                                    ->columns(2),

                                Infolists\Components\Section::make(__('security::filament/resources/user.infolist.sections.permissions.title'))
                                    ->schema([
                                        Infolists\Components\TextEntry::make('roles.name')
                                            ->icon('heroicon-o-key')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/user.infolist.sections.permissions.entries.roles'))
                                            ->listWithLineBreaks()
                                            ->formatStateUsing(fn ($state) => ucfirst($state))
                                            ->bulleted(),
                                        Infolists\Components\TextEntry::make('teams.name')
                                            ->icon('heroicon-o-user-group')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/user.infolist.sections.permissions.entries.teams'))
                                            ->listWithLineBreaks()
                                            ->bulleted(),
                                        Infolists\Components\TextEntry::make('resource_permission')
                                            ->icon(function ($record) {
                                                return [
                                                    PermissionType::GLOBAL->value     => 'heroicon-o-globe-alt',
                                                    PermissionType::INDIVIDUAL->value => 'heroicon-o-user',
                                                    PermissionType::GROUP->value      => 'heroicon-o-user-group',
                                                ][$record->resource_permission];
                                            })
                                            ->formatStateUsing(fn ($state) => PermissionType::options()[$state] ?? $state)
                                            ->placeholder('-')
                                            ->label(__('security::filament/resources/user.infolist.sections.permissions.entries.resource-permission')),
                                    ])
                                    ->columns(2),
                            ])
                            ->columnSpan(2),

                        Infolists\Components\Group::make([
                            Infolists\Components\Section::make(__('security::filament/resources/user.infolist.sections.avatar.title'))
                                ->schema([
                                    Infolists\Components\ImageEntry::make('partner.avatar')
                                        ->hiddenLabel()
                                        ->circular()
                                        ->placeholder('—'),
                                ]),

                            Infolists\Components\Section::make(__('security::filament/resources/user.infolist.sections.multi-company.title'))
                                ->schema([
                                    Infolists\Components\TextEntry::make('allowedCompanies.name')
                                        ->icon('heroicon-o-building-office')
                                        ->placeholder('—')
                                        ->label(__('security::filament/resources/user.infolist.sections.multi-company.allowed-companies'))
                                        ->listWithLineBreaks()
                                        ->bulleted(),
                                    Infolists\Components\TextEntry::make('defaultCompany.name')
                                        ->icon('heroicon-o-building-office-2')
                                        ->placeholder('—')
                                        ->label(__('security::filament/resources/user.infolist.sections.multi-company.default-company')),
                                ]),

                            Infolists\Components\Section::make(__('security::filament/resources/user.infolist.sections.lang-and-status.title'))
                                ->schema([
                                    Infolists\Components\IconEntry::make('is_active')
                                        ->label(__('security::filament/resources/user.infolist.sections.lang-and-status.entries.status'))
                                        ->boolean(),
                                ]),
                        ])->columnSpan(1),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
            'view'   => Pages\ViewUsers::route('/{record}'),
        ];
    }
}
