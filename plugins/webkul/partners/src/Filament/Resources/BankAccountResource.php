<?php

namespace Webkul\Partner\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Partner\Models\BankAccount;

class BankAccountResource extends Resource
{
    protected static ?string $model = BankAccount::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function getNavigationGroup(): string
    {
        return __('partners::filament/resources/bank-account.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('partners::filament/resources/bank-account.navigation.title');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('account_number')
                    ->label(__('partners::filament/resources/bank-account.form.account-number'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\Toggle::make('can_send_money')
                    ->label(__('partners::filament/resources/bank-account.form.can-send-money'))
                    ->inline(false),
                Forms\Components\Select::make('bank_id')
                    ->label(__('partners::filament/resources/bank-account.form.bank'))
                    ->relationship(
                        'bank',
                        'name',
                        modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
                    )
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                    })
                    ->disableOptionWhen(function ($label) {
                        return str_contains($label, ' (Deleted)');
                    })
                    ->required()
                    ->searchable()
                    ->preload()
                    ->createOptionForm(fn (Form $form) => BankResource::form($form)),
                Forms\Components\Select::make('partner_id')
                    ->label(__('partners::filament/resources/bank-account.form.account-holder'))
                    ->relationship('partner', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('account_number')
                    ->label(__('partners::filament/resources/bank-account.table.columns.account-number'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bank.name')
                    ->label(__('partners::filament/resources/bank-account.table.columns.bank'))
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('partner.name')
                    ->label(__('partners::filament/resources/bank-account.table.columns.account-holder'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('can_send_money')
                    ->label(__('partners::filament/resources/bank-account.table.columns.send-money'))
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label(__('partners::filament/resources/bank-account.table.columns.deleted-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('partners::filament/resources/bank-account.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('partners::filament/resources/bank-account.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('bank.name')
                    ->label(__('partners::filament/resources/bank-account.table.groups.bank')),
                Tables\Grouping\Group::make('can_send_money')
                    ->label(__('partners::filament/resources/bank-account.table.groups.can-send-money')),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('partners::filament/resources/bank-account.table.groups.created-at'))
                    ->date(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('can_send_money')
                    ->label(__('partners::filament/resources/bank-account.table.filters.can-send-money')),
                Tables\Filters\SelectFilter::make('bank_id')
                    ->label(__('partners::filament/resources/bank-account.table.filters.bank'))
                    ->relationship('bank', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('partner_id')
                    ->label(__('partners::filament/resources/bank-account.table.filters.account-holder'))
                    ->relationship('partner', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('creator_id')
                    ->label(__('partners::filament/resources/bank-account.table.filters.creator'))
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hidden(fn ($record) => $record->trashed())
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/bank-account.table.actions.edit.notification.title'))
                            ->body(__('partners::filament/resources/bank-account.table.actions.edit.notification.body')),
                    ),
                Tables\Actions\RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/bank-account.table.actions.restore.notification.title'))
                            ->body(__('partners::filament/resources/bank-account.table.actions.restore.notification.body')),
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/bank-account.table.actions.delete.notification.title'))
                            ->body(__('partners::filament/resources/bank-account.table.actions.delete.notification.body')),
                    ),
                Tables\Actions\ForceDeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/bank-account.table.actions.force-delete.notification.title'))
                            ->body(__('partners::filament/resources/bank-account.table.actions.force-delete.notification.body')),
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('partners::filament/resources/bank-account.table.bulk-actions.restore.notification.title'))
                                ->body(__('partners::filament/resources/bank-account.table.bulk-actions.restore.notification.body')),
                        ),
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('partners::filament/resources/bank-account.table.bulk-actions.delete.notification.title'))
                                ->body(__('partners::filament/resources/bank-account.table.bulk-actions.delete.notification.body')),
                        ),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('partners::filament/resources/bank-account.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('partners::filament/resources/bank-account.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ]);
    }
}
