<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\Packages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class PackageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/products/resources/package.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('inventories::filament/clusters/products/resources/package.infolist.sections.general.entries.name'))
                                    ->icon('heroicon-o-cube')
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold),

                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('packageType.name')
                                            ->label(__('inventories::filament/clusters/products/resources/package.infolist.sections.general.entries.package-type'))
                                            ->icon('heroicon-o-rectangle-stack')
                                            ->placeholder('—'),

                                        TextEntry::make('pack_date')
                                            ->label(__('inventories::filament/clusters/products/resources/package.infolist.sections.general.entries.pack-date'))
                                            ->icon('heroicon-o-calendar')
                                            ->date(),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('location.full_name')
                                            ->label(__('inventories::filament/clusters/products/resources/package.infolist.sections.general.entries.location'))
                                            ->icon('heroicon-o-map-pin')
                                            ->placeholder('—'),

                                        TextEntry::make('company.name')
                                            ->label(__('inventories::filament/clusters/products/resources/package.infolist.sections.general.entries.company'))
                                            ->icon('heroicon-o-building-office'),
                                    ]),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/products/resources/package.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('inventories::filament/clusters/products/resources/package.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                TextEntry::make('creator.name')
                                    ->label(__('inventories::filament/clusters/products/resources/package.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-m-user'),

                                TextEntry::make('updated_at')
                                    ->label(__('inventories::filament/clusters/products/resources/package.infolist.sections.record-information.entries.last-updated'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
