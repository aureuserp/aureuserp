<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\Operations\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Webkul\Inventory\Settings\ProductSettings;
use Webkul\Inventory\Settings\WarehouseSettings;

class OperationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextEntry::make('state')
                            ->badge(),
                    ])
                    ->compact(),

                Section::make(__('inventories::filament/clusters/operations/resources/operation.infolist.sections.general.title'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('partner.name')
                                    ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.sections.general.entries.contact'))
                                    ->icon('heroicon-o-user-group')
                                    ->placeholder('—'),

                                TextEntry::make('operationType.name')
                                    ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.sections.general.entries.operation-type'))
                                    ->icon('heroicon-o-clipboard-document-list'),

                                TextEntry::make('sourceLocation.full_name')
                                    ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.sections.general.entries.source-location'))
                                    ->icon('heroicon-o-arrow-up-tray')
                                    ->visible(fn (WarehouseSettings $settings): bool => $settings->enable_locations),

                                TextEntry::make('destinationLocation.full_name')
                                    ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.sections.general.entries.destination-location'))
                                    ->icon('heroicon-o-arrow-down-tray')
                                    ->visible(fn (WarehouseSettings $settings): bool => $settings->enable_locations),
                            ]),
                    ]),

                // Tabs Section
                Tabs::make('Details')
                    ->tabs([
                        // Operations Tab
                        Tab::make(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.title'))
                            ->schema([
                                RepeatableEntry::make('moves')
                                    ->schema([
                                        Grid::make(5)
                                            ->schema([
                                                TextEntry::make('product.name')
                                                    ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.entries.product'))
                                                    ->icon('heroicon-o-cube'),

                                                TextEntry::make('finalLocation.full_name')
                                                    ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.entries.final-location'))
                                                    ->icon('heroicon-o-map-pin')
                                                    ->placeholder('—')
                                                    ->visible(fn (WarehouseSettings $settings) => $settings->enable_locations),

                                                TextEntry::make('description_picking')
                                                    ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.entries.description'))
                                                    ->icon('heroicon-o-document-text')
                                                    ->placeholder('—'),

                                                TextEntry::make('scheduled_at')
                                                    ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.entries.scheduled-at'))
                                                    ->dateTime()
                                                    ->icon('heroicon-o-calendar')
                                                    ->placeholder('—'),

                                                TextEntry::make('deadline')
                                                    ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.entries.deadline'))
                                                    ->dateTime()
                                                    ->icon('heroicon-o-clock')
                                                    ->placeholder('—'),

                                                TextEntry::make('productPackaging.name')
                                                    ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.entries.packaging'))
                                                    ->icon('heroicon-o-gift')
                                                    ->visible(fn (ProductSettings $settings) => $settings->enable_packagings)
                                                    ->placeholder('—'),

                                                TextEntry::make('product_qty')
                                                    ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.entries.demand'))
                                                    ->icon('heroicon-o-calculator'),

                                                TextEntry::make('quantity')
                                                    ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.entries.quantity'))
                                                    ->icon('heroicon-o-scale')
                                                    ->placeholder('—'),

                                                TextEntry::make('uom.name')
                                                    ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.entries.unit'))
                                                    ->icon('heroicon-o-beaker')
                                                    ->visible(fn (ProductSettings $settings) => $settings->enable_uom),

                                                IconEntry::make('is_picked')
                                                    ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.entries.picked'))
                                                    ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                                                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
                                            ]),
                                    ]),
                            ]),

                        Tab::make(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.additional.title'))
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('user.name')
                                            ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.additional.entries.responsible'))
                                            ->icon('heroicon-o-user')
                                            ->placeholder('—'),

                                        TextEntry::make('move_type')
                                            ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.additional.entries.shipping-policy'))
                                            ->icon('heroicon-o-truck')
                                            ->placeholder('—'),

                                        TextEntry::make('scheduled_at')
                                            ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.additional.entries.scheduled-at'))
                                            ->dateTime()
                                            ->icon('heroicon-o-calendar')
                                            ->placeholder('—'),

                                        TextEntry::make('origin')
                                            ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.additional.entries.source-document'))
                                            ->icon('heroicon-o-document-text')
                                            ->placeholder('—'),
                                    ]),
                            ]),

                        Tab::make(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.note.title'))
                            ->schema([
                                TextEntry::make('description')
                                    ->markdown()
                                    ->hiddenLabel()
                                    ->placeholder('—'),
                            ]),
                    ]),
            ])
            ->columns(1);
    }
}
