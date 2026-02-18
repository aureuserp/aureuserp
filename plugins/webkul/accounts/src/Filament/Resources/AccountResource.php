<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Filament\Resources\AccountResource\Pages\ManageAccounts;
use Webkul\Account\Models\Account;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $isGloballySearchable = false;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    public static function getGloballySearchableAttributes(): array
    {
        return ['code', 'name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('accounts::filament/resources/account.global-search.code') => $record->code ?? '—',
            __('accounts::filament/resources/account.global-search.type') => $record->account_type ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('code')
                            ->required()
                            ->label(__('accounts::filament/resources/account.form.sections.fields.code'))
                            ->maxLength(255)
                            ->columnSpan(1)
                            ->unique(ignoreRecord: true),
                        TextInput::make('name')
                            ->required()
                            ->label(__('accounts::filament/resources/account.form.sections.fields.account-name'))
                            ->maxLength(255)
                            ->columnSpan(1),

                        Fieldset::make(__('accounts::filament/resources/account.form.sections.fields.accounting'))
                            ->schema([
                                Select::make('account_type')
                                    ->options(AccountType::groupedOptions())
                                    ->preload()
                                    ->required()
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.account-type'))
                                    ->live()
                                    ->searchable(),
                                Select::make('invoices_account_tax')
                                    ->relationship('taxes', 'name')
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.default-taxes'))
                                    ->hidden(fn (Get $get) => $get('account_type') === AccountType::OFF_BALANCE->value)
                                    ->multiple()
                                    ->preload()
                                    ->searchable(),
                                Select::make('invoices_account_account_tags')
                                    ->relationship('tags', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.tags'))
                                    ->searchable(),
                                Select::make('invoices_account_journals')
                                    ->relationship('journals', 'name')
                                    ->multiple()
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.journals'))
                                    ->preload()
                                    ->searchable(),
                                Select::make('currency_id')
                                    ->relationship(
                                        'currency',
                                        'name',
                                        modifyQueryUsing: fn (Builder $query) => $query->where('active', 1),
                                    )
                                    ->preload()
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.currency'))
                                    ->searchable(),
                                Select::make('companies')
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.companies'))
                                    ->relationship('companies', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->searchable()
                                    ->required(),
                                Toggle::make('deprecated')
                                    ->inline(false)
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.deprecated')),
                                Toggle::make('reconcile')
                                    ->inline(false)
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.reconcile')),
                                Toggle::make('non_trade')
                                    ->inline(false)
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.non-trade')),
                            ]),
                    ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label(__('accounts::filament/resources/account.table.columns.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('accounts::filament/resources/account.table.columns.account-name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('account_type')
                    ->label(__('accounts::filament/resources/account.table.columns.account-type'))
                    ->searchable()
                    ->sortable(),
                IconColumn::make('reconcile')
                    ->label(__('accounts::filament/resources/account.table.columns.reconcile'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('currency.name')
                    ->label(__('accounts::filament/resources/account.table.columns.currency'))
                    ->sortable(),
            ])
            ->groups([
                'account_type',
            ])
            ->filters([
                SelectFilter::make('account_type')
                    ->options(AccountType::groupedOptions())
                    ->label(__('accounts::filament/resources/account.table.filters.account-type')),
                SelectFilter::make('journals')
                    ->relationship('journals', 'name')
                    ->searchable()
                    ->preload()
                    ->label(__('accounts::filament/resources/account.table.filters.account-journals')),
                SelectFilter::make('currency')
                    ->relationship('currency', 'name')
                    ->searchable()
                    ->preload()
                    ->label(__('accounts::filament/resources/account.table.filters.currency')),
                TernaryFilter::make('reconcile')
                    ->label(__('accounts::filament/resources/account.table.filters.allow-reconcile')),
                TernaryFilter::make('non_trade')
                    ->label(__('accounts::filament/resources/account.table.filters.non-trade')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::filament/resources/account.table.actions.edit.notification.title'))
                            ->body(__('accounts::filament/resources/account.table.actions.edit.notification.body'))
                    ),
                DeleteAction::make()
                    ->action(function (Account $record, DeleteAction $action) {
                        if ($record->moveLines()->count() > 0) {
                            $action->failure();

                            return;
                        }

                        $record->delete();

                        $action->success();
                    })
                    ->failureNotification(
                        Notification::make()
                            ->danger()
                            ->title(__('accounts::filament/resources/account.table.actions.delete.notification.error.title'))
                            ->body(__('accounts::filament/resources/account.table.actions.delete.notification.error.body'))
                    )
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::filament/resources/account.table.actions.delete.notification.success.title'))
                            ->body(__('accounts::filament/resources/account.table.actions.delete.notification.success.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (Collection $records, DeleteBulkAction $action) {
                            $hasMoveLines = $records->contains(function ($record) {
                                return $record->moveLines()->exists();
                            });

                            if ($hasMoveLines) {
                                $action->failure();

                                return;
                            }

                            $records->each(fn (Model $record) => $record->delete());

                            $action->success();
                        })
                        ->failureNotification(
                            Notification::make()
                                ->danger()
                                ->title(__('accounts::filament/resources/account.table.bulk-actions.delete.notification.error.title'))
                                ->body(__('accounts::filament/resources/account.table.bulk-actions.delete.notification.error.body'))
                        )
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/account.table.bulk-actions.delete.notification.success.title'))
                                ->body(__('accounts::filament/resources/account.table.bulk-actions.delete.notification.success.body'))
                        ),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextEntry::make('code')
                            ->label(__('accounts::filament/resources/account.infolist.sections.entries.code'))
                            ->icon('heroicon-o-identification')
                            ->placeholder('-')
                            ->columnSpan(1),
                        TextEntry::make('name')
                            ->label(__('accounts::filament/resources/account.infolist.sections.entries.account-name'))
                            ->icon('heroicon-o-document-text')
                            ->placeholder('-')
                            ->columnSpan(1),

                        Section::make(__('accounts::filament/resources/account.infolist.sections.entries.accounting'))
                            ->schema([
                                TextEntry::make('account_type')
                                    ->label(__('accounts::filament/resources/account.infolist.sections.entries.account-type'))
                                    ->placeholder('-')
                                    ->icon('heroicon-o-tag'),
                                TextEntry::make('taxes.name')
                                    ->label(__('accounts::filament/resources/account.infolist.sections.entries.default-taxes'))
                                    ->visible(fn ($record) => $record->account_type !== AccountType::OFF_BALANCE->value)
                                    ->listWithLineBreaks()
                                    ->placeholder('-')
                                    ->icon('heroicon-o-calculator'),
                                TextEntry::make('tags.name')
                                    ->label(__('accounts::filament/resources/account.infolist.sections.entries.tags'))
                                    ->listWithLineBreaks()
                                    ->placeholder('-')
                                    ->icon('heroicon-o-tag'),
                                TextEntry::make('journals.name')
                                    ->label(__('accounts::filament/resources/account.infolist.sections.entries.journals'))
                                    ->listWithLineBreaks()
                                    ->placeholder('-')
                                    ->icon('heroicon-o-book-open'),
                                TextEntry::make('currency.name')
                                    ->label(__('accounts::filament/resources/account.infolist.sections.entries.currency'))
                                    ->placeholder('-')
                                    ->icon('heroicon-o-currency-dollar'),
                                Grid::make(['default' => 3])
                                    ->schema([
                                        IconEntry::make('deprecated')
                                            ->label(__('accounts::filament/resources/account.infolist.sections.entries.deprecated'))
                                            ->placeholder('-'),
                                        IconEntry::make('reconcile')
                                            ->label(__('accounts::filament/resources/account.infolist.sections.entries.reconcile'))
                                            ->placeholder('-'),
                                        IconEntry::make('non_trade')
                                            ->label(__('accounts::filament/resources/account.infolist.sections.entries.non-trade'))
                                            ->placeholder('-'),
                                    ]),
                            ])
                            ->columns(2),
                    ]),
            ])
            ->columns(1);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAccounts::route('/'),
        ];
    }
}
