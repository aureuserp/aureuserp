<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategories\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StorageCategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/storage-category.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category.infolist.sections.general.entries.name'))
                                    ->icon('heroicon-o-tag'), // Example icon for name
                                TextEntry::make('max_weight')
                                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category.infolist.sections.general.entries.max-weight'))
                                    ->numeric()
                                    ->icon('heroicon-o-scale'), // Example icon for max weight
                                TextEntry::make('allow_new_products')
                                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category.infolist.sections.general.entries.allow-new-products'))
                                    ->icon('heroicon-o-plus-circle'), // Example icon for allow new products
                                TextEntry::make('company.name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category.infolist.sections.general.entries.company'))
                                    ->icon('heroicon-o-building-office'), // Example icon for company
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                \Filament\Schemas\Components\Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/storage-category.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                TextEntry::make('creator.name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-m-user'),

                                TextEntry::make('updated_at')
                                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category.infolist.sections.record-information.entries.last-updated'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
