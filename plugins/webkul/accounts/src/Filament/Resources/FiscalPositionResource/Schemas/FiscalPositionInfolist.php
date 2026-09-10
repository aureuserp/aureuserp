<?php

namespace Webkul\Account\Filament\Resources\FiscalPositionResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Webkul\Support\Filament\Infolists\Components\RepeatableEntry;
use Webkul\Support\Filament\Infolists\Components\Repeater\TableColumn as InfolistTableColumn;

class FiscalPositionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Grid::make()
                                    ->schema([
                                        TextEntry::make('name')
                                            ->label(__('accounts::filament/resources/fiscal-position.infolist.entries.name'))
                                            ->placeholder('-')
                                            ->icon('heroicon-o-document-text'),
                                        TextEntry::make('foreign_vat')
                                            ->label(__('accounts::filament/resources/fiscal-position.infolist.entries.foreign-vat'))
                                            ->placeholder('-')
                                            ->icon('heroicon-o-document'),
                                        TextEntry::make('country.name')
                                            ->label(__('accounts::filament/resources/fiscal-position.infolist.entries.country'))
                                            ->placeholder('-')
                                            ->icon('heroicon-o-globe-alt'),
                                        TextEntry::make('countryGroup.name')
                                            ->label(__('accounts::filament/resources/fiscal-position.infolist.entries.country-group'))
                                            ->placeholder('-')
                                            ->icon('heroicon-o-map'),
                                        TextEntry::make('zip_from')
                                            ->label(__('accounts::filament/resources/fiscal-position.infolist.entries.zip-from'))
                                            ->placeholder('-')
                                            ->icon('heroicon-o-map-pin'),
                                        TextEntry::make('zip_to')
                                            ->label(__('accounts::filament/resources/fiscal-position.infolist.entries.zip-to'))
                                            ->placeholder('-')
                                            ->icon('heroicon-o-map-pin'),
                                        IconEntry::make('auto_reply')
                                            ->label(__('accounts::filament/resources/fiscal-position.infolist.entries.detect-automatically'))
                                            ->placeholder('-'),
                                    ])->columns(2),
                            ]),
                        TextEntry::make('notes')
                            ->label(__('accounts::filament/resources/fiscal-position.infolist.entries.notes'))
                            ->placeholder('-')
                            ->markdown(),
                    ])->columnSpanFull(),
                Tabs::make('Mappings')
                    ->tabs([
                        Tab::make('Tax Mapping')
                            ->schema([
                                RepeatableEntry::make('taxes')
                                    ->hiddenLabel()
                                    ->live()
                                    ->table([
                                        InfolistTableColumn::make('taxSource.name')
                                            ->alignCenter()
                                            ->label(__('accounts::filament/resources/fiscal-position.form.tabs.tax-mapping.table.columns.tax-source')),

                                        InfolistTableColumn::make('taxDestination.name')
                                            ->alignCenter()
                                            ->label(__('accounts::filament/resources/fiscal-position.form.tabs.tax-mapping.table.columns.tax-destination')),
                                    ])
                                    ->schema([
                                        TextEntry::make('taxSource.name')
                                            ->placeholder('-'),

                                        TextEntry::make('taxDestination.name')
                                            ->placeholder('-'),
                                    ]),
                            ]),
                        Tab::make('Account Mapping')
                            ->schema([
                                RepeatableEntry::make('accounts')
                                    ->hiddenLabel()
                                    ->live()
                                    ->table([
                                        InfolistTableColumn::make('accountSource.name')
                                            ->alignCenter()
                                            ->label(__('accounts::filament/resources/fiscal-position.form.tabs.account-mapping.table.columns.source-account')),

                                        InfolistTableColumn::make('accountDestination.name')
                                            ->alignCenter()
                                            ->label(__('accounts::filament/resources/fiscal-position.form.tabs.account-mapping.table.columns.destination-account')),
                                    ])
                                    ->schema([
                                        TextEntry::make('accountSource.name')
                                            ->placeholder('-'),

                                        TextEntry::make('accountDestination.name')
                                            ->placeholder('-'),
                                    ]),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}
