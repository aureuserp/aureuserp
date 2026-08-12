<?php

namespace Webkul\Support\Filament\Resources\CurrencyResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Support\Filament\Infolists\Components\RepeatableEntry;
use Webkul\Support\Filament\Infolists\Components\Repeater\TableColumn as InfolistTableColumn;

class CurrencyInfolist
{
    public static function configure(Schema $schema): Schema
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
}
