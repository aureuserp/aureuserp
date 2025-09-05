<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\Locations\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class LocationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.general.entries.location'))
                                    ->icon('heroicon-o-map-pin')
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold)
                                    ->columnSpan(2),
                                TextEntry::make('parent.full_name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.general.entries.parent-location'))
                                    ->icon('heroicon-o-building-office-2'),
                                TextEntry::make('description')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.general.entries.external-notes'))
                                    ->markdown()
                                    ->placeholder('—'),
                                TextEntry::make('type')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.settings.entries.location-type'))
                                    ->icon('heroicon-o-tag'),
                                TextEntry::make('company.name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.settings.entries.company'))
                                    ->icon('heroicon-o-building-office'),
                                TextEntry::make('storageCategory.name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.settings.entries.storage-category'))
                                    ->icon('heroicon-o-archive-box')
                                    ->placeholder('—'),
                            ])
                            ->columns(2),

                        Section::make(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.settings.title'))
                            ->schema([
                                IconEntry::make('is_scrap')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.settings.entries.is-scrap')),
                                IconEntry::make('is_dock')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.settings.entries.is-dock')),
                                IconEntry::make('is_replenish')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.settings.entries.is-replenish')),

                                Fieldset::make(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.settings.entries.cyclic-counting'))
                                    ->schema([
                                        TextEntry::make('cyclic_inventory_frequency')
                                            ->label(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.settings.entries.inventory-frequency'))
                                            ->icon('heroicon-o-clock')
                                            ->placeholder('—'),
                                        TextEntry::make('cyclic_inventory_last')
                                            ->label(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.settings.entries.last-inventory'))
                                            ->icon('heroicon-o-calendar')
                                            ->placeholder('—'),
                                        TextEntry::make('cyclic_inventory_next_expected')
                                            ->label(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.settings.entries.next-expected'))
                                            ->icon('heroicon-o-calendar-days')
                                            ->placeholder('—'),
                                    ]),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                TextEntry::make('creator.name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-m-user'),

                                TextEntry::make('updated_at')
                                    ->label(__('inventories::filament/clusters/configurations/resources/location.infolist.sections.record-information.entries.last-updated'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
