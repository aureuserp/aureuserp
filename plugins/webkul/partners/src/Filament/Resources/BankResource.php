<?php

namespace Webkul\Partner\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('partners::filament/resources/bank.form.sections.general.title'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('partners::filament/resources/bank.form.sections.general.fields.name'))
                            ->maxLength(255)
                            ->required(),
                        Forms\Components\TextInput::make('code')
                            ->label(__('partners::filament/resources/bank.form.sections.general.fields.code'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label(__('partners::filament/resources/bank.form.sections.general.fields.email'))
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label(__('partners::filament/resources/bank.form.sections.general.fields.phone'))
                            ->tel()
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Forms\Components\Section::make(__('partners::filament/resources/bank.form.sections.address.title'))
                    ->schema([
                        Forms\Components\Select::make('country_id')
                            ->label(__('partners::filament/resources/bank.form.sections.address.fields.country'))
                            ->relationship(name: 'country', titleAttribute: 'name')
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('state_id', null))
                            ->searchable()
                            ->preload()
                            ->live(),
                        Forms\Components\Select::make('state_id')
                            ->label(__('partners::filament/resources/bank.form.sections.address.fields.state'))
                            ->relationship(
                                name: 'state',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Forms\Get $get, Builder $query) => $query->where('country_id', $get('country_id')),
                            )
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('street1')
                            ->label(__('partners::filament/resources/bank.form.sections.address.fields.street1'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('street2')
                            ->label(__('partners::filament/resources/bank.form.sections.address.fields.street2'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('city')
                            ->label(__('partners::filament/resources/bank.form.sections.address.fields.city'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('zip')
                            ->label(__('partners::filament/resources/bank.form.sections.address.fields.zip'))
                            ->maxLength(255),
                        Forms\Components\Hidden::make('creator_id')
                            ->default(Auth::user()->id),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('partners::filament/resources/bank.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label(__('partners::filament/resources/bank.table.columns.code'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('country.name')
                    ->label(__('partners::filament/resources/bank.table.columns.country'))
                    ->numeric()
                    ->sortable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('country.name')
                    ->label(__('partners::filament/resources/bank.table.groups.country')),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('partners::filament/resources/bank.table.groups.created-at'))
                    ->date(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hidden(fn ($record) => $record->trashed())
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/bank.table.actions.edit.notification.title'))
                            ->body(__('partners::filament/resources/bank.table.actions.edit.notification.body')),
                    ),
                Tables\Actions\RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/bank.table.actions.restore.notification.title'))
                            ->body(__('partners::filament/resources/bank.table.actions.restore.notification.body')),
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/bank.table.actions.delete.notification.title'))
                            ->body(__('partners::filament/resources/bank.table.actions.delete.notification.body')),
                    ),
                Tables\Actions\ForceDeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/bank.table.actions.force-delete.notification.title'))
                            ->body(__('partners::filament/resources/bank.table.actions.force-delete.notification.body')),
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('partners::filament/resources/bank.table.bulk-actions.restore.notification.title'))
                                ->body(__('partners::filament/resources/bank.table.bulk-actions.restore.notification.body')),
                        ),
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('partners::filament/resources/bank.table.bulk-actions.delete.notification.title'))
                                ->body(__('partners::filament/resources/bank.table.bulk-actions.delete.notification.body')),
                        ),
                    Tables\Actions\ForceDeleteBulkAction::make()
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
