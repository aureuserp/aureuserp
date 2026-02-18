<?php

namespace Webkul\Support\Filament\Resources;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\QueryException;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;
use Webkul\Support\Filament\Infolists\Components\RepeatableEntry;
use Webkul\Support\Filament\Infolists\Components\Repeater\TableColumn as InfolistTableColumn;
use Webkul\Support\Filament\Resources\CurrencyResource\Pages\CreateCurrency;
use Webkul\Support\Filament\Resources\CurrencyResource\Pages\EditCurrency;
use Webkul\Support\Filament\Resources\CurrencyResource\Pages\ListCurrencies;
use Webkul\Support\Filament\Resources\CurrencyResource\Pages\ViewCurrency;
use Webkul\Support\Models\Currency;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $isGloballySearchable = false;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('support::filament/resources/currency.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('support::filament/resources/currency.navigation.group');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('support::filament/resources/currency.form.sections.currency-details.title'))
                                    ->schema([
                                        TextInput::make('name')
                                            ->label(__('support::filament/resources/currency.form.sections.currency-details.fields.name'))
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('support::filament/resources/currency.form.sections.currency-details.fields.name-tooltip')),
                                        TextInput::make('symbol')
                                            ->label(__('support::filament/resources/currency.form.sections.currency-details.fields.symbol'))
                                            ->maxLength(10),
                                        TextInput::make('full_name')
                                            ->label(__('support::filament/resources/currency.form.sections.currency-details.fields.full-name'))
                                            ->maxLength(255),
                                        TextInput::make('iso_numeric')
                                            ->label(__('support::filament/resources/currency.form.sections.currency-details.fields.iso-numeric'))
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(999),
                                    ])
                                    ->columns(2),
                                Section::make(__('support::filament/resources/currency.form.sections.format-information.title'))
                                    ->schema([
                                        TextInput::make('decimal_places')
                                            ->label(__('support::filament/resources/currency.form.sections.format-information.fields.decimal-places'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(6)
                                            ->default(2),
                                        TextInput::make('rounding')
                                            ->label(__('support::filament/resources/currency.form.sections.format-information.fields.rounding'))
                                            ->numeric()
                                            ->step(0.01)
                                            ->minValue(0)
                                            ->default(0.00)
                                            ->helperText(__('support::filament/resources/currency.form.sections.format-information.fields.rounding-helper-text')),
                                    ])
                                    ->columns(2),
                            ])
                            ->columnSpan(['lg' => 2]),
                        Group::make()
                            ->schema([
                                Section::make(__('support::filament/resources/currency.form.sections.status-and-configuration-information.title'))
                                    ->schema([
                                        Toggle::make('active')
                                            ->label(__('support::filament/resources/currency.form.sections.status-and-configuration-information.fields.status'))
                                            ->default(true),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ])
                    ->columns(3),
                Section::make(__('support::filament/resources/currency.form.sections.rates.title'))
                    ->description(__('support::filament/resources/currency.form.sections.rates.description'))
                    ->schema([
                        Repeater::make('rates')
                            ->relationship('rates')
                            ->hiddenLabel()
                            ->compact()
                            ->minItems(1)
                            ->addActionLabel(__('support::filament/resources/currency.form.sections.rates.add-rate'))
                            ->deleteAction(function (Action $action) {
                                return $action->requiresConfirmation();
                            })
                            ->cloneable()
                            ->table([
                                TableColumn::make('name')
                                    ->label(__('support::filament/resources/currency.form.sections.rates.fields.name')),
                                TableColumn::make('rate')
                                    ->label(__('support::filament/resources/currency.form.sections.rates.fields.unit-per-currency', [
                                        'currency' => config('app.currency'),
                                    ])),
                                TableColumn::make('rate_temp')
                                    ->label(__('support::filament/resources/currency.form.sections.rates.fields.currency-per-unit', [
                                        'currency' => config('app.currency'),
                                    ])),
                            ])
                            ->schema([
                                DatePicker::make('name')
                                    ->label(__('support::filament/resources/currency.form.sections.rates.fields.name'))
                                    ->required()
                                    ->native(false)
                                    ->default(today())
                                    ->format('Y-m-d')
                                    ->displayFormat('Y-m-d'),
                                TextInput::make('rate')
                                    ->label(__('support::filament/resources/currency.form.sections.rates.fields.unit-per-currency', [
                                        'currency' => config('app.currency'),
                                    ]))
                                    ->required()
                                    ->numeric()
                                    ->step(0.000001)
                                    ->minValue(1)
                                    ->default(1.000000)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state && $state > 0) {
                                            $set('rate_temp', round(1 / $state, 6));
                                        }
                                    })
                                    ->afterStateHydrated(function ($state, callable $set) {
                                        if ($state && $state > 0) {
                                            $set('rate_temp', round(1 / $state, 6));
                                        }
                                    }),
                                TextInput::make('rate_temp')
                                    ->label(__('support::filament/resources/currency.form.sections.rates.fields.currency-per-unit', [
                                        'currency' => config('app.currency'),
                                    ]))
                                    ->readOnly()
                                    ->dehydrated(false)
                                    ->default(1.000000),
                            ]),
                    ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->label(__('support::filament/resources/currency.table.columns.name'))
                    ->sortable(),
                TextColumn::make('symbol')
                    ->label(__('support::filament/resources/currency.table.columns.symbol'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('full_name')
                    ->label(__('support::filament/resources/currency.table.columns.full-name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('iso_numeric')
                    ->label(__('support::filament/resources/currency.table.columns.iso-numeric'))
                    ->sortable(),
                TextColumn::make('decimal_places')
                    ->label(__('support::filament/resources/currency.table.columns.decimal-places'))
                    ->sortable(),
                TextColumn::make('rounding')
                    ->label(__('support::filament/resources/currency.table.columns.rounding'))
                    ->money('USD', divideBy: 1)
                    ->sortable(),
                IconColumn::make('active')
                    ->label(__('support::filament/resources/currency.table.columns.status'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('support::filament/resources/currency.table.columns.created-at'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('support::filament/resources/currency.table.columns.updated-at'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('support::filament/resources/currency.table.groups.name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('active')
                    ->label(__('support::filament/resources/currency.table.groups.status'))
                    ->collapsible(),
                Tables\Grouping\Group::make('decimal_places')
                    ->label(__('support::filament/resources/currency.table.groups.decimal-places'))
                    ->collapsible(),
            ])
            ->filters([
                TernaryFilter::make('active')
                    ->label(__('support::filament/resources/currency.table.filters.status')),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('support::filament/resources/currency.table.actions.delete.notification.title'))
                                ->body(__('support::filament/resources/currency.table.actions.delete.notification.body')),
                        )
                        ->action(function (Currency $record) {
                            try {
                                $record->delete();

                                Notification::make()
                                    ->success()
                                    ->title(__('support::filament/resources/currency.table.actions.delete.notification.success.title'))
                                    ->body(__('support::filament/resources/currency.table.actions.delete.notification.success.body'))
                                    ->send();
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('support::filament/resources/currency.table.actions.delete.notification.error.title'))
                                    ->body(__('support::filament/resources/currency.table.actions.delete.notification.error.body'))
                                    ->send();
                            }
                        }),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('support::filament/resources/currency.table.bulk-actions.delete.notification.title'))
                                ->body(__('support::filament/resources/currency.table.bulk-actions.delete.notification.body')),
                        ),
                ]),
            ])
            ->reorderable('id');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 3])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('support::filament/resources/currency.infolist.sections.currency-details.title'))
                                    ->schema([
                                        TextEntry::make('name')
                                            ->icon('heroicon-o-currency-dollar')
                                            ->placeholder('—')
                                            ->label(__('support::filament/resources/currency.infolist.sections.currency-details.entries.name')),
                                        TextEntry::make('symbol')
                                            ->icon('heroicon-o-tag')
                                            ->placeholder('—')
                                            ->label(__('support::filament/resources/currency.infolist.sections.currency-details.entries.symbol')),
                                        TextEntry::make('full_name')
                                            ->icon('heroicon-o-document-text')
                                            ->placeholder('—')
                                            ->label(__('support::filament/resources/currency.infolist.sections.currency-details.entries.full-name')),
                                        TextEntry::make('iso_numeric')
                                            ->icon('heroicon-o-hashtag')
                                            ->placeholder('—')
                                            ->label(__('support::filament/resources/currency.infolist.sections.currency-details.entries.iso-numeric')),
                                    ])->columns(2),
                                Section::make(__('support::filament/resources/currency.infolist.sections.format-information.title'))
                                    ->schema([
                                        TextEntry::make('decimal_places')
                                            ->icon('heroicon-o-calculator')
                                            ->placeholder('—')
                                            ->label(__('support::filament/resources/currency.infolist.sections.format-information.entries.decimal-places')),
                                        TextEntry::make('rounding')
                                            ->icon('heroicon-o-arrow-path-rounded-square')
                                            ->placeholder('—')
                                            ->money('USD', divideBy: 1)
                                            ->label(__('support::filament/resources/currency.infolist.sections.format-information.entries.rounding')),
                                    ])->columns(2),
                            ])->columnSpan(2),
                        Group::make()
                            ->schema([
                                Section::make(__('support::filament/resources/currency.infolist.sections.status-and-configuration-information.title'))
                                    ->schema([
                                        IconEntry::make('active')
                                            ->label(__('support::filament/resources/currency.infolist.sections.status-and-configuration-information.entries.status')),
                                    ]),
                            ])
                            ->columnSpan(1),
                    ]),
                Section::make(__('support::filament/resources/currency.infolist.sections.rates.title'))
                    ->schema([
                        RepeatableEntry::make('rates')
                            ->hiddenLabel()
                            ->table([
                                InfolistTableColumn::make('name')
                                    ->label(__('support::filament/resources/currency.infolist.sections.rates.entries.name')),
                                InfolistTableColumn::make('rate')
                                    ->label(__('support::filament/resources/currency.infolist.sections.rates.entries.unit-per-currency', [
                                        'currency' => config('app.currency'),
                                    ])),
                                InfolistTableColumn::make('rate_temp')
                                    ->label(__('support::filament/resources/currency.infolist.sections.rates.entries.currency-per-unit', [
                                        'currency' => config('app.currency'),
                                    ])),
                            ])
                            ->schema([
                                TextEntry::make('name')
                                    ->placeholder('-')
                                    ->date('Y-m-d'),
                                TextEntry::make('rate')
                                    ->placeholder('-'),
                                TextEntry::make('rate_temp')
                                    ->placeholder('-')
                                    ->getStateUsing(function ($record) {
                                        if ($record && $record->rate && $record->rate > 0) {
                                            return round(1 / $record->rate, 6);
                                        }

                                        return null;
                                    }),
                            ]),
                    ]),
            ])
            ->columns(1);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCurrencies::route('/'),
            'create' => CreateCurrency::route('/create'),
            'view'   => ViewCurrency::route('/{record}'),
            'edit'   => EditCurrency::route('/{record}/edit'),
        ];
    }
}
