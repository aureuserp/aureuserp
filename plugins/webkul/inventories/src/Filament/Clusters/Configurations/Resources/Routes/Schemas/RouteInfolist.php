<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\Routes\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class RouteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.general.entries.route'))
                                    ->icon('heroicon-o-arrow-path')
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold),
                                TextEntry::make('company.name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.general.entries.company'))
                                    ->icon('heroicon-o-building-office'),
                            ]),

                        Section::make(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.applicable-on.title'))
                            ->description(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.applicable-on.description'))
                            ->schema([
                                Grid::make()
                                    ->schema([
                                        IconEntry::make('product_category_selectable')
                                            ->label(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.applicable-on.entries.product-categories'))
                                            ->boolean()
                                            ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),
                                        IconEntry::make('product_selectable')
                                            ->label(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.applicable-on.entries.products'))
                                            ->boolean()
                                            ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),
                                        IconEntry::make('packaging_selectable')
                                            ->label(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.applicable-on.entries.packaging'))
                                            ->boolean()
                                            ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),
                                    ])
                                    ->columns(3),

                                Grid::make()
                                    ->schema([
                                        IconEntry::make('warehouse_selectable')
                                            ->label(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.applicable-on.entries.warehouses'))
                                            ->boolean()
                                            ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),
                                        TextEntry::make('warehouses.name')
                                            ->label(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.applicable-on.entries.warehouses'))
                                            ->listWithLineBreaks()
                                            ->visible(fn ($record) => $record->warehouse_selectable)
                                            ->icon('heroicon-o-building-office'),
                                    ])
                                    ->columns(2),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                TextEntry::make('creator.name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-m-user'),

                                TextEntry::make('updated_at')
                                    ->label(__('inventories::filament/clusters/configurations/resources/route.infolist.sections.record-information.entries.last-updated'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
