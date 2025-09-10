<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\Warehouses\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class WarehouseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/warehouse.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/warehouse.infolist.sections.general.entries.name'))
                                    ->icon('heroicon-o-building-storefront')
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold),

                                TextEntry::make('code')
                                    ->label(__('inventories::filament/clusters/configurations/resources/warehouse.infolist.sections.general.entries.code'))
                                    ->icon('heroicon-m-hashtag'),

                                Group::make()
                                    ->schema([
                                        TextEntry::make('company.name')
                                            ->label(__('inventories::filament/clusters/configurations/resources/warehouse.infolist.sections.general.entries.company'))
                                            ->icon('heroicon-o-building-office'),
                                        TextEntry::make('partnerAddress.name')
                                            ->label(__('inventories::filament/clusters/configurations/resources/warehouse.infolist.sections.general.entries.address'))
                                            ->icon('heroicon-o-map'),
                                    ])
                                    ->columns(2),
                            ]),

                        Section::make(__('inventories::filament/clusters/configurations/resources/warehouse.infolist.sections.settings.title'))
                            ->schema([
                                Fieldset::make(__('inventories::filament/clusters/configurations/resources/warehouse.infolist.sections.settings.entries.shipment-management'))
                                    ->schema([
                                        TextEntry::make('reception_steps')
                                            ->label(__('inventories::filament/clusters/configurations/resources/warehouse.infolist.sections.settings.entries.incoming-shipments'))
                                            ->icon('heroicon-o-truck'),

                                        TextEntry::make('delivery_steps')
                                            ->label(__('inventories::filament/clusters/configurations/resources/warehouse.infolist.sections.settings.entries.outgoing-shipments'))
                                            ->icon('heroicon-o-paper-airplane'),
                                    ]),

                                Fieldset::make(__('inventories::filament/clusters/configurations/resources/warehouse.infolist.sections.settings.entries.resupply-management'))
                                    ->schema([
                                        TextEntry::make('supplierWarehouses.name')
                                            ->label(__('inventories::filament/clusters/configurations/resources/warehouse.infolist.sections.settings.entries.resupply-from'))
                                            ->icon('heroicon-o-refresh')
                                            ->placeholder('—'),
                                    ]),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/warehouse.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('inventories::filament/clusters/configurations/resources/warehouse.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                TextEntry::make('creator.name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/warehouse.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-m-user'),

                                TextEntry::make('updated_at')
                                    ->label(__('inventories::filament/clusters/configurations/resources/warehouse.infolist.sections.record-information.entries.last-updated'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
