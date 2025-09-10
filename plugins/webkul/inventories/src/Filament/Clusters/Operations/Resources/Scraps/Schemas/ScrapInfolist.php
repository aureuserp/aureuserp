<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\Scraps\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Inventory\Models\Scrap;
use Webkul\Inventory\Settings\OperationSettings;
use Webkul\Inventory\Settings\TraceabilitySettings;

class ScrapInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/operations/resources/scrap.infolist.sections.general.title'))
                            ->schema([
                                Group::make()
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                TextEntry::make('product.name')
                                                    ->label(__('inventories::filament/clusters/operations/resources/scrap.infolist.sections.general.entries.product'))
                                                    ->icon('heroicon-o-shopping-bag'),

                                                TextEntry::make('qty')
                                                    ->label(__('inventories::filament/clusters/operations/resources/scrap.infolist.sections.general.entries.quantity'))
                                                    ->icon('heroicon-o-calculator')
                                                    ->suffix(fn (Scrap $record) => ' '.$record->uom?->name),

                                                TextEntry::make('lot.name')
                                                    ->label(__('inventories::filament/clusters/operations/resources/scrap.infolist.sections.general.entries.lot'))
                                                    ->icon('heroicon-o-rectangle-stack')
                                                    ->placeholder('—')
                                                    ->visible(fn (TraceabilitySettings $settings) => $settings->enable_lots_serial_numbers),

                                                TextEntry::make('tags.name')
                                                    ->label(__('inventories::filament/clusters/operations/resources/scrap.infolist.sections.general.entries.tags'))
                                                    ->icon('heroicon-o-tag')
                                                    ->badge()
                                                    ->separator(','),
                                            ]),

                                        Group::make()
                                            ->schema([
                                                TextEntry::make('package.name')
                                                    ->label(__('inventories::filament/clusters/operations/resources/scrap.infolist.sections.general.entries.package'))
                                                    ->icon('heroicon-o-cube')
                                                    ->placeholder('—')
                                                    ->visible(fn (OperationSettings $settings) => $settings->enable_packages),

                                                TextEntry::make('partner.name')
                                                    ->label(__('inventories::filament/clusters/operations/resources/scrap.infolist.sections.general.entries.owner'))
                                                    ->icon('heroicon-o-user-circle'),

                                                TextEntry::make('sourceLocation.full_name')
                                                    ->label(__('inventories::filament/clusters/operations/resources/scrap.infolist.sections.general.entries.source-location'))
                                                    ->icon('heroicon-o-map-pin'),

                                                TextEntry::make('destinationLocation.full_name')
                                                    ->label(__('inventories::filament/clusters/operations/resources/scrap.infolist.sections.general.entries.destination-location'))
                                                    ->icon('heroicon-o-map-pin'),

                                                TextEntry::make('origin')
                                                    ->label(__('inventories::filament/clusters/operations/resources/scrap.infolist.sections.general.entries.source-document'))
                                                    ->icon('heroicon-o-document-text')
                                                    ->placeholder('—'),

                                                TextEntry::make('company.name')
                                                    ->label(__('inventories::filament/clusters/operations/resources/scrap.infolist.sections.general.entries.company'))
                                                    ->icon('heroicon-o-building-office'),
                                            ]),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/operations/resources/scrap.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('inventories::filament/clusters/operations/resources/scrap.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                TextEntry::make('creator.name')
                                    ->label(__('inventories::filament/clusters/operations/resources/scrap.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-m-user'),

                                TextEntry::make('updated_at')
                                    ->label(__('inventories::filament/clusters/operations/resources/scrap.infolist.sections.record-information.entries.last-updated'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
