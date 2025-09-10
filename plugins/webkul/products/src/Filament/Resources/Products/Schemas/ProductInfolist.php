<?php

namespace Webkul\Product\Filament\Resources\Products\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Webkul\Product\Enums\ProductType;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('products::filament/resources/product.infolist.sections.general.entries.name')),

                                TextEntry::make('description')
                                    ->label(__('products::filament/resources/product.infolist.sections.general.entries.description'))
                                    ->html()
                                    ->placeholder('—'),

                                TextEntry::make('tags.name')
                                    ->label(__('products::filament/resources/product.infolist.sections.general.entries.tags'))
                                    ->badge()
                                    ->separator(', ')
                                    ->weight(FontWeight::Bold),
                            ]),

                        Section::make(__('products::filament/resources/product.infolist.sections.images.title'))
                            ->schema([
                                ImageEntry::make('images')
                                    ->hiddenLabel()
                                    ->circular(),
                            ])
                            ->visible(fn ($record): bool => ! empty($record->images)),

                        Section::make(__('products::filament/resources/product.infolist.sections.inventory.title'))
                            ->schema([
                                Section::make(__('products::filament/resources/product.infolist.sections.inventory.fieldsets.logistics.title'))
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('weight')
                                                    ->label(__('products::filament/resources/product.infolist.sections.inventory.fieldsets.logistics.entries.weight'))
                                                    ->placeholder('—')
                                                    ->icon('heroicon-o-scale'),

                                                TextEntry::make('volume')
                                                    ->label(__('products::filament/resources/product.infolist.sections.inventory.fieldsets.logistics.entries.volume'))
                                                    ->placeholder('—')
                                                    ->icon('heroicon-o-beaker'),
                                            ]),
                                    ]),
                            ])
                            ->visible(fn ($record): bool => $record->type == ProductType::GOODS),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('products::filament/resources/product.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('products::filament/resources/product.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-o-calendar'),

                                TextEntry::make('creator.name')
                                    ->label(__('products::filament/resources/product.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-o-user'),

                                TextEntry::make('updated_at')
                                    ->label(__('products::filament/resources/product.infolist.sections.record-information.entries.updated-at'))
                                    ->dateTime()
                                    ->icon('heroicon-o-calendar'),
                            ]),

                        Section::make(__('products::filament/resources/product.infolist.sections.settings.title'))
                            ->schema([
                                TextEntry::make('type')
                                    ->label(__('products::filament/resources/product.infolist.sections.settings.entries.type'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-queue-list'),

                                TextEntry::make('reference')
                                    ->label(__('products::filament/resources/product.infolist.sections.settings.entries.reference'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-identification'),

                                TextEntry::make('barcode')
                                    ->label(__('products::filament/resources/product.infolist.sections.settings.entries.barcode'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-bars-4'),

                                TextEntry::make('category.full_name')
                                    ->label(__('products::filament/resources/product.infolist.sections.settings.entries.category'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-folder'),

                                TextEntry::make('company.name')
                                    ->label(__('products::filament/resources/product.infolist.sections.settings.entries.company'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-building-office'),
                            ]),

                        Section::make(__('products::filament/resources/product.infolist.sections.pricing.title'))
                            ->schema([
                                TextEntry::make('price')
                                    ->label(__('products::filament/resources/product.infolist.sections.pricing.entries.price'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-banknotes'),

                                TextEntry::make('cost')
                                    ->label(__('products::filament/resources/product.infolist.sections.pricing.entries.cost'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-banknotes'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
