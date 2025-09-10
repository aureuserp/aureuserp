<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackageTypes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class PackageTypeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/package-type.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.infolist.sections.general.entries.name'))
                                    ->icon('heroicon-o-tag')
                                    ->weight(FontWeight::Bold)
                                    ->size(TextSize::Large),

                                Group::make([
                                    Section::make(__('inventories::filament/clusters/configurations/resources/package-type.infolist.sections.general.entries.fieldsets.size.title'))
                                        ->schema([
                                            Grid::make(3)
                                                ->schema([
                                                    TextEntry::make('length')
                                                        ->label(__('inventories::filament/clusters/configurations/resources/package-type.infolist.sections.general.entries.fieldsets.size.entries.length'))
                                                        ->icon('heroicon-o-arrows-right-left')
                                                        ->numeric()
                                                        ->suffix(' cm'),

                                                    TextEntry::make('width')
                                                        ->label(__('inventories::filament/clusters/configurations/resources/package-type.infolist.sections.general.entries.fieldsets.size.entries.width'))
                                                        ->icon('heroicon-o-arrows-up-down')
                                                        ->numeric()
                                                        ->suffix(' cm'),

                                                    TextEntry::make('height')
                                                        ->label(__('inventories::filament/clusters/configurations/resources/package-type.infolist.sections.general.entries.fieldsets.size.entries.height'))
                                                        ->icon('heroicon-o-arrows-up-down')
                                                        ->numeric()
                                                        ->suffix(' cm'),
                                                ]),
                                        ])
                                        ->icon('heroicon-o-cube'),
                                ]),

                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('base_weight')
                                            ->label(__('inventories::filament/clusters/configurations/resources/package-type.infolist.sections.general.entries.weight'))
                                            ->icon('heroicon-o-scale')
                                            ->numeric()
                                            ->suffix(' kg'),

                                        TextEntry::make('max_weight')
                                            ->label(__('inventories::filament/clusters/configurations/resources/package-type.infolist.sections.general.entries.max-weight'))
                                            ->icon('heroicon-o-scale')
                                            ->numeric()
                                            ->suffix(' kg'),
                                    ]),

                                TextEntry::make('barcode')
                                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.infolist.sections.general.entries.barcode'))
                                    ->icon('heroicon-o-bars-4')
                                    ->placeholder('—'),

                                TextEntry::make('company.name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.infolist.sections.general.entries.company'))
                                    ->icon('heroicon-o-building-office'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/package-type.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                TextEntry::make('creator.name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-m-user'),

                                TextEntry::make('updated_at')
                                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.infolist.sections.record-information.entries.last-updated'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
