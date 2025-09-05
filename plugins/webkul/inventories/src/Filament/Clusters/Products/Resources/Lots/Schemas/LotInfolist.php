<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\Lots\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class LotInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/products/resources/lot.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('inventories::filament/clusters/products/resources/lot.infolist.sections.general.entries.name'))
                                    ->icon('heroicon-o-rectangle-stack')
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold),

                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('product.name')
                                            ->label(__('inventories::filament/clusters/products/resources/lot.infolist.sections.general.entries.product'))
                                            ->icon('heroicon-o-cube'),

                                        TextEntry::make('reference')
                                            ->label(__('inventories::filament/clusters/products/resources/lot.infolist.sections.general.entries.reference'))
                                            ->icon('heroicon-o-document-text')
                                            ->placeholder('—'),
                                    ]),

                                TextEntry::make('description')
                                    ->label(__('inventories::filament/clusters/products/resources/lot.infolist.sections.general.entries.description'))
                                    ->html()
                                    ->placeholder('—'),

                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('total_quantity')
                                            ->label(__('inventories::filament/clusters/products/resources/lot.infolist.sections.general.entries.on-hand-qty'))
                                            ->icon('heroicon-o-calculator')
                                            ->badge(),

                                        TextEntry::make('company.name')
                                            ->label(__('inventories::filament/clusters/products/resources/lot.infolist.sections.general.entries.company'))
                                            ->icon('heroicon-o-building-office'),
                                    ]),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/products/resources/lot.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('inventories::filament/clusters/products/resources/lot.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                TextEntry::make('creator.name')
                                    ->label(__('inventories::filament/clusters/products/resources/lot.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-m-user'),

                                TextEntry::make('updated_at')
                                    ->label(__('inventories::filament/clusters/products/resources/lot.infolist.sections.record-information.entries.last-updated'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
