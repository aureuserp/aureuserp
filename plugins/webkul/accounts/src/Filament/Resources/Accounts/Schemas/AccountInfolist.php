<?php

namespace Webkul\Account\Filament\Resources\Accounts\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Account\Enums\AccountType;

class AccountInfolist
{
    public static function configure(Schema $schema): Schema
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
                    ])->columns(2),
            ]);
    }
}
