<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Webkul\Field\Filament\Infolists\Components\ProgressStepper as InfolistProgressStepper;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource;
use Webkul\Support\Filament\Infolists\Components\RepeatableEntry;
use Webkul\Support\Filament\Infolists\Components\Repeater\TableColumn as InfolistTableColumn;

class OperationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                InfolistProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(OperationState::options())
                    ->options(function ($record) {
                        $options = OperationState::options();

                        if ($record->state !== OperationState::CANCELED) {
                            unset($options[OperationState::CANCELED->value]);
                        }

                        if ($record->state !== OperationState::WAITING) {
                            unset($options[OperationState::WAITING->value]);
                        }

                        return $options;
                    })
                    ->default(OperationState::DRAFT),

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
                                    ->visible(OperationResource::getWarehouseSettings()->enable_locations),

                                TextEntry::make('destinationLocation.full_name')
                                    ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.sections.general.entries.destination-location'))
                                    ->icon('heroicon-o-arrow-down-tray')
                                    ->visible(OperationResource::getWarehouseSettings()->enable_locations),
                            ]),
                    ]),

                // Tabs Section
                Tabs::make('Details')
                    ->tabs([
                        // Operations Tab
                        Tab::make(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.title'))
                            ->schema([
                                RepeatableEntry::make('moves')
                                    ->columnManager()
                                    ->columnManagerColumns(2)
                                    ->table([
                                        InfolistTableColumn::make('product.name')
                                            ->alignStart()
                                            ->width(250)
                                            ->toggleable()
                                            ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.entries.product')),
                                        InfolistTableColumn::make('finalLocation.full_name')
                                            ->alignStart()
                                            ->width(150)
                                            ->toggleable(isToggledHiddenByDefault: true)
                                            ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.entries.final-location')),
                                        InfolistTableColumn::make('description_picking')
                                            ->alignStart()
                                            ->width(150)
                                            ->toggleable(isToggledHiddenByDefault: true)
                                            ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.entries.description')),
                                        InfolistTableColumn::make('scheduled_at')
                                            ->alignStart()
                                            ->width(150)
                                            ->toggleable(isToggledHiddenByDefault: true)
                                            ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.entries.scheduled-at')),
                                        InfolistTableColumn::make('deadline')
                                            ->alignStart()
                                            ->width(150)
                                            ->toggleable(isToggledHiddenByDefault: true)
                                            ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.entries.deadline')),
                                        InfolistTableColumn::make('productPackaging.name')
                                            ->alignStart()
                                            ->width(150)
                                            ->toggleable()
                                            ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.entries.packaging')),
                                        InfolistTableColumn::make('product_uom_qty')
                                            ->alignStart()
                                            ->width(100)
                                            ->toggleable()
                                            ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.entries.demand')),
                                        InfolistTableColumn::make('quantity')
                                            ->alignStart()
                                            ->width(100)
                                            ->toggleable()
                                            ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.entries.quantity')),
                                        InfolistTableColumn::make('uom.name')
                                            ->alignStart()
                                            ->width(100)
                                            ->toggleable()
                                            ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.entries.unit')),
                                        InfolistTableColumn::make('is_picked')
                                            ->alignStart()
                                            ->width(100)
                                            ->toggleable()
                                            ->label(__('inventories::filament/clusters/operations/resources/operation.infolist.tabs.operations.entries.picked')),
                                    ])
                                    ->schema([
                                        TextEntry::make('product.name'),
                                        TextEntry::make('finalLocation.full_name')
                                            ->placeholder('—')
                                            ->visible(OperationResource::getWarehouseSettings()->enable_locations),
                                        TextEntry::make('description_picking')
                                            ->placeholder('—'),
                                        TextEntry::make('scheduled_at')
                                            ->date()
                                            ->placeholder('—'),
                                        TextEntry::make('deadline')
                                            ->date()
                                            ->placeholder('—'),
                                        TextEntry::make('productPackaging.name')
                                            ->visible(OperationResource::getProductSettings()->enable_packagings)
                                            ->placeholder('—'),
                                        TextEntry::make('product_uom_qty'),
                                        TextEntry::make('quantity')
                                            ->placeholder('—'),
                                        TextEntry::make('uom.name')
                                            ->visible(OperationResource::getProductSettings()->enable_uom),
                                        IconEntry::make('is_picked'),
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
