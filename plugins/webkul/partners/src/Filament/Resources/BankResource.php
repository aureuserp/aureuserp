<?php

namespace Webkul\Partner\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Partner\Models\Bank;

class BankResource extends Resource
{
    protected static ?string $model = Bank::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function getNavigationGroup(): string
    {
        return __('partners::filament/resources/bank.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('partners::filament/resources/bank.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('partners::filament/resources/bank.form.sections.general.title'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('partners::filament/resources/bank.form.sections.general.fields.name'))
                            ->maxLength(255)
                            ->required(),
                        TextInput::make('code')
                            ->label(__('partners::filament/resources/bank.form.sections.general.fields.code'))
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label(__('partners::filament/resources/bank.form.sections.general.fields.email'))
                            ->email()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label(__('partners::filament/resources/bank.form.sections.general.fields.phone'))
                            ->tel()
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Section::make(__('partners::filament/resources/bank.form.sections.address.title'))
                    ->schema([
                        Select::make('country_id')
                            ->label(__('partners::filament/resources/bank.form.sections.address.fields.country'))
                            ->relationship(name: 'country', titleAttribute: 'name')
                            ->afterStateUpdated(fn (Set $set) => $set('state_id', null))
                            ->searchable()
                            ->preload()
                            ->live(),
                        Select::make('state_id')
                            ->label(__('partners::filament/resources/bank.form.sections.address.fields.state'))
                            ->relationship(
                                name: 'state',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Get $get, Builder $query) => $query->where('country_id', $get('country_id')),
                            )
                            ->searchable()
                            ->preload(),
                        TextInput::make('street1')
                            ->label(__('partners::filament/resources/bank.form.sections.address.fields.street1'))
                            ->maxLength(255),
                        TextInput::make('street2')
                            ->label(__('partners::filament/resources/bank.form.sections.address.fields.street2'))
                            ->maxLength(255),
                        TextInput::make('city')
                            ->label(__('partners::filament/resources/bank.form.sections.address.fields.city'))
                            ->maxLength(255),
                        TextInput::make('zip')
                            ->label(__('partners::filament/resources/bank.form.sections.address.fields.zip'))
                            ->maxLength(255),
                        Hidden::make('creator_id')
                            ->default(Auth::user()->id),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('partners::filament/resources/bank.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->label(__('partners::filament/resources/bank.table.columns.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('country.name')
                    ->label(__('partners::filament/resources/bank.table.columns.country'))
                    ->numeric()
                    ->sortable()
                    ->sortable(),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('country.name')
                    ->label(__('partners::filament/resources/bank.table.groups.country')),
                Group::make('created_at')
                    ->label(__('partners::filament/resources/bank.table.groups.created-at'))
                    ->date(),
            ])
            ->recordActions([
                EditAction::make()
                    ->hidden(fn ($record) => $record->trashed())
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/bank.table.actions.edit.notification.title'))
                            ->body(__('partners::filament/resources/bank.table.actions.edit.notification.body')),
                    ),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/bank.table.actions.restore.notification.title'))
                            ->body(__('partners::filament/resources/bank.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/bank.table.actions.delete.notification.title'))
                            ->body(__('partners::filament/resources/bank.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/bank.table.actions.force-delete.notification.title'))
                            ->body(__('partners::filament/resources/bank.table.actions.force-delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('partners::filament/resources/bank.table.bulk-actions.restore.notification.title'))
                                ->body(__('partners::filament/resources/bank.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('partners::filament/resources/bank.table.bulk-actions.delete.notification.title'))
                                ->body(__('partners::filament/resources/bank.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('partners::filament/resources/bank.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('partners::filament/resources/bank.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ]);
    }
}
