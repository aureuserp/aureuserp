<?php

namespace Webkul\Product\Filament\Resources\ProductResource\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Filament\Resources\ProductResource\Support\ProductSchemaRegistry as Registry;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        $leftGroup = array_merge(
            [static::generalSection()],
            Registry::renderInfolist('left.general.after'),
            [static::mediaSection()],
            Registry::hasInfolistSlot('left.inventory')
                ? Registry::renderInfolist('left.inventory')
                : [static::inventorySection()],
            Registry::renderInfolist('left.append'),
        );

        $rightGroup = array_merge(
            [static::recordInformationSection(), static::settingsSection(), static::pricingSection()],
            Registry::renderInfolist('right.append'),
        );

        return $schema
            ->components([
                Group::make()
                    ->schema($leftGroup)
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema($rightGroup)
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function generalSection(): Section
    {
        return Section::make()
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
            ]);
    }

    public static function mediaSection(): Section
    {
        return Section::make(__('products::filament/resources/product.infolist.sections.images.title'))
            ->schema([
                ImageEntry::make('images')
                    ->hiddenLabel(),
            ])
            ->visible(fn ($record): bool => ! empty($record->images));
    }

    public static function inventorySection(): Section
    {
        return Section::make(__('products::filament/resources/product.infolist.sections.inventory.title'))
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
            ->visible(fn ($record): bool => $record->type == ProductType::GOODS);
    }

    public static function recordInformationSection(): Section
    {
        return Section::make(__('products::filament/resources/product.infolist.sections.record-information.title'))
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
            ]);
    }

    public static function settingsSection(): Section
    {
        return Section::make(__('products::filament/resources/product.infolist.sections.settings.title'))
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
            ]);
    }

    public static function pricingSection(): Section
    {
        return Section::make(__('products::filament/resources/product.infolist.sections.pricing.title'))
            ->schema([
                TextEntry::make('price')
                    ->label(__('products::filament/resources/product.infolist.sections.pricing.entries.price'))
                    ->placeholder('—')
                    ->money()
                    ->suffix(fn ($record): string => $record->uom ? ' / '.$record->uom->name : ''),

                TextEntry::make('cost')
                    ->label(__('products::filament/resources/product.infolist.sections.pricing.entries.cost'))
                    ->placeholder('—')
                    ->money()
                    ->suffix(fn ($record): string => $record->uomPO ? ' / '.$record->uomPO->name : ''),
            ]);
    }
}
