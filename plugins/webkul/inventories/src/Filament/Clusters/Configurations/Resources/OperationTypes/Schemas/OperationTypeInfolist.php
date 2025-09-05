<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypes\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class OperationTypeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.sections.general.entries.name'))
                                    ->icon('heroicon-o-queue-list')
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold)
                                    ->columnSpan(2),
                            ]),

                        Tabs::make()
                            ->tabs([
                                Tab::make(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.title'))
                                    ->icon('heroicon-o-cog')
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                TextEntry::make('type')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.entries.type'))
                                                    ->icon('heroicon-o-cog'),
                                                TextEntry::make('sequence_code')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.entries.sequence_code'))
                                                    ->icon('heroicon-o-tag'),
                                                IconEntry::make('print_label')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.entries.print_label'))
                                                    ->boolean()
                                                    ->icon('heroicon-o-printer'),
                                                TextEntry::make('warehouse.name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.entries.warehouse'))
                                                    ->icon('heroicon-o-building-office'),
                                                TextEntry::make('reservation_method')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.entries.reservation_method'))
                                                    ->icon('heroicon-o-clock'),
                                                IconEntry::make('auto_show_reception_report')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.entries.auto_show_reception_report'))
                                                    ->boolean()
                                                    ->icon('heroicon-o-document-text'),
                                            ])
                                            ->columns(2),

                                        Group::make()
                                            ->schema([
                                                TextEntry::make('company.name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.entries.company'))
                                                    ->icon('heroicon-o-building-office'),
                                                TextEntry::make('returnOperationType.name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.entries.return_operation_type'))
                                                    ->icon('heroicon-o-arrow-uturn-left'),
                                                TextEntry::make('create_backorder')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.entries.create_backorder'))
                                                    ->icon('heroicon-o-archive-box'),
                                                TextEntry::make('move_type')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.entries.move_type'))
                                                    ->icon('heroicon-o-arrows-right-left'),
                                            ])
                                            ->columns(2),

                                        Fieldset::make(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.fieldsets.lots.title'))
                                            ->schema([
                                                IconEntry::make('use_create_lots')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.fieldsets.lots.entries.use_create_lots'))
                                                    ->boolean()
                                                    ->icon('heroicon-o-plus-circle'),
                                                IconEntry::make('use_existing_lots')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.fieldsets.lots.entries.use_existing_lots'))
                                                    ->boolean()
                                                    ->icon('heroicon-o-archive-box'),
                                            ]),

                                        Fieldset::make(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.fieldsets.locations.title'))
                                            ->schema([
                                                TextEntry::make('sourceLocation.full_name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.fieldsets.locations.entries.source_location'))
                                                    ->icon('heroicon-o-map-pin'),
                                                TextEntry::make('destinationLocation.full_name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.tabs.general.fieldsets.locations.entries.destination_location'))
                                                    ->icon('heroicon-o-map-pin'),
                                            ]),
                                    ]),
                            ])
                            ->columnSpan('full'),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                TextEntry::make('creator.name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-m-user'),

                                TextEntry::make('updated_at')
                                    ->label(__('inventories::filament/clusters/configurations/resources/operation-type.infolist.sections.record-information.entries.last-updated'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
