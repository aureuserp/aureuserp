<?php

namespace Webkul\Account\Filament\Resources\FiscalPositions\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

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
                    ]),
            ]);
    }
}
