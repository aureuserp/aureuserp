<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Account\Enums\RoundingMethod;
use Webkul\Account\Enums\RoundingStrategy;
use Webkul\Account\Filament\Resources\CashRoundingResource\Pages\CreateCashRounding;
use Webkul\Account\Filament\Resources\CashRoundingResource\Pages\EditCashRounding;
use Webkul\Account\Filament\Resources\CashRoundingResource\Pages\ListCashRounding;
use Webkul\Account\Filament\Resources\CashRoundingResource\Pages\ViewCashRounding;
use Webkul\Account\Models\CashRounding;

class CashRoundingResource extends Resource
{
    protected static ?string $model = CashRounding::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->label(__('accounts::filament/resources/cash-rounding.form.fields.name'))
                                    ->autofocus(),
                                TextInput::make('rounding')
                                    ->label(__('accounts::filament/resources/cash-rounding.form.fields.rounding-precision'))
                                    ->required()
                                    ->numeric()
                                    ->default(0.01)
                                    ->minValue(0)
                                    ->maxValue(99999999999),
                                Select::make('strategy')
                                    ->options(RoundingStrategy::class)
                                    ->default(RoundingStrategy::BIGGEST_TAX->value)
                                    ->label(__('accounts::filament/resources/cash-rounding.form.fields.rounding-strategy')),
                                Select::make('rounding_method')
                                    ->options(RoundingMethod::class)
                                    ->default(RoundingMethod::HALF_UP->value)
                                    ->label(__('accounts::filament/resources/cash-rounding.form.fields.rounding-method'))
                                    ->required()
                                    ->autofocus(),
                            ]),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('accounts::filament/resources/cash-rounding.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('strategy')
                    ->label(__('accounts::filament/resources/cash-rounding.table.columns.rounding-strategy'))
                    ->formatStateUsing(fn ($state) => RoundingStrategy::options()[$state] ?? $state)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('rounding_method')
                    ->label(__('accounts::filament/resources/cash-rounding.table.columns.rounding-method'))
                    ->formatStateUsing(fn ($state) => RoundingMethod::options()[$state] ?? $state)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('createdBy.name')
                    ->label(__('accounts::filament/resources/cash-rounding.table.columns.created-by'))
                    ->searchable()
                    ->sortable(),
            ])
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('accounts::filament/resources/cash-rounding.table.groups.name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('rounding_strategy')
                    ->label(__('accounts::filament/resources/cash-rounding.table.groups.rounding-strategy'))
                    ->collapsible(),
                Tables\Grouping\Group::make('rounding_method')
                    ->label(__('accounts::filament/resources/cash-rounding.table.groups.rounding-method'))
                    ->collapsible(),
                Tables\Grouping\Group::make('createdBy.name')
                    ->label(__('accounts::filament/resources/cash-rounding.table.groups.created-by'))
                    ->collapsible(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::filament/resources/cash-rounding.table.actions.delete.notification.title'))
                            ->body(__('accounts::filament/resources/cash-rounding.table.actions.delete.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/cash-rounding.table.actions.delete.notification.title'))
                                ->body(__('accounts::filament/resources/cash-rounding.table.actions.delete.notification.body'))
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
                        Group::make()
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('accounts::filament/resources/cash-rounding.infolist.entries.name'))
                                    ->icon('heroicon-o-document-text'),
                                TextEntry::make('rounding')
                                    ->label(__('accounts::filament/resources/cash-rounding.infolist.entries.rounding-precision'))
                                    ->icon('heroicon-o-calculator')
                                    ->numeric(
                                        decimalPlaces: 2,
                                        decimalSeparator: '.',
                                        thousandsSeparator: ','
                                    ),
                                TextEntry::make('strategy')
                                    ->label(__('accounts::filament/resources/cash-rounding.infolist.entries.rounding-strategy'))
                                    ->icon('heroicon-o-cog')
                                    ->formatStateUsing(fn (string $state): string => RoundingStrategy::options()[$state]),
                                TextEntry::make('rounding_method')
                                    ->label(__('accounts::filament/resources/cash-rounding.infolist.entries.rounding-method'))
                                    ->icon('heroicon-o-adjustments-horizontal')
                                    ->formatStateUsing(fn (string $state): string => RoundingMethod::options()[$state]),
                            ])->columns(2),
                    ])->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCashRounding::route('/'),
            'create' => CreateCashRounding::route('/create'),
            'view'   => ViewCashRounding::route('/{record}'),
            'edit'   => EditCashRounding::route('/{record}/edit'),
        ];
    }
}
